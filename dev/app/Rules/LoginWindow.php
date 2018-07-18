<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Helpers\Misc\TimeRestrictions;
use Carbon\Carbon;

class LoginWindow implements Rule
{
    private $time;
    private $startTime;
    private $endTime;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->time = Carbon::now();
        $this->startTime = Carbon::createFromTimeString(config('marketmartial.window.operation_start_time'));
        $this->endTime = Carbon::createFromTimeString(config('marketmartial.window.operation_end_time'));
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
        return TimeRestrictions::canLogin($this->time);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Login is only during '.$this->startTime->format('H:i').' and '.$this->endTime->format('H:i').' On Weekdays';
    }
}
