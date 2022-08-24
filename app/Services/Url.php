<?php

declare(strict_types = 1);

namespace App\Services;

final class Url
{
    public static function appBaseUrl(): string
    {
        return config('app.nuxt_app_url');
    }
}
