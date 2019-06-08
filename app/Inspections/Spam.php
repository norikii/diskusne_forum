<?php

namespace App\Inspections;

/**
 * Class responsible for detecting any kind of spam messages
 * Class Spam
 * @package App
 */
class Spam
{
    protected $inspections = [
        InvalidKeywords::class,
        KeyHeldDown::class
    ];

    public function detect($body)
    {
        foreach ($this->inspections as $inspection) {
            // create an instance of this class and calls detect
            app($inspection)->detect($body);
        }

        return false;
    }
}
