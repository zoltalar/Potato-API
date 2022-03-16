<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Models\State;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;

trait Addressable
{
    /**
     * Retrieve formatted address line.
     *
     * @param   string $glue
     * @param   array $elements
     * @return  string
     */
    public function addressLine(string $glue = ',', array $elements = []): string
    {
        if ($glue == ',') {
            $glue = ', ';
        }

        $country = null;

        if ( ! empty($this->state_id)) {
            $country = $this->state->country;
        }

        if (empty($elements)) {
            $elements = ['address', 'address_2', 'city', 'state', 'zip'];
        }

        $address = '';

        if (in_array('address', $elements)) {
            $address = $this->address;
        }

        $address2 = $this->address_2;

        if (in_array('address_2', $elements) && ! empty($address2)) {
            $address .= ( ! empty($address) ? $glue : '') . $address2;
        }

        $middle = '';

        if (in_array('city', $elements)) {
            $middle = $this->city;
        }

        if (in_array('state', $elements) && ! empty($this->state_id)) {
            $middle .= ( ! empty($middle) ? ', ' : '') . $this->state->abbreviation;
        }

        if (in_array('zip', $elements) && ! empty($this->zip)) {
            $middle .= ( ! empty($middle) ? ' ' : '') . $this->zip;
        }

        if ( ! empty($middle)) {
            $address .= ( ! empty($address) ? $glue : '') . $middle;
        }

        if (in_array('country', $elements) && ! empty($this->state_id)) {
            $name = __('phrases.' . Str::slug($country->name, '_'));
            $address .= ( ! empty($address) ? $glue : '') . $name;
        }

        return $address;
    }

    /**
     * Get related state model.
     *
     * @return  BelongsTo
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
