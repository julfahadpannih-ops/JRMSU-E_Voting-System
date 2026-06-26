<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoterController extends Controller
{
    use LogsActivity;

    private function admin()
    {
        return Auth::guard('admin')->user();
    }

    public function index()
    {
        $voters = Voter::orderBy('name')->get();
        return view('admin.voters', compact('voters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'unique:voters,student_id'],
            'name'       => ['required', 'string', 'max:255'],
            'course'     => ['required', 'string', 'max:255'],
        ]);

        $voter = Voter::create(array_merge($data, ['is_approved' => true]));

        $admin = $this->admin();
        $this->auditLog($request, 'voter_added', 'admin', $admin->id, $admin->name, [
            'voter_id'   => $voter->id,
            'voter_name' => $voter->name,
            'student_id' => $voter->student_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Voter added successfully.']);
    }

    public function update(Request $request, Voter $voter)
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'unique:voters,student_id,' . $voter->id],
            'name'       => ['required', 'string', 'max:255'],
            'course'     => ['required', 'string', 'max:255'],
        ]);

        $old = $voter->only(['student_id', 'name', 'course']);
        $voter->update($data);

        $admin = $this->admin();
        $this->auditLog($request, 'voter_updated', 'admin', $admin->id, $admin->name, [
            'voter_id' => $voter->id,
            'before'   => $old,
            'after'    => $data,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Voter $voter)
    {
        $request = request();
        $admin   = $this->admin();
        $this->auditLog($request, 'voter_deleted', 'admin', $admin->id, $admin->name, [
            'voter_id'   => $voter->id,
            'voter_name' => $voter->name,
            'student_id' => $voter->student_id,
        ]);

        $voter->delete();
        return response()->json(['success' => true]);
    }

    public function approve(Voter $voter)
    {
        $voter->update(['is_approved' => true]);

        $admin = $this->admin();
        $this->auditLog(request(), 'voter_approved', 'admin', $admin->id, $admin->name, [
            'voter_id'   => $voter->id,
            'voter_name' => $voter->name,
            'student_id' => $voter->student_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function reject(Voter $voter)
    {
        $admin = $this->admin();
        $this->auditLog(request(), 'voter_rejected', 'admin', $admin->id, $admin->name, [
            'voter_id'   => $voter->id,
            'voter_name' => $voter->name,
            'student_id' => $voter->student_id,
        ]);

        $voter->delete();
        return response()->json(['success' => true, 'message' => 'Registration rejected and removed.']);
    }
}
