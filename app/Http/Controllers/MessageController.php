<?php

namespace App\Http\Controllers;

use App\Attach;
use App\Events\MessageCreatedEvent;
use App\Events\ThreadEvent;
use App\Message;
use App\Participant;
use App\Thread;
use App\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Thread $thread
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Thread $thread)
    {
        $this->authorize('view', $thread);

        $auth_id = auth()->id();

        $thread->markAsRead($auth_id);

        /*
        * Call event
        */

        foreach ($thread->users()->get() as $user)
            event(new ThreadEvent($user, $thread));

        $participant = Participant::query()
            ->where('thread_id', $thread->id)
            ->where('user_id', '!=', $auth_id)
            ->first();

        return response()->json($thread->messages()
            ->select('messages.*', DB::raw("(messages.created_at <= '".$participant->last_seen."') as seen"))
            ->latest()->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Thread $thread
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function store(Thread $thread, Request $request)
    {
        $this->authorize('create', [$thread, Message::class]);

        $validator = Validator::make($request->all(), [
            'body' => 'required_without_all:attach|max:10240',
            'attach' => 'required_without_all:body|array',
            'attach.*.attaches.id' => 'numeric|exists:attaches,id'
        ]);

        if ($validator->fails())
            return response()->json($validator->errors()->all(), 422);

        $auth = $this->user();
        $message = new Message();
        $message->thread_id = $thread->id;
        $message->user_id = $auth->id;
        $message->body = $request->input('body');
        $message->save();

        if ($request->has('attach')) {
            foreach ($request->input('attach') as $item) {
                try {
                    /** @var Attach $attach */
                    $attach = Attach::query()->find($item["attaches"]["id"]);
                    if ($attach->user_id == $auth->id) {
                        $message->attaches()->attach($attach);
                    }
                } catch (Exception $exception) {

                }
            }
        }

        $thread->markAsRead($auth->id);

        /*
         * Call event
         */

        event(new MessageCreatedEvent($message));
        /** @var Thread $thread */
        $thread = $message->thread()->first();
        foreach ($thread->users()->get() as $user)
            event(new ThreadEvent($user, $thread));

        return response()->json($message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Thread $thread
     * @param Message $message
     * @return Response
     * @throws AuthorizationException
     */
    public function update(Request $request, Thread $thread, Message $message)
    {
        $this->authorize('update', $message);
        $validator = Validator::make($request->all(), [
            'body' => 'required|max:10240'
        ]);

        if ($validator->fails())
            return response()->json($validator->errors()->all(), 422);

        $message->body = $request->input('body');
        $message->save();

        $thread->markAsRead(auth()->id());

        return response()->json($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Message $message
     * @return Response
     * @throws Exception
     */
    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        return response()->json($message->delete());
    }

    /**
     * @param Thread $thread
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function markAsRead(Thread $thread)
    {
        $this->authorize('view', $thread);

        $thread->markAsRead(auth()->id());

        $thread->fresh();

        /*
        * Call event
        */

        foreach ($thread->users()->get() as $user)
            event(new ThreadEvent($user, $thread));

        return response()->json(true);
    }

    /**
     * @return User
     */
    private function user()
    {
        return auth()->user();
    }
}
