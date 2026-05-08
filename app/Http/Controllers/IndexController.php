<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;

class IndexController extends Controller
{
    public function __construct(
        private readonly PropertyService $propertyService,
    ) {}

    public function index()
    {
        ['properties' => $properties, 'propertyWithImages' => $propertyWithImages] =
            $this->propertyService->getAllWithFirstImage();

        return view('index', compact('propertyWithImages', 'properties'));
    }
}
