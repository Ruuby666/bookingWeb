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
            $imageFolder = public_path('images/' . $property->images_div);
            $nameImage = null;

            if (File::exists($imageFolder)) {
                $images = File::files($imageFolder);

                if (!empty($images)) {
                    $nameImage = basename($images[0]);
                }
            }

            $propertyWithImages[$property->id] = $nameImage;
        }
        return view('index', compact('propertyWithImages'));
    }
}
