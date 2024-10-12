<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function show($id)
    {
        $property = Property::findOrFail($id);  // If not found, return 404

        // Assuming images are stored in 'public/images/{property_id}/'
        $imageFolder = public_path('images/' . $property->image_url);

        $images = [];

        // Check if the image folder exists
        if (File::exists($imageFolder)) {
            // Get all file names in the image folder
            $images = array_map('basename', File::files($imageFolder)); // Extract only file names

            // Determine the main image and the others for thumbnails
            $mainImage = $images[0] ?? null; // Get the first image, or null if not available

            $imagesWithoutFirst = array_slice($images, 1); // All other images

        } else {
            $mainImage = null;  // Set mainImage to null if no images found
            $imagesWithoutFirst = [];  // Initialize as an empty array
        }

        // Pass the property to the view
        return view('property.show', compact('property', 'id', 'mainImage', 'imagesWithoutFirst'));
    }
}
