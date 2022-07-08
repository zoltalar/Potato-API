<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Farm;
use App\Notifications\FarmDeactivation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFarmDeactivationNotificationJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /** @var Farm */
    protected $farm;

    public function __construct(Farm $farm)
    {
        $this->farm = $farm;
    }

    public function handle()
    {
        $admins = Admin::query()
            ->active()
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new FarmDeactivation($this->farm));
        }
    }
}
