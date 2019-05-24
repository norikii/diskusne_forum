<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    /**
     * RepliesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    /**
     * Persists a new reply
     *
     * @param string $channel_id
     * @param Thread $thread
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(string $channel_id, Thread $thread)
    {
        $this->validate(request(), ['body' => 'required']);

        $reply =$thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);

        // this will cast reply to json
        if (request()->expectsJson()) {
            return $reply->load('owner');
        }

        return back()->with('flash', 'Your reply has been added');
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->delete();

        // expectsJson = if we have a ajax request
        if (request()->expectsJson()) {
            return response(['status' => 'Reply Deleted']);
        }

        return back();
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        $reply->update(request(['body']));
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }
}
