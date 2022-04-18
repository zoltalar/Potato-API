<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Inventory;

class InventoryObserver
{
    public function deleted(Inventory $inventory)
    {
        $inventory->translations()->delete();
    }
}
