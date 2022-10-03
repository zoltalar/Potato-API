<?php

namespace App\Jobs;

use App\Models\Admin;
use App\Models\Market;
use App\Notifications\MarketDeactivation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMarketDeactivationNotificationJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /** @var Market */
    protected $market;

    public function __construct(Market $market)
    {
        $this->market = $market;
    }

    public function handle()
    {
        $admins = Admin::query()
            ->active()
            ->get();

        foreach ($admins as $admin) {
            $admin->notify(new MarketDeactivation($this->market));
        }
    }
}
