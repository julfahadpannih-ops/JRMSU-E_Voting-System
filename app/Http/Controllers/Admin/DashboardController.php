<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Setting;
use App\Models\Vote;
use App\Models\Voter;

class DashboardController extends Controller
{
    public function index()
    {
        $totalApproved = Voter::where('is_approved', true)->count();
        $votedCount    = Voter::where('has_voted', true)->count();

        $stats = [
            'total_voters'    => $totalApproved,
            'pending_voters'  => Voter::where('is_approved', false)->count(),
            'voted_count'     => $votedCount,
            'total_candidates'=> Candidate::count(),
            'total_positions' => Position::count(),
            'total_votes'     => Vote::count(),
            'turnout_percent' => $totalApproved > 0
                ? round(($votedCount / $totalApproved) * 100, 1)
                : 0,
        ];

        $start = Setting::getValue('start_time');
        $end   = Setting::getValue('end_time');

        return view('admin.dashboard', compact('stats', 'start', 'end'));
    }
}
