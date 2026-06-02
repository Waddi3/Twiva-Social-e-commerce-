<?php

namespace Modules\Buyer\Rules;
use DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueIf implements Rule
{
    protected $otherField;
    protected $otherValue;

    public function __construct($otherField, $otherValue)
    {
        $this->otherField = $otherField;
        $this->otherValue = $otherValue;
    }

    public function passes($attribute, $value)
    {
        $count = DB::table('buyers')
            ->where($attribute, $value)
            ->where($this->otherField, $this->otherValue)
            ->count();
        return $count === 0;
    }

    public function message()
    {
        return 'The :attribute has already been taken.';
    }

}
