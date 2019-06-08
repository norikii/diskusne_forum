<?php

namespace App\Http\Controllers;

use App\Inspections\Spam;
use App\Reply;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * @param Spam $spam
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(string $channel_id, Thread $thread, Spam $spam)
    {
        try {
            $this->validate(request(), ['body' => 'required']);

            $spam->detect(request('body'));

            $reply =$thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ]);
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time.', 422);
        }

        return $reply->load('owner');
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

    public function update(Reply $reply, Spam $spam)
    {
        $this->authorize('update', $reply);

        try {
            $this->validate(request(), ['body' => 'required']);

            $spam->detect(request('body'));

            $reply->update(request(['body']));
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time.', 422);
        }
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(10);
    }
}
