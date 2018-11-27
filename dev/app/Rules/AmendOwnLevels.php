<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AmendOwnLevels implements Rule
{
    private $lastNegotiation;
    private $organisation_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($lastNegotiation, $organisation_id)
    {
        $this->lastNegotiation = $lastNegotiation;
        $this->organisation_id = $organisation_id;
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
        $att = explode('.', $attribute);
        $att = $att[count($att)-1];

        if(!in_array($att, ['bid', 'offer'])) {
            return false;
        }

        \Log::info([$att, $value]);
        if($value != null) {
            $source = $this->lastNegotiation->marketNegotiationSource($att);
            \Log::info([$source, $source->user->organisation_id]);
            return $source->user->organisation_id == $this->organisation_id;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'You may only amend your own levels.';
    }
}
