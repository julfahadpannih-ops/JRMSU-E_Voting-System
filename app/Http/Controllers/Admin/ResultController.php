<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Candidate;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $results = $this->getResults();

        // JS (apiFetch) sends Accept: application/json — return JSON for AJAX calls
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $results]);
        }

        return view('admin.results', compact('results'));
    }

    public function resetVotes(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        DB::transaction(function () {
            Vote::query()->delete();
            Voter::query()->update(['has_voted' => false]);
        });

        $this->auditLog($request, 'votes_reset', 'admin', $admin->id, $admin->name, [
            'note' => 'All votes wiped and has_voted flags cleared.',
        ]);

        return response()->json(['success' => true, 'message' => 'All votes reset successfully.']);
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

    public function exportCsv()
    {
        $results = $this->getResults();

        // Group by position
        $byPosition = collect($results)->groupBy('positionName');

        $rows   = [];
        $rows[] = ['Position', 'Candidate', 'Party List', 'Votes'];

        foreach ($byPosition as $positionName => $candidates) {
            foreach ($candidates as $c) {
                $rows[] = [
                    $positionName,
                    $c['candidateName'],
                    $c['partyList'] ?? '—',
                    $c['voteCount'],
                ];
            }
        }

        $filename = 'election-results-' . date('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdfView()
    {
        $results    = collect($this->getResults())->groupBy('positionName');
        $exportedAt = now()->timezone('Asia/Manila')->format('F j, Y g:i A');
        return view('admin.results-pdf', compact('results', 'exportedAt'));
    }

}