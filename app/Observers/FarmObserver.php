<?php

namespace App\Observers;

use App\Models\Farm;
use Str;

class FarmObserver
{
    public function saving(Farm $farm)
    {
        if ( ! empty($farm->website) && ! Str::startsWith($farm->website, 'http://')) {
            $farm->website = 'http://' . $farm->website;
        }

        if ($farm->active == 1) {
            $farm->deactivation_reason = null;
            $farm->deactivated_at = null;
        }
    }
}
