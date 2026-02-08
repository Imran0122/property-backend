<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        return response()->json(['message' => 'Message sent']);
    }

    public function conversations()
    {
        return response()->json(['message' => 'Conversation list']);
    }

    public function conversation($otherUserId, $propertyId = null)
    {
        return response()->json(['message' => 'Single conversation']);
    }

    public function markAsRead($id)
    {
        return response()->json(['message' => 'Marked as read']);
    }

    public function unreadCount()
    {
        return response()->json(['count' => 0]);
    }
}
