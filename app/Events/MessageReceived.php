<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageReceived extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $username, $message, $conversation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($username, $message, $conversation)
    {
        $this->username = $username;
        $this->message = $message;
        $this->conversation = $conversation;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['message-received'];
    }

    public function broadcastAs()
    {
        return 'MessageReceived';
    }
}
