<?php

declare(strict_types = 1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class CityUpdateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:60'],
            'population' => ['nullable', 'numeric', 'min:0'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'state_id' => ['required', 'exists:states,id']
        ];

        $city = $this->route('city');
        $id = $city->id ?? null;
        $name = $this->name;
        $stateId = $this->state_id;

        if ( ! empty($name) && ! empty($stateId)) {
            $unique = Rule::unique('cities')
                ->where(function($query) use ($name, $stateId) {
                    return $query
                        ->where('name', $name)
                        ->where('state_id', $stateId);
                })
                ->ignore($id);
            $rules['name'][] = $unique;
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => mb_strtolower(__('phrases.name')),
            'latitude' => mb_strtolower(__('phrases.latitude')),
            'longitude' => mb_strtolower(__('phrases.longitude')),
            'state_id' => mb_strtolower(__('phrases.state'))
        ];
    }
}
