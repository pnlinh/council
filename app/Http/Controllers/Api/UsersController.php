<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function index()
    {
        return User::where('name', 'LIKE', request('name').'%')
            ->take(5)
            ->pluck('name');
    }
}
