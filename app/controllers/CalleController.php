<?php
require_once __DIR__ . '/../models/CalleModel.php';

/**
 *  Controlador de CalleModel.php
 */
class CalleController
{
    private CalleModel $model;

    public function __construct()
    {
        $this->model = new CalleModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }        
    }

    // Mostrar todas las calles en una vista.
    public function index()
    {
        $calles = $this->model->getAllCalles();
        require __DIR__ . '/../views/calles/index.php';
    }

    // Mostrar una calle específica.
    public function show($id_calle)
    {
        $calle = $this->model->getCalle($id_calle);
        if (!$calle) {
            $_SESSION['error'] = "Calle no encontrada.";
            header("Location: calles.php?action=index");
            exit();
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
            $_SESSION['error'] = "El nombre es obligatorio.";
            header("Location: calles.php?action=create");
            exit();
        }
        $id_calle = $this->model->insertCalle($_POST['nombre']);
        header("Location: calles.php?action=index");
        exit();
    }

    // Mostrar formulario para editar una calle.
    public function edit($id_calle)
    {
        $calle = $this->model->getCalle($id_calle);
        if (!$calle) {
            $_SESSION['error'] = "Calle no encontrada.";
            header("Location: calles.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/calles/edit.php';
    }

    // Procesar la actualización de calle.
    public function update($id_calle)
    {
        if (empty($_POST['nombre'])) {
            $_SESSION['error'] = "El nombre es obligatorio.";
            header("Location: calles.php?action=edit&id=$id_calle");
            exit();
        }
        $this->model->updateCalle($id_calle, $_POST['nombre']);
        header("Location: calles.php?action=index");
        exit();
    }

    // Eliminar una calle.
    public function delete($id_calle)
    {
        $this->model->deleteCalle($id_calle);
        header("Location: calles.php?action=index");
        exit();
    }
}
