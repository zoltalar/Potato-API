<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Str;

trait Addressable
{
    public function addressLine(string $glue = ',', array $elements = []): string
    {
        if ($glue == ',') {
            $glue = ', ';
        }

        $country = $this->state->country ?? null;

        if (empty($elements) && $country !== null) {

            if ($country->name == Country::NAME_POLAND) {
                $elements = ['address', 'zip', 'city', 'country'];
            }
        }

        $address = '';

        if (in_array('address', $elements)) {
            $address = $this->address;
        }

        if (in_array('address_2', $elements) && ! empty($this->address_2)) {
            $address .= ( ! empty($address) ? $glue : '') . $this->address_2;
        }

        $middle = '';

        if ($country !== null) {

            if ($country->name == Country::NAME_POLAND) {

                if (in_array('zip', $elements) && ! empty($this->zip)) {
                    $middle .= $this->zip;
                }

                if (in_array('city', $elements)) {
                    $middle .= ( ! empty($middle) ? ' ' : '') . $this->city;
                }
            }
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

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
