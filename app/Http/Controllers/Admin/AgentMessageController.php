<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentMessage;
use App\Models\AgentReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AgentMessageController extends Controller
{
    public function index()
    {
        $messages = AgentMessage::with('property')->latest()->paginate(10);
        return view('admin.agent_messages.index', compact('messages'));
    }

    public function show($id)
    {
        $message = AgentMessage::with('property', 'replies')->findOrFail($id);

        // ✅ Mark as read
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }

        return view('admin.agent_messages.show', compact('message'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string|max:2000',
        ]);

        $message = AgentMessage::findOrFail($id);

        // Save reply in DB
        AgentReply::create([
            'agent_message_id' => $message->id,
            'reply_message'   => $request->reply_message,
        ]);

        // Send email
        Mail::raw($request->reply_message, function ($mail) use ($message) {
            $mail->to($message->email)
                ->subject('Reply to your inquiry about: ' . ($message->property->title ?? 'Property'));
        });

        return redirect()->route('admin.agent-messages.show', $id)
            ->with('success', 'Reply sent successfully and saved to history.');
    }

    public function destroy($id)
    {
        AgentMessage::findOrFail($id)->delete();
        return redirect()->route('admin.agent-messages.index')
            ->with('success', 'Message deleted successfully.');
    }
}

