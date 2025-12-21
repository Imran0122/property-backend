<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function trending()
    {
        $projects = Project::with('city')
            ->withCount('units')
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->latest()
            ->limit(6)
            ->get();

        return response()->json(
            $projects->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'city' => optional($p->city)->name,
                'location' => $p->location,
                'cover_image' => asset('storage/' . $p->cover_image),
                'price_range' => $this->priceRange($p->id),
                'unit_types' => $this->unitTypes($p->id),
            ])
        );
    }

    private function priceRange($projectId)
    {
        $range = DB::table('project_units')
            ->where('project_id', $projectId)
            ->selectRaw('MIN(price) as min, MAX(price) as max')
            ->first();

        if (!$range || !$range->min) return null;

        return 'PKR ' . number_format($range->min) . ' to ' . number_format($range->max);
    }

    private function unitTypes($projectId)
    {
        return DB::table('project_units')
            ->where('project_id', $projectId)
            ->pluck('type')
            ->unique()
            ->values();
    }
    public function index()
{
    $projects = Project::all();
    return response()->json($projects);
}

}
