<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('name')->get();
        return view('admin.positions', compact('positions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'max_votes' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $position = Position::create($data);
        return response()->json(['success' => true, 'id' => $position->id]);
    }

    public function update(Request $request, Position $position)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'max_votes' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $position->update($data);
        return response()->json(['success' => true]);
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return response()->json(['success' => true]);
    }
}
