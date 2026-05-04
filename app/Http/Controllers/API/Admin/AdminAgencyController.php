<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminSubmissionMail;
use App\Mail\StatusUpdateMail;
use App\Models\Agency;
use Illuminate\Support\Facades\Mail;

class AdminAgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::withCount(['agents', 'properties'])->latest()->paginate(20);
        return response()->json(['status' => true, 'data' => $agencies]);
    }

    public function approve($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->update(['status' => 'approved']);

        // Email to agency owner
        if ($agency->email) {
            try {
                Mail::to($agency->email)->send(new StatusUpdateMail(
                    'agency',
                    'approved',
                    $agency->name,
                    $agency->name
                ));
            } catch (\Exception $e) {}
        }

        return response()->json(['status' => true, 'message' => 'Agency approved successfully']);
    }

    public function reject($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->update(['status' => 'rejected']);

        // Email to agency owner
        if ($agency->email) {
            try {
                Mail::to($agency->email)->send(new StatusUpdateMail(
                    'agency',
                    'rejected',
                    $agency->name,
                    $agency->name
                ));
            } catch (\Exception $e) {}
        }

        return response()->json(['status' => true, 'message' => 'Agency rejected']);
    }

    public function destroy($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->delete();
        return response()->json(['status' => true, 'message' => 'Agency deleted']);
    }
}