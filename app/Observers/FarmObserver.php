<?php

namespace App\Observers;

use App\Models\Farm;

class FarmObserver
{
    public function saving(Farm $farm)
    {
        if ($farm->active == 1) {
            $farm->deactivation_reason = null;
            $farm->deactivated_at = null;
        }
    }
}
