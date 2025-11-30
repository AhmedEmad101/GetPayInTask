<?php

namespace App\Actions\Hold;
use Illuminate\Support\Str;

final class generateUnikeKeyAction
{
    public static function execute()
    { 
        return rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
    }
}