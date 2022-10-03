<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Rules\MarketDeactivated;

class MarketDeactivateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return ['deactivation_reason' => ['required', 'string', 'max:500', new MarketDeactivated($id)]];
    }

    public function attributes(): array
    {
        return ['deactivation_reason' => mb_strtolower(__('phrases.deactivation_reason'))];
    }
}
