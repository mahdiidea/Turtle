<?php

namespace App\Http\Controllers;

use App\Attach;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Post::query()->with('user', 'attaches')
            ->orderByDesc('created_at')->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required|max:255',
            'picture' => 'max:10240|file'
        ]);

        if ($validator->fails())
            return response()->json($validator->errors()->all(), 422);

        $user_id = auth()->id();

        $post = new Post();
        $post->user_id = $user_id;
        $post->body = $request->input('body');
        $post->save();

        if ($request->hasFile('picture')) {
            $attach = new Attach();
            $attach->user_id = $user_id;
            $attach->uri = $request->file('picture')->store('posts');
            $attach->mime = $request->file('picture')->getMimeType();
            $attach->size = $request->file('picture')->getSize();
            $attach->save();

            $post->attaches()->attach($attach);
        }

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
