<?php

namespace Database\Seeders;

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
                "description": "Casa Delfín Playa Blanca está en Playa Blanca, a 17 min a pie de Playa Dorada y a 1,6 km de Playa Blanca, y ofrece aire acondicionado. Esta villa tiene piscina privada, jardín, zona de barbacoa, wifi gratis y parking privado gratis. Esta villa dispone de una terraza con vistas a la piscina y también incluye TV de pantalla plana vía satélite, una cocina bien equipada con nevera, lavavajillas y horno, y 2 baños con ducha y secador de pelo. En la villa, la clientela puede disfrutar de bañera de hidromasaje. La clientela de este alojamiento puede practicar senderismo en los alrededores o disfrutar de la piscina al aire libre abierta todo el año. Parque Nacional de Timanfaya está a 20 km del alojamiento, y Montañas de Fuego está a 21 km. El aeropuerto (Aeropuerto de Lanzarote) está a 28 km.",
                "location": "23 Calle Bonilla, 35580 Playa Blanca, Spain",
                "price_per_night": "135.00",
                "capacity": 6,
                "size": "150",
                "bedrooms": "{\"1\": \"1 large double bed\", \"2\": \"1 double bed\", \"3\": \"2 single beds\"}",
                "bathrooms": 2,
                "min_nights": 5,
                "images_div": "delfin",
                "tv": "Flat-screen TV, Cable channels, Satellite channels, DVD player, TV",
                "entertainment": true,
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
                "description": "El Galeón está en Playa Blanca, a 6 min a pie de Playa Blanca y a 900 metros de Playa Flamingo, y ofrece alojamiento con aire acondicionado, balcón y wifi gratis. El alojamiento ofrece vistas al mar y a la montaña y está a 13 min a pie de Playa Dorada. El apartamento tiene 1 dormitorio, 1 baño, ropa de cama, toallas, TV de pantalla plana con canales vía satélite, zona de comedor, cocina totalmente equipada y terraza con vistas a la ciudad. El apartamento ofrece barbacoa. Hay servicio de alquiler de bicicletas y servicio de alquiler de coches en El Galeón. Parque Nacional de Timanfaya está a 20 km del alojamiento, y Montañas de Fuego está a 22 km. El aeropuerto más cercano (Aeropuerto de Lanzarote) está a 30 km.",
                "location": "Calle Chalana, 14., 35580 Playa Blanca, Spain",
                "price_per_night": "90.00",
                "capacity": 2,
                "size": "60",
                "bedrooms": "{\"1\": \"1 double bed\"}",
                "bathrooms": 1,
                "min_nights": 3,
                "images_div": "galeon",
                "tv": "Flat-screen TV, Cable channels, Satellite channels, TV",
                "entertainment": false,
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
                "description": "El Marlin I Puerto del Carmen ofrece alojamiento con WiFi gratuita, aire acondicionado y piscina con vistas en Puerto del Carmen, a menos de 1 km de Playa Chica y a 13 minutos a pie de la playa de Puerto del Carmen.El establecimiento tiene vistas a la piscina y está a 3,4 km del complejo de golf Lanzarote Golf Resort y a 4,4 km del Rancho Texas Park. El apartamento también dispone de zona de estar al aire libre. El apartamento cuenta con 1 dormitorio, 1 baño, ropa de cama, toallas, TV de pantalla plana con canales vía satélite, cocina totalmente equipada y terraza con vistas al mar. Una entrada privada conduce a los huéspedes al apartamento, donde pueden disfrutar de vino o champán y frutas. Este apartamento es para no fumadores y está insonorizado. Se pueden realizar visitas turísticas cerca del establecimiento. El Marlin I Puerto del Carmen está a 14 km del monumento al Campesino y a 18 km de las Montañas de Fuego. El aeropuerto de Lanzarote está a 7 km.",
                "location": "Calle Teide 43 - Aptos Rincon C1, 35518 Puerto del Carmen, España",
                "price_per_night": "80.00",
                "capacity": 2,
                "size": "51",
                "bedrooms": "{\"1\": \"1 cama doble\"}",
                "bathrooms": 1,
                "min_nights": 3,
                "images_div": "marlinc1",
                "tv": "TV de pantalla plana, Canales vía satélite, TV",
                "entertainment": false,
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
                "description": "El Marlin II Puerto del Carmen ofrece alojamiento con aire acondicionado, piscina con vistas al mar y terraza en Puerto del Carmen. El establecimiento tiene vistas a la piscina y está a menos de 1 km de Playa Chica y a 14 minutos a pie de la playa de Puerto del Carmen. Este apartamento para no fumadores ofrece WiFi gratuita en todas las instalaciones y solárium. El apartamento está situado en la planta baja y dispone de 1 dormitorio, TV de pantalla plana con canales vía satélite y cocina totalmente equipada con microondas, tostadora, lavadora, nevera y utensilios de cocina. Se proporcionan toallas y ropa de cama en el apartamento. El establecimiento tiene zona de comedor al aire libre. El apartamento ofrece servicio de alquiler de bicicletas y coches. El complejo de golf Lanzarote se encuentra a 3,4 km del Marlin II Puerto del Carmen, mientras que el Rancho Texas Park está a 4,4 km. El aeropuerto de Lanzarote está a 7 km.",
                "location": "Calle Teide 43 - Aptos Rincon C2, 35518 Puerto del Carmen, España",
                "price_per_night": "70.00",
                "capacity": 2,
                "size": "30",
                "bedrooms": "{\"1\": \"1 cama doble\", \"2\": \"1 sofa cama\"}",
                "bathrooms": 1,
                "min_nights": 3,
                "images_div": "marlinc2",
                "tv": "TV de pantalla plana, Canales vía satélite, TV",
                "entertainment": false,
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
        if (is_array($properties)) {
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
                    'min_nights' => $property['min_nights'],
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
        }else{
            echo "Error al decodificar el JSON de propiedades.";
        }
    }
}
