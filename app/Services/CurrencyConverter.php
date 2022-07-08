<?php

declare(strict_types = 1);

namespace App\Services;

use Cache;
use Http;

final class CurrencyConverter
{
    /** @var string */
    protected $baseCode;

    public function __construct(string $baseCode)
    {
        $this->baseCode = $baseCode;
    }

    public function convert(string $code, float $amount): ?float
    {
        if (Cache::has($this->cacheKey())) {
            $rates = Cache::get($this->cacheKey());
        } else {
            Cache::put($this->cacheKey(), ($rates = Http::get($this->url())->json('conversion_rates')), 600);
        }

        if (is_array($rates) && array_key_exists($code, $rates)) {
            return round($amount * $rates[$code], 2);
        }

        return null;
    }

    public function baseCode(): string
    {
        return $this->baseCode;
    }

    protected function cacheKey(): string
    {
        return sprintf('potato.currency.conversion_rates.%s', $this->baseCode);
    }

    protected function key(): string
    {
        return config('services.exchange_rate.key');
    }

    protected function url(): string
    {
        return sprintf('https://v6.exchangerate-api.com/v6/%s/latest/%s', $this->key(), $this->baseCode);
    }
}
