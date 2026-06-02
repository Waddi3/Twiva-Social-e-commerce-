<?php

namespace Modules\Campaign\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class MinInfluencerPriceInArrayRule implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value): bool
    {

        Log::debug('MinInfluencerPriceInArrayRule', ['$attribute' => $attribute, '$value' => $value]);
    }

    public function message(): string
    {
        return 'The validation error message.';
    }
}