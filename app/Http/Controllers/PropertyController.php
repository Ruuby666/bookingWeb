<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PropertyController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly PropertyService $propertyService,
    ) {}

    public function show($id)
    {
        $property = Property::findOrFail($id);

        ['mainImage' => $mainImage, 'imagesWithoutFirst' => $imagesWithoutFirst] =
            $this->propertyService->getImagesForProperty($property);

        return view('property.show', compact('property', 'id', 'mainImage', 'imagesWithoutFirst'));
    }

    public function create()
    {
        return view('property.add_or_edit_property');
    }

    public function store(StorePropertyRequest $request)
    {
        $this->authorize('create', Property::class);

        $this->propertyService->createProperty($request->validated());

        return redirect()->route('admin.properties')->with('success', 'Property added successfully');
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        $this->authorize('view', $property);

        return view('property.add_or_edit_property', compact('property'));
    }

    public function update(UpdatePropertyRequest $request, $id)
    {
        $property = Property::findOrFail($id);
        $this->authorize('update', $property);

        $this->propertyService->updateProperty($property, $request->validated());

        return redirect()->route('admin.properties')->with('success', 'Property updated successfully!');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $this->authorize('update', $property);

        $property->delete();

        return redirect()->route('admin.properties')->with('success', 'Property deleted successfully.');
    }
}
