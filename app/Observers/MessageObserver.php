<?php

namespace App\Observers;

use App\Message;
use App\Thread;

class MessageObserver
{
    /**
     * Handle the message "created" event.
     *
     * @param Message $message
     * @return void
     */
    public function created(Message $message)
    {
        Thread::query()->where('id', $message->thread_id)
            ->update(['updated_at' => now()->format('Y-m-d H:i:s')]);
    }

    /**
     * Handle the message "updated" event.
     *
     * @param Message $message
     * @return void
     */
    public function updated(Message $message)
    {
        //
    }

    /**
     * Handle the message "deleted" event.
     *
     * @param Message $message
     * @return void
     */
    public function deleted(Message $message)
    {
        //
    }
}
