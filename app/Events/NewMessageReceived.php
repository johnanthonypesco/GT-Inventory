<?php

namespace App\Events;

use App\Models\Conversation; // <-- Import the Conversation model
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The conversation instance.
     *
     * @var \App\Models\Conversation
     */
    public $conversation; // <-- Make it public

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation) // <-- Accept the model
    {
        $this->conversation = $conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}