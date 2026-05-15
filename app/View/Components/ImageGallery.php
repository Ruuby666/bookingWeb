<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageGallery extends Component
{
    public object $property;

    public string $mainImage;

    public array $imagesWithoutFirst;

    public function __construct(object $property, string $mainImage, array $imagesWithoutFirst = [])
    {
        $this->property = $property;
        $this->mainImage = $mainImage;
        $this->imagesWithoutFirst = $imagesWithoutFirst;
    }

    public function render(): View|Closure|string
    {
        return view('components.image-gallery');
    }
}
