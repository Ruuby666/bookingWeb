<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // JSON con las propiedades
        $propertiesJson = '[
            {
                "id": 1,
                "title": "Casa Delfin Playa Blanca",
                "description": "Situated in Playa Blanca, 1.4 km from Playa Dorada and 1.6 km from Playa Blanca...",
                "location": "23 Calle Bonilla, 35580 Playa Blanca, Spain",
                "price_per_night": "135.00",
                "capacity": 6,
                "size": "150",
                "bedrooms": "{\"1\": \"1 large double bed\", \"2\": \"1 double bed\", \"3\": \"2 single beds\"}",
                "bathrooms": 2,
                "images_div": "delfin",
                "tv": "Flat-screen TV, Cable channels, Satellite channels, DVD player, TV",
                "entertainment": 1,
                "parking": true,
                "pool": true,
                "garden": true,
                "safeBox": true,
                "terrace": true,
                "wifi": true,
                "lat": "28.8742420",
                "lng": "-13.8252320"
            },
            {
                "id": 2,
                "title": "El Galeon",
                "description": "Set in Playa Blanca, 500 metres from Playa Blanca and less than 1 km from Flamingo Beach...",
                "location": "Calle Chalana, 14., 35580 Playa Blanca, Spain",
                "price_per_night": "90.00",
                "capacity": 2,
                "size": "60",
                "bedrooms": "{\"1\": \"1 double bed\"}",
                "bathrooms": 1,
                "images_div": "galeon",
                "tv": "Flat-screen TV, Cable channels, Satellite channels, TV",
                "entertainment": 0,
                "parking": true,
                "pool": false,
                "garden": false,
                "safeBox": true,
                "terrace": true,
                "wifi": true,
                "lat": "28.8599400",
                "lng": "-13.8356700"
            },
            {
                "id": 3,
                "title": "Marlin I Puerto del Carmen",
                "description": "Marlin I Puerto del Carmen se encuentra en Puerto del Carmen, a 10 min a pie de Playa Chica...",
                "location": "Calle Teide 43 - Aptos Rincon C1, 35518 Puerto del Carmen, España",
                "price_per_night": "80.00",
                "capacity": 2,
                "size": "51",
                "bedrooms": "{\"1\": \"1 cama doble\"}",
                "bathrooms": 1,
                "images_div": "marlinc1",
                "tv": "TV de pantalla plana, Canales vía satélite, TV",
                "entertainment": 0,
                "parking": true,
                "pool": true,
                "garden": false,
                "safeBox": true,
                "terrace": true,
                "wifi": true,
                "lat": "28.9220720",
                "lng": "-13.6762580"
            },
            {
                "id": 4,
                "title": "Marlin II Puerto del Carmen",
                "description": "Marlin II Puerto del Carmen se encuentra en Puerto del Carmen, a 11 min a pie de Playa Chica...",
                "location": "Calle Teide 43 - Aptos Rincon C2, 35518 Puerto del Carmen, España",
                "price_per_night": "70.00",
                "capacity": 2,
                "size": "30",
                "bedrooms": "{\"1\": \"1 cama doble\", \"2\": \"1 sofa cama\"}",
                "bathrooms": 1,
                "images_div": "marlinc2",
                "tv": "TV de pantalla plana, Canales vía satélite, TV",
                "entertainment": 0,
                "parking": true,
                "pool": true,
                "garden": false,
                "safeBox": true,
                "terrace": true,
                "wifi": true,
                "lat": "28.9220760",
                "lng": "-13.6761950"
            }
        ]';

        $properties = json_decode($propertiesJson, true);

        // Insertar las propiedades en la base de datos
        foreach ($properties as $property) {
            Property::create([
                'id' => $property['id'],
                'title' => $property['title'],
                'description' => $property['description'],
                'location' => $property['location'],
                'price_per_night' => $property['price_per_night'],
                'capacity' => $property['capacity'],
                'size' => $property['size'],
                'bedrooms' => $property['bedrooms'],
                'bathrooms' => $property['bathrooms'],
                'images_div' => $property['images_div'],
                'tv' => $property['tv'],
                'entertainment' => $property['entertainment'],
                'parking' => $property['parking'],
                'pool' => $property['pool'],
                'garden' => $property['garden'],
                'safeBox' => $property['safeBox'],
                'terrace' => $property['terrace'],
                'wifi' => $property['wifi'],
                'lat' => $property['lat'],
                'lng' => $property['lng'],
            ]);
        }
    }
}
