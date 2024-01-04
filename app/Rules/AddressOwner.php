<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Address;
use App\Models\Event;
use App\Models\Farm;
use App\Models\Market;
use Illuminate\Contracts\Validation\Rule;

class AddressOwner implements Rule
{
    /** @var string */
    protected $type;
    
    /** @var int */
    protected $id;

    /** @var Base */
    protected $addressable = null;

    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
        
        $this->initialize();
    }
    
    protected function initialize(): void
    {
        $type = $this->type;
        $id = $this->id;
        
        if ($type === Address::TYPE_ADDRESSABLE_EVENT) {
            $addressable = Event::query()
                ->whereHas('eventable', function($query) {
                    $query->where('user_id', auth()->id());
                })
                ->find($id);
        } elseif ($type === Address::TYPE_ADDRESSABLE_FARM) {
            $addressable = Farm::query()
                ->where('user_id', auth()->id())
                ->find($id);
        } elseif ($type === Address::TYPE_ADDRESSABLE_MARKET) {
            $addressable = Market::query()
                ->where('user_id', auth()->id())
                ->find($id);
        } logger()->info($type);logger()->info($id);
        
        if (isset($addressable)) {
            $this->addressable = $addressable;
        }
    }

    public function passes($attribute, $value): bool
    {
        $addressable = $this->addressable;
        
        if ($addressable !== null) {
            return auth()->id() === $addressable->user_id;
        }
        
        return false;
    }

    public function message(): string
    {
        return __('messages.address_owner_error');
    }
}
