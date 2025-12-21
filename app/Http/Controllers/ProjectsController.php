<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectsController extends Controller
{
    // ✅ List all projects
    public function index()
    {
        $projects = Project::with('city')->latest()->paginate(12);
        return view('projects.index', compact('projects'));
    }

    // ✅ Show single project (with units)
    public function show(Project $project)
    {
        $project->load(['city','units']);
        $related = Project::where('city_id', $project->city_id)
                          ->where('id','!=',$project->id)
                          ->take(3)->get();

        return view('projects.show', compact('project','related'));
    }

    // ✅ Admin: create form
    public function create()
    {
        $cities = City::all();
        return view('admin.projects.create', compact('cities'));
    }

    // ✅ Admin: store project
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'location' => 'nullable|string',
            'developer' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:ongoing,completed',
            'cover_image' => 'nullable|image|max:2048'
        ]);

        $data['slug'] = Str::slug($data['title']);

        if($request->hasFile('cover_image')){
            $data['cover_image'] = $request->file('cover_image')->store('projects','public');
        }

        Project::create($data);

        return redirect()->route('admin.projects.index')->with('success','Project created successfully.');
    }

    // ✅ Admin: edit form
    public function edit(Project $project)
    {
        $cities = City::all();
        return view('admin.projects.edit', compact('project','cities'));
    }

    // ✅ Admin: update project
    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'location' => 'nullable|string',
            'developer' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:ongoing,completed',
            'cover_image' => 'nullable|image|max:2048'
        ]);

        $data['slug'] = Str::slug($data['title']);

        if($request->hasFile('cover_image')){
            $data['cover_image'] = $request->file('cover_image')->store('projects','public');
        }

        $project->update($data);

        return redirect()->route('admin.projects.index')->with('success','Project updated successfully.');
    }

    // ✅ Admin: delete project
    public function destroy(Project $project)
    {
        $project->delete();
        return back()->with('success','Project deleted successfully.');
    }
}
