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
        $rates = Cache::get($this->cacheKey());

        if (empty($rates)) {
            Cache::put($this->cacheKey(), ($rates = Http::get($this->url())->json('conversion_rates')), 60);
        }

        if (is_array($rates) && array_key_exists($code, $rates)) {
            return round($amount * $rates[$code], 2);
        }

        return null;
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
