<?php

declare(strict_types = 1);

namespace App\Http\Requests\Potato;

use App\Http\Requests\BaseRequest;
use App\Rules\FarmOwner;

class FarmPromoteUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        
        return ['promote' => ['required', new FarmOwner($id)]];
    }

    public function attributes(): array
    {
        return ['promote' => mb_strtolower(__('phrases.promote_this_farm'))];
    }
}
