<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Message;
use App\Events\MessageReceived;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $message = Message::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        // Trigger Real-time Event
        broadcast(new MessageReceived($message));

        return $this->successResponse(
            data: null,
            message: 'Your message has been sent successfully!'
        );
    }
}
