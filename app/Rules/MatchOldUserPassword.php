<?php

declare(strict_types = 1);

namespace App\Rules;

use Hash;
use Illuminate\Contracts\Validation\Rule;

class MatchOldUserPassword implements Rule
{
    public function passes($attribute, $value): bool
    {
        return Hash::check($value, auth()->user()->password);
    }

    public function message(): string
    {
        return __('messages.match_old_user_password_error');
    }
}
