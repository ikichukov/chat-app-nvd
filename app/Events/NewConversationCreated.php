<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewConversationCreated extends Event implements ShouldBroadcast
{
    use SerializesModels;
    public $id, $conversation1, $conversation2, $user1, $user2;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id, $conversation1, $conversation2, $user1, $user2)
    {
        $this->id = $id;
        $this->conversation1 = $conversation1;
        $this->conversation2 = $conversation2;
        $this->user1 = $user1;
        $this->user2 = $user2;
    }

    public function broadcastAs()
    {
        return 'ConversationCreated';
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['conversation-created'];
    }
}
