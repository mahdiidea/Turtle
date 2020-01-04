<?php

namespace App\Http\Controllers;

use App\Attach;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class AttachController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'attach' => 'required|file|max:102400'
        ]);

        if ($validate->fails())
            return response()->json($validate->errors()->all(), 422);

        $attach = new Attach();
        $attach->user_id = auth()->id();
        $attach->uri = $request->file('attach')->store('attachments');
        $attach->mime = $request->file('attach')->getMimeType();
        $attach->size = $request->file('attach')->getSize();
        $attach->name = $request->file('attach')->getClientOriginalName();
        $attach->save();

        return response()->json($attach);
    }

    /**
     * Display the specified resource.
     *
     * @param Attach $attach
     * @return Response
     */
    public function show(Attach $attach)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Attach $attach
     * @return Response
     */
    public function edit(Attach $attach)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Attach $attach
     * @return Response
     */
    public function update(Request $request, Attach $attach)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Attach $attach
     * @return Response
     */
    public function destroy(Attach $attach)
    {
        //
    }
}
