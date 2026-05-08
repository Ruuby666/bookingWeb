<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PropertyService
{
    /**
     * Return all properties with their first/representative image filename.
     *
     * @return array{properties: \Illuminate\Database\Eloquent\Collection, propertyWithImages: array<int, string>}
     */
    public function getAllWithFirstImage(): array
    {
        $properties       = Property::all();
        $propertyWithImages = [];

        foreach ($properties as $property) {
            $imageFolder = public_path('images/' . $property->images_div);
            $nameImage   = 'default.jpg';

            if (File::exists($imageFolder)) {
                $files = File::files($imageFolder);
                if (! empty($files)) {
                    $nameImage = basename($files[0]);
                }
            }

            $propertyWithImages[$property->id] = $nameImage;
        }

        return compact('properties', 'propertyWithImages');
    }

    /**
     * Return the image list for a single property split into main + rest.
     *
     * @param  Property $property
     * @return array{mainImage: string|null, imagesWithoutFirst: array<int, string>}
     */
    public function getImagesForProperty(Property $property): array
    {
        $imageFolder = public_path('images/' . $property->images_div);

        if (! File::exists($imageFolder)) {
            return ['mainImage' => null, 'imagesWithoutFirst' => []];
        }

        $images = array_map('basename', File::files($imageFolder));

        return [
            'mainImage'         => $images[0] ?? null,
            'imagesWithoutFirst' => array_slice($images, 1),
        ];
    }

    /**
     * Create a new property owned by the authenticated user.
     *
     * @param  array $data  Validated request data (bedrooms as comma-separated string)
     * @return Property
     */
    public function createProperty(array $data): Property
    {
        $data['bedrooms'] = $this->parseBedroomsToJson($data['bedrooms']);
        $data['owner_id'] = Auth::id();

        return Property::create($data);
    }

    /**
     * Update an existing property.
     *
     * @param  Property $property
     * @param  array    $data  Validated request data
     * @return Property
     */
    public function updateProperty(Property $property, array $data): Property
    {
        $property->update($data);

        return $property->fresh();
    }

    /**
     * Convert a comma-separated bedroom string into a JSON object.
     * e.g. "King, Twin, Double" => {"1":"King","2":"Twin","3":"Double"}
     *
     * @param  string $bedrooms
     * @return string  JSON-encoded
     */
    private function parseBedroomsToJson(string $bedrooms): string
    {
        $items = array_map('trim', explode(',', $bedrooms));

        return json_encode(array_combine(range(1, count($items)), $items));
    }
}
