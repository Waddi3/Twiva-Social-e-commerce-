<?php

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class TimeCast implements CastsAttributes {
    public function get($model, string $key, mixed $value, array $attributes)
    {
        return  Carbon::parse($value)->format('g:i A');
    }

    public function set($model, string $key, mixed $value, array $attributes)
    {
        return $value;
    }
}
