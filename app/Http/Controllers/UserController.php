<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function leaderboard(Request $request)
    {
        return User::orderBy('points', 'DESC')->get();
    }
}
