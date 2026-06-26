<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class CandidateController extends Controller
{
    use LogsActivity;

    private function admin()
    {
        return Auth::guard('admin')->user();
    }

    public function index()
    {
        $candidates = Candidate::with('position')->orderBy('name')->get();
        $positions  = Position::orderBy('name')->get();
        return view('admin.candidates', compact('candidates', 'positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'party_list'  => ['nullable', 'string', 'max:255'],
            'position_id' => ['required', 'exists:positions,id'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/candidates'), $filename);
            $data['image'] = $filename;
        }

        $candidate = Candidate::create($data);
        $position  = Position::find($data['position_id']);

        $admin = $this->admin();
        $this->auditLog($request, 'candidate_added', 'admin', $admin->id, $admin->name, [
            'candidate_id'   => $candidate->id,
            'candidate_name' => $candidate->name,
            'position'       => $position->name ?? '',
        ]);

        return response()->json(['success' => true, 'id' => $candidate->id]);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'party_list'  => ['nullable', 'string', 'max:255'],
            'position_id' => ['required', 'exists:positions,id'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($candidate->image) {
                $old = public_path('images/candidates/' . $candidate->image);
                if (file_exists($old)) unlink($old);
            }
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/candidates'), $filename);
            $data['image'] = $filename;
        }

        $old = $candidate->only(['name', 'party_list', 'position_id']);
        $candidate->update($data);

        $admin = $this->admin();
        $this->auditLog($request, 'candidate_updated', 'admin', $admin->id, $admin->name, [
            'candidate_id' => $candidate->id,
            'before'       => $old,
            'after'        => Arr::except($data, ['image']),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Candidate $candidate)
    {
        $admin = $this->admin();
        $this->auditLog(request(), 'candidate_deleted', 'admin', $admin->id, $admin->name, [
            'candidate_id'   => $candidate->id,
            'candidate_name' => $candidate->name,
        ]);

        if ($candidate->image) {
            $path = public_path('images/candidates/' . $candidate->image);
            if (file_exists($path)) unlink($path);
        }
        $candidate->delete();
        return response()->json(['success' => true]);
    }
}
