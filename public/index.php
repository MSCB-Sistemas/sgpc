<?php

  require_once __DIR__ . '/../app/models/CalleModel.php';
  require_once __DIR__ . '/../app/models/EmpresaModel.php';


// Habilita CORS si vas a probar con frontend
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Métodos permitidos para CORS (opcional)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit(0);
}

// Importa el controlador
require_once realpath(__DIR__ . '/../app/controllers/CalleController.php');


// Instancia del controlador
$controller = new CalleController();

// Detecta método HTTP y URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Ajusta el índice si estás en subcarpeta
// Ejemplo: http://localhost/calle-api/index.php/calles/1
// Entonces $uri = ['calle-api', 'index.php', 'calles', '1']
$pathIndex = array_search('index.php', $uri);
$path = array_slice($uri, $pathIndex + 1);

// Enrutamiento
if ($path[0] === 'calles') {
    // GET /calles o GET /calles/1
    if ($method === 'GET') {
        if (isset($path[1])) {
            $controller->getCalle($path[1]);
        } else {
            $controller->getAllCalles();
        }
    }

    // POST /calles
    elseif ($method === 'POST') {
        $controller->insertCalle();
    }

    // PUT /calles/1
    elseif ($method === 'PUT' && isset($path[1])) {
        $controller->updateCalle($path[1]);
    }

    // DELETE /calles/1
    elseif ($method === 'DELETE' && isset($path[1])) {
        $controller->deleteCalle($path[1]);
    }

    else {
        http_response_code(400);
        echo json_encode(['error' => 'Método o ruta no válida']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada']);
}

?>