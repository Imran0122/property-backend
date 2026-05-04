<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatusUpdateMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AdminAgentController extends Controller
{
    public function index()
    {
        $agents = User::where('is_agent', 1)->latest()->paginate(20);
        return response()->json(['status' => true, 'data' => $agents]);
    }

    public function pending()
    {
        $agents = User::where('is_agent', 1)->where('status', 'pending')->latest()->paginate(20);
        return response()->json(['status' => true, 'data' => $agents]);
    }

    public function approve($id)
    {
        $agent = User::findOrFail($id);
        $agent->update(['status' => 'active']);

        try {
            Mail::to($agent->email)->send(new StatusUpdateMail(
                'agent account',
                'approved',
                $agent->name,
                $agent->name
            ));
        } catch (\Exception $e) {}

        return response()->json(['status' => true, 'message' => 'Agent approved successfully']);
    }

    public function reject($id)
    {
        $agent = User::findOrFail($id);
        $agent->update(['status' => 'rejected', 'is_agent' => 0]);

        try {
            Mail::to($agent->email)->send(new StatusUpdateMail(
                'agent account',
                'rejected',
                $agent->name,
                $agent->name
            ));
        } catch (\Exception $e) {}

        return response()->json(['status' => true, 'message' => 'Agent rejected']);
    }

    public function destroy($id)
    {
        $agent = User::findOrFail($id);
        $agent->delete();
        return response()->json(['status' => true, 'message' => 'Agent deleted']);
    }
}