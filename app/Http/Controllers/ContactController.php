<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return response()->json(User::query()
            ->where('id', '!=', auth()->id())
            ->orderByDesc("created_at")->paginate(20));
    }
}
