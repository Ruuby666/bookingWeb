<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PropertyForm extends Component
{
    public object $property;
    public int $maxCapacity;
    public string $sendEmailRoute;
    
    public function __construct(object $property)
    {
        $this->property       = $property;
        $this->maxCapacity    = (int) $property->capacity;
        $this->sendEmailRoute = route('send.email');
    }
    public function render(): View|Closure|string
    {
        return view('components.property-form');
    }
}
