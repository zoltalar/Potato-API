<?php

namespace App\Services\VerificationCode;

final class Generator
{
    public function generate(): string
    {
        $length = $this->length();
        $characters = $this->characters();

        return collect(range(0, $length - 1))
            ->map(function() use ($characters) {
                return $characters[rand(0, strlen($characters) - 1)];
            })
            ->join('');
    }

    protected function length(): int
    {
        return 7;
    }

    protected function characters(): string
    {
        return '123456789';
    }
}
