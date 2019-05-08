<?php

namespace App;

use ReflectionException;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        if (auth()->guest()) return;
//         when the thread is created in the database we will
//         ass well create new activity
//        foreach (static::getRecordEvents() as $event) {
//            static::$event(function ($model) use ($event) {
//                $model->recordAcitvity($event);
//            });
//
//        }
        static::created(function ($thread) {
            $thread->recordActivity('created');
        });

        static::deleting(function ($thread) {
           $thread->activity()->delete();
        });
    }

    /**
     * @param $event
     * @return string
     * @throws ReflectionException
     */
    protected function getActivityType($event): string
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }

    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => auth()->id(),
            'type' => $this->getActivityType($event),
        ]);
    }

    protected static function getRecordEvents()
    {
        return ['created'];
    }

    public function activity()
    {
        // morphMany is like hasMany without the need of
        // hardcoding the related model.
        return $this->morphMany('App\Activity', 'subject');
    }
}
