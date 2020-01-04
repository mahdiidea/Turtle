<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use Illuminate\Support\Facades\Broadcast;
use App\Thread;

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('messengers.{user_id}', function ($user, $user_id) {
    return (int)$user->id === (int)$user_id;
});

Broadcast::channel('threads.{thread_id}', function ($user, $thread_id) {
    /** @var Thread $thread */
    $thread = Thread::query()->find($thread_id);
    return $thread->hasParticipant($user->id);
});