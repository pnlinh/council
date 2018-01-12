<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Reputation;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }

    public function store(Reply $reply)
    {
        $reply->favorite();

        Reputation::award($reply->owner, Reputation::REPLY_FAVORITED);
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();

        Reputation::reduce($reply->owner, Reputation::REPLY_FAVORITED);
    }
}
