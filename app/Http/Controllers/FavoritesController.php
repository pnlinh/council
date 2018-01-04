<?php

namespace App\Http\Controllers;

use App\Reply;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }

    public function store(Reply $reply)
    {
        $reply->favorite();

        return redirect()->back();
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}
