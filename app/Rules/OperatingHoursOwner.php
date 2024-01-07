<?php

declare(strict_types = 1);

namespace App\Rules;

use App\Models\Base;
use App\Models\Farm;
use App\Models\Market;
use App\Models\OperatingHour;
use Illuminate\Contracts\Validation\Rule;

class OperatingHoursOwner implements Rule
{
    /** @var string */
    protected $type;
    
    /** @var int */
    protected $id;
    
    /** @var Base */
    protected $operatable;

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
        
        if ($type === OperatingHour::TYPE_OPERATABLE_FARM) {
            $operatable = Farm::find($id);
        } elseif ($type === OperatingHour::TYPE_OPERATABLE_MARKET) {
            $operatable = Market::find($id);
        }
        
        if (isset($operatable)) {
            $this->operatable = $operatable;
        }
    }

    public function passes($attribute, $value): bool
    {
        $operatable = $this->operatable;
        
        if ($operatable !== null) {
            return auth()->id() === $operatable->user_id;
        }
        
        return false;
    }

    public function message(): string
    {
        return __('messages.operating_hours_owner_error');
    }
}
