<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    // Send a message or inquiry
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string',
            'property_id' => 'nullable|exists:properties,id',
            'type' => 'nullable|string'
        ]);

        $user = $request->user();

        // create message
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'property_id' => $request->property_id,
            'body' => $request->body,
            'type' => $request->type ?? 'message',
            'is_read' => false
        ]);

        // (Optional) Fire event/notification to receiver here

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message->load('sender','property')
        ], 201);
    }

    // Conversations summary (inbox): last message per conversation (conversation = [other_user, property_id])
    public function conversations(Request $request)
    {
        $userId = $request->user()->id;

        // Build conversation key: other_user + property_id (property_id can be null)
        $sub = DB::table('messages')
            ->select(
                DB::raw("CASE WHEN sender_id = {$userId} THEN receiver_id ELSE sender_id END as other_user_id"),
                'property_id',
                DB::raw('MAX(created_at) as last_message_at')
            )
            ->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->groupBy('other_user_id','property_id');

        // join back to get last message row for each conversation
        $convos = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub)
            ->join('messages', function($join) use ($userId) {
                $join->on(function($j) {
                    $j->on('messages.property_id', '=', 'sub.property_id')
                      ->orOn(function($jj){
                          $jj->whereNull('messages.property_id')->whereNull('sub.property_id');
                      });
                });
                $join->on('messages.created_at', '=', 'sub.last_message_at');
                $join->on(function($j) use ($userId) {
                    $j->on(DB::raw("CASE WHEN messages.sender_id = {$userId} THEN messages.receiver_id ELSE messages.sender_id END"), '=', 'sub.other_user_id');
                });
            })
            ->select('messages.*', 'sub.other_user_id', 'sub.property_id')
            ->orderBy('messages.created_at', 'desc')
            ->get();

        // Attach user and property info
        $result = $convos->map(function($m) {
            $sender = \App\Models\User::find($m->sender_id);
            $receiver = \App\Models\User::find($m->receiver_id);
            $property = $m->property_id ? Property::find($m->property_id) : null;
            $otherUserId = $m->other_user_id;
            $otherUser = User::find($otherUserId);

            return [
                'conversation_with' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'avatar' => $otherUser->avatar ?? null,
                ],
                'property' => $property ? ['id'=>$property->id, 'title'=>$property->title] : null,
                'last_message' => [
                    'id' => $m->id,
                    'body' => $m->body,
                    'sender_id' => $m->sender_id,
                    'is_read' => $m->is_read,
                    'created_at' => $m->created_at,
                ],
            ];
        });

        return response()->json($result);
    }

    // Get messages in a conversation with another user; optionally filter by property_id
    public function conversation(Request $request, $otherUserId, $propertyId = null)
    {
        $userId = $request->user()->id;

        $query = Message::where(function($q) use ($userId, $otherUserId, $propertyId) {
            $q->where(function($a) use ($userId, $otherUserId) {
                $a->where('sender_id', $userId)->where('receiver_id', $otherUserId);
            })->orWhere(function($b) use ($userId, $otherUserId) {
                $b->where('sender_id', $otherUserId)->where('receiver_id', $userId);
            });

            if ($propertyId !== null) {
                $q->where('property_id', $propertyId);
            } else {
                $q->whereNull('property_id');
            }
        });

        $messages = $query->with('sender','receiver')
                          ->orderBy('created_at','desc')
                          ->paginate(20);

        return response()->json($messages);
    }

    // Mark a single message as read
    public function markAsRead(Request $request, $id)
    {
        $userId = $request->user()->id;

        $message = Message::where('id', $id)
            ->where('receiver_id', $userId)
            ->firstOrFail();

        $message->is_read = true;
        $message->save();

        return response()->json(['message' => 'Marked as read']);
    }

    // Unread count quick endpoint
    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;
        $count = Message::where('receiver_id', $userId)->where('is_read', false)->count();

        return response()->json(['unread' => $count]);
    }
}
