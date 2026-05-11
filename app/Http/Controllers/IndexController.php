<?php

namespace App\Http\Controllers;

use App\Services\PropertyService;

/**
 * Controller responsible for the homepage.
 */
class IndexController extends Controller
{
    /**
     * Inject required services.
     */
    public function __construct(
        private readonly PropertyService $propertyService,
    ) {}

    /**
     * Display the homepage with available properties.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        ['properties' => $properties, 'propertyWithImages' => $propertyWithImages] =
            $this->propertyService->getAllWithFirstImage();

        return view('index', compact('propertyWithImages', 'properties'));
    }
}
