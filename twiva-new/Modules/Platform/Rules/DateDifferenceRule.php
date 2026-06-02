<?php

namespace Modules\Platform\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class DateDifferenceRule implements Rule
{
    protected $start_date;
    protected $diff_in_day;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($start_date, $diff_in_day)
    {
        $this->start_date = $start_date;
        $this->diff_in_day = $diff_in_day;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($value);

        return $end->diffInDays($start) <= $this->diff_in_day;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute must not be more than {$this->diff_in_day} days after the start date.";
    }
}
