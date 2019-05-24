<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $with = ['owner', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected static function boot()
    {
        parent::boot();

//        static::addGlobalScope('replyCount', function ($builder) {
//            $builder->withCount('replies');
//        });

        // when you deleting the thread delete as well
        // all replies
        static::deleting(function ($thread) {
            $thread->replies->each->delete();
            // alternatively use
//            $thread->replies()->each(function ($reply) {
//                $reply->delete();
//            });
        });
    }

    /**
     * Helper function to get path
     *
     * @return string
     */
    public function path()
    {
        return '/threads/' . $this->channel->slug . '/' . $this->id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * @param $reply
     * @return Model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        $this->subscriptions->filter(function ($sub) use ($reply) {
            // if the subscriber's id is not the reply's owner id
           return $sub->user_id != $reply->user_id;
           // for each subscription call notify method
        })->each->notify($reply);


        return $reply;
    }

    public function scopeFilter($query, $filters)
    {
        // we want to apply the set of filters to the current
        // thread query we have running
        return $filters->apply($query);
    }

    /**
     * Subscribe a user to a current thread.
     *
     * @param null $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            // use the userId if one is provided
            // otherwise check the authenticated user
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     * A thread can have many subscriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
                ->where('user_id', auth()->id())
                ->exists();
    }
}
