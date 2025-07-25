<?php
require_once __DIR__ . '/../models/CalleModel.php';
/**
 *  Controlador de CalleModel.php
 * 
 */
class CalleController
{
    private CalleModel $model;

    public function __construct()
    {
        $this->model = new CalleModel();
    }

    // Mostrar todas las calles en una vista.
    public function index()
    {
        $calles = $this->model->getAllCalles();
        // Muestreo de datos.
        require __DIR__ . '/../views/calles/index.php';
    }

    // Mostrar una calle específica.
    public function show($id)
    {
        $calle = $this->model->getCalle($id);
        if (!$calle) {
            // En caso de no encontrar la calle, imprime el error y se sale de la funcion.
            echo "Calle no encontrada";
            return;
        }
        require __DIR__ . '/../views/calles/show.php';
    }

    // Mostrar formulario para crear una calle nueva.
    public function create()
    {
        require __DIR__ . '/../views/calles/create.php';
    }

    // Procesar el formulario para guardar calle nueva.
    public function store()
    {
        if (empty($_POST['nombre'])) {
            // Si el nombre esta vacio imprime el mensaje y sale de la funcion.
            echo "El nombre es obligatorio";
            return;
        }
        $id_calle = $this->model->insertCalle($_POST['nombre']);
        // Redirigir a la lista o mostrar mensaje
        header("Location: /calles"); // ejemplo ruta
        exit();
    }

    // Mostrar formulario para editar una calle.
    public function edit($id_calle)
    {
        $calle = $this->model->getCalle($id_calle);
        if (!$calle) {
            echo "Calle no encontrada";
            return;
        }
        require __DIR__ . '/../views/calles/edit.php';
    }

    // Procesar la actualización de calle.
    public function update($id_calle)
    {
        // Condicional en caso de que este vacio.
        if (empty($_POST['nombre'])) {
            echo "El nombre es obligatorio";
            return;
        }
        $this->model->updateCalle($id_calle, $_POST['nombre']);
        header("Location: /calles");
        exit();
    }

    // Eliminar una calle
    public function delete($id_calle)
    {
        $this->model->deleteCalle($id_calle);
        header("Location: /calles");
        exit();
    }
}
