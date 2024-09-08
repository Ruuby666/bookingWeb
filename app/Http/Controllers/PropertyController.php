<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function show($id)
    {
        $property = Property::findOrFail($id);  // If not found, return 404

        // Pass the property to the view
        return view('property.show', compact('property'));
    }
}
