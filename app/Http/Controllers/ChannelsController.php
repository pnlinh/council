<?php

namespace App\Http\Controllers;

use App\Channel;

class ChannelsController extends Controller
{
    public function index(Channel $channel)
    {
        $threads = $channel->threads()->latest()->get();
        return view('threads.index', ['threads' => $threads]);
    }
}
