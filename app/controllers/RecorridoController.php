<?php
require_once __DIR__ . '/../models/RecorridoModel.php';

/**
 *  Controlador de RecorridoModel.php
 */
class RecorridoController
{
    private RecorridoModel $model;

    public function __construct()
    {
        $this->model = new RecorridoModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }        
    }

    // Mostrar todos los recorridos en una vista.
    public function index()
    {
        $calles = $this->model->getAllRecorridos();
        require __DIR__ . '/../views/recorridos/index.php';
    }

    // Mostrar un recorrido específico.
    public function show($id)
    {
        $recorrido = $this->model->getRecorrido($id);
        if (!$recorrido) {
            $_SESSION['error'] = "Recorrido no encontrado.";
            header("Location: recorridos.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/recorridos/show.php';
    }

    // Mostrar formulario para crear un recorrido nuevo
    public function create()
    {
        require __DIR__ . '/../views/recorridos/create.php';
    }

    // Procesar el formulario para guardar un recorrido nuevo.
    public function store()
    {
        if (empty($_POST['nombre'])) {
            $_SESSION['error'] = "El nombre es obligatorio.";
            header("Location: recorridos.php?action=create");
            exit();
        }
        $id_recorrido = $this->model->insertRecorrido($_POST['nombre']);
        header("Location: recorrido.php?action=index");
        exit();
    }

    // Mostrar formulario para editar un recorrido.
    public function edit($id)
    {
        $recorrido = $this->model->getRecorrido($id);
        if (!$recorrido) {
            $_SESSION['error'] = "Recorrido no encontrado.";
            header("Location: recorridos.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/recorridos/edit.php';
    }

    // Procesar la actualización de recorrido.
    public function update($id)
    {
        if (empty($_POST['nombre'])) {
            $_SESSION['error'] = "El nombre es obligatorio.";
            header("Location: recorridos.php?action=edit&id=$id");
            exit();
        }
        $this->model->updateRecorrido($id, $_POST['nombre']);
        header("Location: recorridos.php?action=index");
        exit();
    }

    // Eliminar un recorrido.
    public function delete($id)
    {
        $this->model->deleteRecorrido($id);
        header("Location: recorrido.php?action=index");
        exit();
    }
}
