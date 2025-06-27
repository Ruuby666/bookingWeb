<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function show($id)
    {
        $property = Property::findOrFail($id);
        $imageFolder = public_path('images/' . $property->images_div);
        $images = [];

        if (File::exists($imageFolder)) {
            $images = array_map('basename', File::files($imageFolder));
            $mainImage = $images[0] ?? null;
            $imagesWithoutFirst = array_slice($images, 1);
        } else {
            $mainImage = null;
            $imagesWithoutFirst = [];
        }

        return view('property.show', compact('property', 'id', 'mainImage', 'imagesWithoutFirst'));
    }

    public function create()
    {
        return view('property.add_or_edit_property');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price_per_night' => 'required|numeric',
            'capacity' => 'required|integer',
            'size' => 'required|integer',
            'bedrooms' => 'required|string',
            'bathrooms' => 'required|integer',
            'images_div' => 'required|string',
            'tv' => 'nullable|string',
            'entertainment' => 'required|boolean',
            'parking' => 'required|boolean',
            'pool' => 'required|boolean',
            'garden' => 'required|boolean',
            'safeBox' => 'required|boolean',
            'terrace' => 'required|boolean',
            'wifi' => 'required|boolean',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        // Convert bedrooms input to JSON format
        $bedroomsArray = array_map('trim', explode(',', $data['bedrooms']));
        $data['bedrooms'] = json_encode(array_combine(range(1, count($bedroomsArray)), $bedroomsArray));

        // Crear la propiedad con los datos
        Property::create($data);

        return redirect()->route('admin.properties')->with('success', 'Property added successfully');
    }

    public function destroy($id)
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return redirect()->route('admin.properties')->with('success', 'Property deleted successfully.');
    }

    public function edit($id)
    {
        $property = Property::findOrFail($id);
        return view('property.add_or_edit_property', compact('property'));
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'size' => 'required|numeric|min:0',
            'bedrooms' => 'required|string|max:100',
            'bathrooms' => 'required|integer|min:0',
            'images_div' => 'required|string|max:255',
            'tv' => 'nullable|string|max:100',
            'entertainment' => 'required|boolean',
            'parking' => 'required|boolean',
            'pool' => 'required|boolean',
            'garden' => 'required|boolean',
            'safeBox' => 'required|boolean',
            'terrace' => 'required|boolean',
            'wifi' => 'required|boolean',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $property->update($validated);

        return redirect()->route('admin.properties')->with('success', 'Property updated successfully!');
    }
}
