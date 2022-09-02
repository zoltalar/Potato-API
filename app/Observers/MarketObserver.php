<?php

namespace App\Observers;

use App\Models\Market;
use Str;

class MarketObserver
{
    public function saving(Market $market)
    {
        if ( ! empty($market->website) && ! Str::startsWith($market->website, 'http://')) {
            $market->website = 'http://' . $market->website;
        }

        if ($market->active == 1) {
            $market->deactivation_reason = null;
            $market->deactivated_at = null;
        }
    }
}
