<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;

class AdminAgencyController extends Controller
{
    public function index(Request $request)
    {
        $query = Agency::withCount(['agents', 'properties']);

        if ($request->search) {
            $q = $request->search;
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('city', 'like', "%{$q}%");
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $agencies = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data'    => $agencies->items(),
            'meta'    => [
                'total'        => $agencies->total(),
                'current_page' => $agencies->currentPage(),
                'last_page'    => $agencies->lastPage(),
            ],
        ]);
    }

    public function show($id)
    {
        $agency = Agency::withCount(['agents', 'properties'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $agency]);
    }

    public function approve($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->update(['status' => 'active']);
        return response()->json(['success' => true, 'message' => 'Agency approved successfully.']);
    }

    public function reject($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->update(['status' => 'rejected']);
        return response()->json(['success' => true, 'message' => 'Agency rejected.']);
    }

    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->delete();
        return response()->json(['success' => true, 'message' => 'Agency deleted.']);
    }
}