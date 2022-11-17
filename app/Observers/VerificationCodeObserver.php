<?php

namespace App\Observers;

use App\Models\VerificationCode;
use Hash;

class VerificationCodeObserver
{
    public function creating(VerificationCode $code)
    {
        if ($code->expires_at === null) {
            $code->expires_at = now()->addHours($code->expireHours());
        }

        if (Hash::needsRehash($code->code)) {
            $code->code = Hash::make($code->code);
        }
    }

    public function created(VerificationCode $code)
    {
        $max = $code->maxCodes();

        $ids = VerificationCode::query()
            ->where('verifiable', $code->verifiable)
            ->orderByDesc('expires_at')
            ->orderByDesc('id')
            ->skip($max)
            ->take(PHP_INT_MAX)
            ->pluck('id');

        VerificationCode::query()
            ->whereIn('id', $ids)
            ->delete();
    }
}
