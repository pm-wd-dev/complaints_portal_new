<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use Illuminate\Http\Request;

class StageController extends Controller
{
    public function index()
    {
        $stages = Stage::ordered()->get();
        return view('admin.stages.index', compact('stages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'step_number' => 'required|integer|min:1|unique:stages,step_number',
            'color' => 'required|string|max:7', // Hex color code
        ]);

        Stage::create($validated);

        return redirect()->back()->with('success', 'Stage created successfully.');
    }

    public function update(Request $request, Stage $stage)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'step_number' => 'required|integer|min:1|unique:stages,step_number,' . $stage->id,
            'color' => 'required|string|max:7',
        ]);

        $stage->update($validated);

        return redirect()->back()->with('success', 'Stage updated successfully.');
    }

    public function toggle(Stage $stage)
    {
        $stage->update(['is_active' => !$stage->is_active]);
        return redirect()->back()->with('success', 'Stage status updated successfully.');
    }

    public function destroy(Stage $stage)
    {
        // Check if stage is being used by any complaints
        if ($stage->complaints()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete stage that is in use by complaints.');
        }
        $stage->delete();

        return redirect()->back()->with('success', 'Stage deleted successfully.');
    }
}
