<?php
require_once __DIR__ . '/../models/NacionalidadModel.php';

class NacionalidadController
{
    private NacionalidadModel $model;

    public function __construct()
    {
        $this->model = new NacionalidadModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Listar todas las nacionalidades.
    public function index()
    {
        $nacionalidades = $this->model->getAllNacionalidades();
        require __DIR__ . '/../views/nacionalidades/index.php';
    }

    // Mostrar una nacionalidad específica.
    public function show($id)
    {
        $nacionalidad = $this->model->getNacionalidad($id);
        if (!$nacionalidad) {
            $_SESSION['error'] = "Nacionalidad no encontrada.";
            header("Location: nacionalidades.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/nacionalidades/show.php';
    }

    // Mostrar formulario para crear nacionalidad.
    public function create()
    {
        require __DIR__ . '/../views/nacionalidades/create.php';
    }

    // Procesar creación de nacionalidad.
    public function store()
    {
        $nacionalidad = trim($_POST['nacionalidad'] ?? '');

        if ($nacionalidad === '') {
            $_SESSION['error'] = "El nombre de la nacionalidad no puede estar vacío.";
            header("Location: nacionalidades.php?action=create");
            exit();
        }

        $this->model->insertNacionalidad($nacionalidad);
        header("Location: nacionalidades.php?action=index");
        exit();
    }

    // Mostrar formulario para editar nacionalidad.
    public function edit($id)
    {
        $nacionalidad = $this->model->getNacionalidad($id);
        if (!$nacionalidad) {
            $_SESSION['error'] = "Nacionalidad no encontrada.";
            header("Location: nacionalidades.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/nacionalidades/edit.php';
    }

    // Procesar actualización de nacionalidad.
    public function update($id)
    {
        $nuevaNacionalidad = trim($_POST['nacionalidad'] ?? '');

        if ($nuevaNacionalidad === '') {
            $_SESSION['error'] = "El nombre de la nacionalidad no puede estar vacío.";
            header("Location: nacionalidades.php?action=edit&id=$id");
            exit();
        }

        $actualizado = $this->model->updateNacionalidad($id, $nuevaNacionalidad);

        if (!$actualizado) {
            $_SESSION['error'] = "No se pudo actualizar la nacionalidad o no hubo cambios.";
        }

        header("Location: nacionalidades.php?action=index");
        exit();
    }

    // Eliminar una nacionalidad.
    public function delete($id)
    {
        $eliminado = $this->model->deleteNacionalidad($id);

        if (!$eliminado) {
            $_SESSION['error'] = "No se pudo eliminar la nacionalidad o no existe.";
        }

        header("Location: nacionalidades.php?action=index");
        exit();
    }
}
