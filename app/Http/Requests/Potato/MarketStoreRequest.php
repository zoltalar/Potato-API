<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Models\Base;
use App\Models\Country;
use App\Models\Language;

class MarketStoreRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $length = Base::DEFAULT_STRING_LENGTH;

        return [
            'name' => ['required', "max:{$length}"],
            'email' => ['required', 'email', "max:{$length}"],
            'promote' => ['required']
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.farmers_market_name')),
            'promote' => mb_strtolower(__('phrases.promote_this_farmers_market'))
        ];
    }
}
