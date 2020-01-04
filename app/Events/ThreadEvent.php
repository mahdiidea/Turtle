<?php

namespace App\Events;

use App\Thread;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ThreadEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $auth, $thread;

    /**
     * Create a new event instance.
     *
     * @param User $auth
     * @param Thread $thread
     */
    public function __construct($auth, $thread)
    {
        $this->auth = $auth;
        $this->thread = $thread;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messengers.' . $this->auth->id);
    }

    /**
     * @return array
     */
    public function broadcastWith()
    {
        return ['thread' => $this->auth->threadResource($this->thread->id)];
    }
}
