<?php

namespace App\Http\Controllers;

use App\Events\ThreadEvent;
use App\Thread;
use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        return response()->json($user->threadResource());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users.*' => 'required|numeric|exists:users,id'
        ]);

        if ($validator->fails())
            return response()->json($validator->errors()->all(), 422);

        $auth = $this->user();

        if (count($request->input('users')) == 1) {
            $thread = $auth->hasThread($request->input('users')[0]);
            if ($thread) return response()->json($thread);
        }

        $thread = new Thread();
        $thread->caption = $request->input("caption", "");
        $thread->creator = $auth->id;
        $thread->save();

        foreach ($request->input('users') as $item)
            $thread->users()->attach($item);

        $thread->users()->attach($auth);

        event(new ThreadEvent($auth, $thread));

        return response()->json($thread);
    }

    /**
     * Display the specified resource.
     *
     * @param Thread $thread
     * @return Response
     * @throws AuthorizationException
     */
    public function show(Thread $thread)
    {
        $this->authorize('view', $thread);
        return response()->json($thread->load('users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Thread $thread
     * @return Response
     * @throws AuthorizationException
     */
    public function update(Request $request, Thread $thread)
    {
        $this->authorize('update', $thread);
        return response()->json(true);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Thread $thread
     * @return Response
     */
    public function destroy(Thread $thread)
    {
        return response()->json($thread->users()->detach($this->user()));
    }

    /**
     * @return User
     */
    private function user()
    {
        return auth()->user();
    }
}
