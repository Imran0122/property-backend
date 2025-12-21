<?php

namespace App\Http\Controllers;

use App\Models\ProjectUnit;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectUnitController extends Controller
{
    // ✅ Admin: Add units to project
    public function create(Project $project)
    {
        return view('admin.project_units.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'area' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'status' => 'required|in:available,sold'
        ]);

        $data['project_id'] = $project->id;

        ProjectUnit::create($data);

        return redirect()->route('admin.projects.edit',$project)->with('success','Unit added successfully.');
    }

    // ✅ Admin: edit unit
    public function edit(Project $project, ProjectUnit $unit)
    {
        return view('admin.project_units.edit', compact('project','unit'));
    }

    public function update(Request $request, Project $project, ProjectUnit $unit)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'nullable|string',
            'bedrooms' => 'nullable|integer',
            'bathrooms' => 'nullable|integer',
            'area' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'status' => 'required|in:available,sold'
        ]);

        $unit->update($data);

        return redirect()->route('admin.projects.edit',$project)->with('success','Unit updated successfully.');
    }

    public function destroy(Project $project, ProjectUnit $unit)
    {
        $unit->delete();
        return back()->with('success','Unit deleted successfully.');
    }
}
