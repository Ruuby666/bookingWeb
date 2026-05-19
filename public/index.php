<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determina si la aplicación está en modo de mantenimiento...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Registra el autoload de Composer...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap de Laravel y maneja la solicitud...
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Maneja la solicitud y genera la respuesta
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture(),
);

// Envía la respuesta al navegador
$response->send();

// Termina el ciclo de vida de la solicitud
$kernel->terminate($request, $response);
