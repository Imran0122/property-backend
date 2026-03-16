<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * POST /api/messages/send
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'property_id' => 'nullable|exists:properties,id',
            'body' => 'required|string',
            'type' => 'nullable|string|max:50',
        ]);

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $data['receiver_id'],
            'property_id' => $data['property_id'] ?? null,
            'body' => $data['body'],
            'type' => $data['type'] ?? 'message',
            'is_read' => false,
            'is_trashed_by_receiver' => false,
            'trashed_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ]);
    }

    /**
     * GET /api/inbox
     */
    public function inbox(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 15);

        $query = Message::with([
                'sender:id,name,email',
                'property:id,title'
            ])
            ->where('receiver_id', $userId)
            ->where('is_trashed_by_receiver', false)
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('body', 'like', "%{$search}%");
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', filter_var($request->is_read, FILTER_VALIDATE_BOOLEAN));
        }

        $messages = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $messages->getCollection()->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'type' => $message->type,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at,
                    'sender' => $message->sender ? [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'email' => $message->sender->email,
                    ] : null,
                    'property' => $message->property ? [
                        'id' => $message->property->id,
                        'title' => $message->property->title,
                    ] : null,
                ];
            }),
            'meta' => [
                'total' => $messages->total(),
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
            ]
        ]);
    }

    /**
     * GET /api/inbox/trash
     */
    public function trash(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 15);

        $messages = Message::with([
                'sender:id,name,email',
                'property:id,title'
            ])
            ->where('receiver_id', $userId)
            ->where('is_trashed_by_receiver', true)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $messages->getCollection()->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'type' => $message->type,
                    'is_read' => $message->is_read,
                    'trashed_at' => $message->trashed_at,
                    'created_at' => $message->created_at,
                    'sender' => $message->sender ? [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'email' => $message->sender->email,
                    ] : null,
                    'property' => $message->property ? [
                        'id' => $message->property->id,
                        'title' => $message->property->title,
                    ] : null,
                ];
            }),
            'meta' => [
                'total' => $messages->total(),
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
            ]
        ]);
    }

    /**
     * GET /api/inbox/stats
     */
    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'status' => true,
            'stats' => [
                'inbox' => Message::where('receiver_id', $userId)
                    ->where('is_trashed_by_receiver', false)
                    ->count(),

                'trash' => Message::where('receiver_id', $userId)
                    ->where('is_trashed_by_receiver', true)
                    ->count(),

                'unread' => Message::where('receiver_id', $userId)
                    ->where('is_trashed_by_receiver', false)
                    ->where('is_read', false)
                    ->count(),
            ]
        ]);
    }

    /**
     * PATCH /api/inbox/{id}/read
     * (existing route messages/{id}/read bhi isko hit kar sakta hai)
     */
    public function markAsRead(Request $request, $id)
    {
        $message = Message::where('id', $id)
            ->where('receiver_id', $request->user()->id)
            ->first();

        if (!$message) {
            return response()->json([
                'status' => false,
                'message' => 'Message not found'
            ], 404);
        }

        $message->update([
            'is_read' => true
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Marked as read'
        ]);
    }

    /**
     * PATCH /api/inbox/{id}/trash
     */
    public function moveToTrash(Request $request, $id)
    {
        $message = Message::where('id', $id)
            ->where('receiver_id', $request->user()->id)
            ->first();

        if (!$message) {
            return response()->json([
                'status' => false,
                'message' => 'Message not found'
            ], 404);
        }

        $message->update([
            'is_trashed_by_receiver' => true,
            'trashed_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message moved to trash'
        ]);
    }

    /**
     * PATCH /api/inbox/{id}/restore
     */
    public function restore(Request $request, $id)
    {
        $message = Message::where('id', $id)
            ->where('receiver_id', $request->user()->id)
            ->first();

        if (!$message) {
            return response()->json([
                'status' => false,
                'message' => 'Message not found'
            ], 404);
        }

        $message->update([
            'is_trashed_by_receiver' => false,
            'trashed_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message restored successfully'
        ]);
    }

    /**
     * GET /api/messages/conversations
     * existing route compatibility
     */
    public function conversations(Request $request)
    {
        $userId = $request->user()->id;

        $messages = Message::with(['sender:id,name', 'receiver:id,name', 'property:id,title'])
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($userId) {
                $otherUserId = $message->sender_id == $userId
                    ? $message->receiver_id
                    : $message->sender_id;

                return $otherUserId . '-' . ($message->property_id ?? 0);
            })
            ->map(function ($group) use ($userId) {
                $latest = $group->first();
                $otherUser = $latest->sender_id == $userId ? $latest->receiver : $latest->sender;

                return [
                    'other_user' => $otherUser ? [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                    ] : null,
                    'property' => $latest->property ? [
                        'id' => $latest->property->id,
                        'title' => $latest->property->title,
                    ] : null,
                    'last_message' => $latest->body,
                    'last_message_at' => $latest->created_at,
                ];
            })
            ->values();

        return response()->json([
            'status' => true,
            'data' => $messages
        ]);
    }

    /**
     * GET /api/messages/conversation/{otherUserId}/{propertyId?}
     */
    public function conversation(Request $request, $otherUserId, $propertyId = null)
    {
        $userId = $request->user()->id;

        $query = Message::with(['sender:id,name', 'receiver:id,name'])
            ->where(function ($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $otherUserId)
                  ->where('receiver_id', $userId);
            });

        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'status' => true,
            'data' => $messages
        ]);
    }

    /**
     * GET /api/messages/unread-count
     */
    public function unreadCount(Request $request)
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->where('is_trashed_by_receiver', false)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => true,
            'count' => $count
        ]);
    }

    
}