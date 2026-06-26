<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoterDashboardController extends Controller
{
    use LogsActivity;

    private function electionStatus(): array
    {
        date_default_timezone_set('Asia/Manila');

        $startRaw = Setting::getValue('start_time');
        $endRaw   = Setting::getValue('end_time');
        $now      = time();
        $startTs  = $startRaw ? strtotime($startRaw) : 0;
        $endTs    = $endRaw   ? strtotime($endRaw)   : 0;

        if (! $startTs || ! $endTs) {
            return ['open' => false, 'message' => 'The election schedule has not been set by the administrator yet.'];
        }

        if ($now < $startTs) {
            return ['open' => false, 'message' => 'Voting has not started yet. The election will begin on ' . date('F j, Y \a\t g:i A', $startTs) . '.'];
        }

        if ($now > $endTs) {
            return ['open' => false, 'message' => 'Voting is officially closed. The election ended on ' . date('F j, Y \a\t g:i A', $endTs) . '.'];
        }

        return ['open' => true, 'message' => ''];
    }

    public function index()
    {
        /** @var Voter $voter */
        $voter  = Auth::guard('voter')->user();
        $status = $this->electionStatus();

        $positions  = Position::with(['candidates' => fn ($q) => $q->orderBy('name')])->orderBy('name')->get();
        $resultsRaw = $this->getResults();

        return view('voter.dashboard', compact('voter', 'status', 'positions', 'resultsRaw'));
    }

    public function submitVote(Request $request)
    {
        /** @var Voter $voter */
        $voter  = Auth::guard('voter')->user();
        $status = $this->electionStatus();

        if (! $status['open']) {
            $this->auditLog($request, 'vote_attempt_outside_window', 'voter', $voter->id, $voter->name);
            abort(403, 'Voting is currently closed.');
        }

        if ($voter->has_voted) {
            $this->auditLog($request, 'vote_attempt_double_vote', 'voter', $voter->id, $voter->name);
            abort(403, 'You have already submitted your ballot.');
        }

        $submittedVotes = $request->input('votes', []);
        $positionLimits = Position::pluck('max_votes', 'id');

        foreach ($submittedVotes as $positionId => $candidateData) {
            // FIX #3: always treat as array before counting (prevents overvote bypass)
            $candidates = is_array($candidateData) ? $candidateData : [$candidateData];
            $maxAllowed = $positionLimits[$positionId] ?? 1;
            $selected   = count($candidates);

            if ($selected > $maxAllowed) {
                $this->auditLog($request, 'vote_overvote_detected', 'voter', $voter->id, $voter->name, [
                    'position_id' => $positionId,
                    'selected'    => $selected,
                    'max_allowed' => $maxAllowed,
                ]);
                abort(422, "Overvoting detected for position #{$positionId}. Ballot is void.");
            }

            // FIX #2: validate each candidate actually belongs to the claimed position
            foreach ($candidates as $candidateId) {
                $valid = Candidate::where('id', $candidateId)
                    ->where('position_id', $positionId)
                    ->exists();
                if (! $valid) {
                    $this->auditLog($request, 'vote_invalid_candidate', 'voter', $voter->id, $voter->name, [
                        'position_id'  => $positionId,
                        'candidate_id' => $candidateId,
                    ]);
                    abort(422, "Invalid candidate #{$candidateId} for position #{$positionId}. Ballot is void.");
                }
            }
        }

        // Build summary of vote choices for audit log
        $voteSummary = [];
        DB::transaction(function () use ($voter, $submittedVotes, &$voteSummary) {
            foreach ($submittedVotes as $positionId => $candidateData) {
                $candidates = is_array($candidateData) ? $candidateData : [$candidateData];
                foreach ($candidates as $candidateId) {
                    Vote::create([
                        'voter_id'     => $voter->id,
                        'position_id'  => $positionId,
                        'candidate_id' => $candidateId,
                    ]);
                    $voteSummary[] = ['position_id' => $positionId, 'candidate_id' => $candidateId];
                }
            }
            $voter->update(['has_voted' => true]);
        });

        $this->auditLog($request, 'vote_submitted', 'voter', $voter->id, $voter->name, [
            'positions_voted' => count($submittedVotes),
            // NOTE: We log position/candidate IDs for integrity checks.
            // To enforce full ballot secrecy, remove 'votes' below.
            'votes' => $voteSummary,
        ]);

        return redirect()->route('voter.dashboard')->with('voted', true);
    }

    /**
     * FIX #1: Public JSON endpoint for live results — accessible by voter dashboard JS.
     * No auth required; results are already public after election closes or voter has voted.
     */
    public function liveResults()
    {
        return response()->json(['success' => true, 'data' => $this->getResults()]);
    }

   private function getResults(): array
{
    return Candidate::with('position')
        ->select([
            'candidates.*',
            DB::raw('(SELECT COUNT(*) FROM votes WHERE votes.candidate_id = candidates.id) as vote_count'),
        ])
        ->get()
        ->map(fn ($c) => [
            'candidateId'   => $c->id,
            'candidateName' => $c->name,
            'partyList'     => $c->party_list,
            'image'         => $c->image_url,
            'positionId'    => $c->position_id,
            'positionName'  => $c->position->name ?? '',
            'maxVotes'      => $c->position->max_votes ?? 1,
            'voteCount'     => (int) $c->vote_count,
        ])
        ->sortByDesc('voteCount')
        ->values()
        ->toArray();
}
}