<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyService
{
    /**
     * Get all properties with a representative image for each one.
     *
     * @return array{properties: Collection, propertyWithImages: array<int, string>}
     */
    public function getAllWithFirstImage(): array
    {
        $properties = Property::all();
        $propertyWithImages = [];

        foreach ($properties as $property) {
            $files = Storage::disk('public')->files('images/' . $property->images_div);
            $propertyWithImages[$property->id] = ! empty($files)
                ? basename($files[0])
                : 'default.jpg';
        }

        return compact('properties', 'propertyWithImages');
    }

    /**
     * Get images for a specific property.
     * Returns the main image and the rest of the gallery images.
     *
     * @return array{mainImage: string|null, imagesWithoutFirst: array<int, string>}
     */
    public function getImagesForProperty(Property $property): array
    {
        $files = Storage::disk('public')->files('images/' . $property->images_div);

        if (empty($files)) {
            return [
                'mainImage' => null,
                'imagesWithoutFirst' => [],
            ];
        }

        $images = array_map('basename', $files);

        return [
            'mainImage' => $images[0],
            'imagesWithoutFirst' => array_slice($images, 1),
        ];
    }

    /**
     * Create a new property for the authenticated user.
     * The image folder is generated automatically from the property title.
     *
     * @param  array  $data  Validated property data
     */
    public function createProperty(array $data): Property
    {
        // La carpeta se genera a partir del título de la propiedad
        $folder = $this->generateFolderName($data['title']);

        // Sube las imágenes si se han proporcionado
        if (! empty($data['images'])) {
            $this->uploadImages($data['images'], $folder);
        }

        $data['bedrooms'] = $this->parseBedroomsToJson($data['bedrooms']);
        $data['owner_id'] = Auth::id();
        $data['images_div'] = $folder;

        unset($data['images']);

        return Property::create($data);
    }

    /**
     * Update an existing property.
     * If new images are provided, they are added to the existing folder.
     *
     * @param  array  $data  Validated property data
     */
    public function updateProperty(Property $property, array $data): Property
    {
        // Solo sube nuevas imágenes si se han proporcionado
        if (! empty($data['images'])) {
            $this->uploadImages($data['images'], $property->images_div);
        }

        $data['bedrooms'] = $this->parseBedroomsToJson($data['bedrooms']);

        unset($data['images']);

        $property->update($data);

        return $property->fresh();
    }

    /**
     * Sube un array de archivos a la carpeta de la propiedad en Storage.
     *
     * @param  \Illuminate\Http\UploadedFile[]  $images
     */
    private function uploadImages(array $images, string $folder): void
    {
        foreach ($images as $image) {
            $image->store('images/' . $folder, 'public');
        }
    }

    /**
     * Genera un nombre de carpeta válido a partir del título de la propiedad.
     * Ejemplo: "Casa del Sol" => "casa_del_sol"
     */
    private function generateFolderName(string $title): string
    {
        return Str::slug($title, '_');
    }

    /**
     * Convert a comma-separated string of bedrooms into a JSON structure.
     *
     * Example:
     * "King, Twin, Double" => {"1":"King","2":"Twin","3":"Double"}
     *
     * @return string JSON encoded string
     */
    private function parseBedroomsToJson(string $bedrooms): string
    {
        $items = array_map('trim', explode(',', $bedrooms));

        return json_encode(array_combine(range(1, count($items)), $items));
    }
}
