<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class IndexController extends Controller


{
    public function index()
    {
        $properties = Property::all();
        $propertyWithImages = [];

        foreach ($properties as $property) {
            $imageFolder = public_path('images/' . $property->image_url);
            $images = [];

            if (File::exists($imageFolder)) {

                $images = array_map('basename', File::files($imageFolder)); 
                $nameImage = $images[0] ?? null; 

            } else {
                $nameImage = null; 
            }
            
            $propertyWithImages[$property->id] = $nameImage;
        }

        return view('index', compact('propertyWithImages'));
    }

}
