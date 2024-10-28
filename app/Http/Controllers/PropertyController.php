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
}
