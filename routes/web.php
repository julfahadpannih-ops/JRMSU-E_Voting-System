<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Voter;
use App\Http\Middleware\AuthenticateAdmin;
use App\Http\Middleware\AuthenticateVoter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Public
    Route::get('/login',        [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',       [Admin\AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');

    // Protected
    Route::middleware(AuthenticateAdmin::class)->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Voters
        Route::get('/voters',              [Admin\VoterController::class, 'index'])->name('voters.index');
        Route::post('/voters',             [Admin\VoterController::class, 'store'])->name('voters.store');
        Route::put('/voters/{voter}',      [Admin\VoterController::class, 'update'])->name('voters.update');
        Route::delete('/voters/{voter}',   [Admin\VoterController::class, 'destroy'])->name('voters.destroy');
        Route::patch('/voters/{voter}/approve', [Admin\VoterController::class, 'approve'])->name('voters.approve');
        Route::delete('/voters/{voter}/reject', [Admin\VoterController::class, 'reject'])->name('voters.reject');

        // Positions
        Route::get('/positions',             [Admin\PositionController::class, 'index'])->name('positions.index');
        Route::post('/positions',            [Admin\PositionController::class, 'store'])->name('positions.store');
        Route::put('/positions/{position}',  [Admin\PositionController::class, 'update'])->name('positions.update');
        Route::delete('/positions/{position}', [Admin\PositionController::class, 'destroy'])->name('positions.destroy');

        // Candidates
        Route::get('/candidates',              [Admin\CandidateController::class, 'index'])->name('candidates.index');
        Route::post('/candidates',             [Admin\CandidateController::class, 'store'])->name('candidates.store');
        Route::post('/candidates/{candidate}', [Admin\CandidateController::class, 'update'])->name('candidates.update'); // POST for file upload support
        Route::delete('/candidates/{candidate}', [Admin\CandidateController::class, 'destroy'])->name('candidates.destroy');

        // Results & Reset
        Route::get('/results',     [Admin\ResultController::class, 'index'])->name('results.index');
        Route::post('/reset-votes',[Admin\ResultController::class, 'resetVotes'])->name('results.reset');

        // Settings
        Route::get('/settings',    [Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings',   [Admin\SettingController::class, 'update'])->name('settings.update');

        // Export
        Route::get('/results/export/csv',  [Admin\ResultController::class, 'exportCsv'])->name('results.export.csv');
        Route::get('/results/export/pdf',  [Admin\ResultController::class, 'exportPdfView'])->name('results.export.pdf');

        // Audit Logs
        Route::get('/audit-logs',  [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});

/*
|--------------------------------------------------------------------------
| Voter Routes
|--------------------------------------------------------------------------
*/
Route::prefix('voter')->name('voter.')->group(function () {

    // Public
    Route::get('/login',    [Voter\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [Voter\AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::post('/register',[Voter\AuthController::class, 'register'])->name('register')->middleware('throttle:10,1');

    // Public: live results JSON (used by voter dashboard after voting)
    Route::get('/results', [Voter\VoterDashboardController::class, 'liveResults'])->name('results');

    // Protected
    Route::middleware(AuthenticateVoter::class)->group(function () {
        Route::post('/logout',    [Voter\AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard',  [Voter\VoterDashboardController::class, 'index'])->name('dashboard');
        Route::post('/vote',      [Voter\VoterDashboardController::class, 'submitVote'])->name('vote');
    });
});

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/ping', function () {
    return response('OK', 200);
});
