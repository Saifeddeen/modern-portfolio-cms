<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::orderBy('created_at', 'desc')->get();
        return Inertia::render('Messages/Index', ['messages' => $messages]);
    }

    public function markAsRead(Message $message)
    {
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
        }

        return back()->with('success', 'Message marked as read.');
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return back()->with('success', 'Message deleted successfully.');
    }
}
