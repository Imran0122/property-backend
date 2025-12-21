<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentMessage;
use App\Models\Property;

class AgentMessageController extends Controller
{
    /**
     * Store agent message from property detail page
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'required|string|max:20',
            'message'     => 'nullable|string|max:1000',
        ]);

        AgentMessage::create($request->all());

        return response()->json([
            'status'  => true,
            'message' => 'Thanks! Your message has been sent successfully.',
        ]);
    }

    /**
     * Show all agent messages in admin panel
     */
    public function index()
    {
        $messages = AgentMessage::with('property')->latest()->paginate(10);

        return view('admin.agent_messages.index', compact('messages'));
    }

    /**
     * View a single agent message (optional)
     */
    public function show($id)
    {
        $message = AgentMessage::with('property')->findOrFail($id);

        return view('admin.agent_messages.show', compact('message'));
    }

    /**
     * Delete agent message (optional)
     */
    public function destroy($id)
    {
        $message = AgentMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.agent-messages.index')
            ->with('success', 'Message deleted successfully');
    }
}
