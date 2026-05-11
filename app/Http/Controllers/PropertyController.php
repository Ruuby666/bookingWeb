<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;


/**
 * Controller responsible for property management.
 */
class PropertyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Inject required services.
     */
    public function __construct(
        private readonly PropertyService $propertyService,
    ) {}

    /**
     * Display a specific property.
     *
     * @param int $id Property ID
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $property = Property::findOrFail($id);

        ['mainImage' => $mainImage, 'imagesWithoutFirst' => $imagesWithoutFirst] =
            $this->propertyService->getImagesForProperty($property);

        return view(
            'property.show',
            compact('property', 'id', 'mainImage', 'imagesWithoutFirst')
        );
    }

    /**
     * Show the property creation form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('property.add_or_edit_property');
    }

    /**
     * Store a new property.
     *
     * @param StorePropertyRequest $request Property data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePropertyRequest $request)
    {
        $this->authorize('create', Property::class);

        $this->propertyService->createProperty($request->validated());

        return redirect()
            ->route('admin.properties')
            ->with('success', 'Property added successfully');
    }

    /**
     * Show the property edit form.
     *
     * @param int $id Property ID
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {

        $property = Property::findOrFail($id);

        $this->authorize('view', $property);

        return view('property.add_or_edit_property', compact('property'));
    }

    /**
     * Update an existing property.
     *
     * @param UpdatePropertyRequest $request Updated property data
     * @param int $id Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatePropertyRequest $request, $id)
    {
        $property = Property::findOrFail($id);

        $this->authorize('update', $property);

        $this->propertyService->updateProperty(
            $property,
            $request->validated()
        );

        return redirect()
            ->route('admin.properties')
            ->with('success', 'Property updated successfully!');
    }

    /**
     * Delete a property.
     *
     * @param int $id Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        $this->authorize('update', $property);

        $property->delete();

        return redirect()
            ->route('admin.properties')
            ->with('success', 'Property deleted successfully.');
    }
}
