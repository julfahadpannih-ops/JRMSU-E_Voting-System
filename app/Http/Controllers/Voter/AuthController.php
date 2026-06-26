<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use LogsActivity;

    public function showLogin()
    {
        if (Auth::guard('voter')->check()) {
            return redirect()->route('voter.dashboard');
        }
        return view('auth.voter-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'string'],
            'password'   => ['required', 'string', 'min:6'],
        ]);

        $voter = Voter::where('student_id', $request->student_id)->first();

        if (! $voter) {
            $this->auditLog($request, 'voter_login_failed', 'voter', null, $request->student_id, [
                'reason' => 'Student ID not found',
            ]);
            return back()->withErrors(['student_id' => 'Student ID not found in the official voter list.'])->withInput();
        }

        if (! $voter->is_approved) {
            $this->auditLog($request, 'voter_login_failed', 'voter', $voter->id, $voter->name, [
                'reason' => 'Account not yet approved',
            ]);
            return back()->withErrors(['student_id' => 'Your registration is still pending admin approval.'])->withInput();
        }

        // First-time login: set password
        if ($voter->password === null) {
            $voter->update(['password' => Hash::make($request->password)]);
            Auth::guard('voter')->login($voter);
            $request->session()->regenerate();
            $this->auditLog($request, 'voter_first_login', 'voter', $voter->id, $voter->name);
            return redirect()->route('voter.dashboard');
        }

        if (! Hash::check($request->password, $voter->password)) {
            $this->auditLog($request, 'voter_login_failed', 'voter', $voter->id, $voter->name, [
                'reason' => 'Wrong password',
            ]);
            return back()->withErrors(['password' => 'Invalid password.'])->withInput();
        }

        Auth::guard('voter')->login($voter);
        $request->session()->regenerate();
        $this->auditLog($request, 'voter_login', 'voter', $voter->id, $voter->name);
        return redirect()->route('voter.dashboard');
    }

    public function logout(Request $request)
    {
        $voter = Auth::guard('voter')->user();
        if ($voter) {
            $this->auditLog($request, 'voter_logout', 'voter', $voter->id, $voter->name);
        }
        Auth::guard('voter')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('voter.login');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'unique:voters,student_id'],
            'name'       => ['required', 'string', 'max:255'],
            'course'     => ['required', 'string', 'max:255'],
        ]);

        $voter = Voter::create(array_merge($data, ['is_approved' => false]));
        $this->auditLog($request, 'voter_registered', 'voter', $voter->id, $voter->name, [
            'student_id' => $voter->student_id,
            'course'     => $voter->course,
        ]);

        return back()->with('success', 'Registration submitted. Please wait for admin approval before voting.');
    }
}
