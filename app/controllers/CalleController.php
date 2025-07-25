<?php
require_once __DIR__ . '/../models/CalleModel.php';
/**
 * Controller de CalleModel.php.
 */
class CalleController
{
    private CalleModel $model;

    public function __construct() // Constructor.
    {
        $this->model = new CalleModel();
    }

    // Obtiene todas las calles
    public function getAllCalles()
    {
        $calles = $this->model->getAllCalles();
        // Establece la respuesta en modo JSON.
        header('Content-Type: application/json');
        // Imprime como respuesta del servidor el contenido de $calles.
        echo json_encode($calles);
    }


    // Obtener una sola calle por ID
    public function getCalle($id)
    {
        $calle = $this->model->getCalle($id);
        // Establece la respuesta en modo JSON.
        header('Content-Type: application/json');
        // Imprime como respuesta del servidor el contenido de $calle.
        echo json_encode($calle);
    }


    // Insertar una nueva calle
    public function insertCalle()
    {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['nombre'])) {
            // Codigo del estado del http del cliente (400 = BadRequest).
            http_response_code(400);
            // Mensaje de respuesta del servidor.
            echo json_encode(['error' => 'El nombre de la calle es obligatorio']);
            return;
        }

        $id = $this->model->insertCalle($input['nombre']);
        // Imprime el mensaje y el valor del id nuevo .
        echo json_encode(['id_insertado' => $id]);
    }


    // Actualizar una calle existente
    public function updateCalle($id)
    {
        // lee todo el contenido de la peticion con el file_get_contents y crea un array asociativo
        // indicando con true que sea array y no objeto.
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['nombre'])) {
            // Codigo del estado del http del cliente (400 = BadRequest).
            http_response_code(400);
            // Crea un array asociativo, con el valor de la cadena y lo devuelve como respuesta del servidor.
            echo json_encode(['error' => 'El nombre de la calle es obligatorio']);
            // Termina la ejecucion.
            return;
        }

        $resultado = $this->model->updateCalle($id, $input['nombre']);
        // devuelve el mensaje como respuesta del servidor y el valor de $resultado.
        echo json_encode(['actualizado' => $resultado]);
    }

    // Eliminar una calle
    public function deleteCalle($id)
    {
        $resultado = $this->model->deleteCalle($id);
        // imprime el mensaje "eliminado" y el valor de $resultado(de tipo bool).
        echo json_encode(['eliminado' => $resultado]);
    }
}
