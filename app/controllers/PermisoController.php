<?php
session_start();
require_once __DIR__ . '/../models/PermisoModel.php';
/**
 * Controlador de la tabla `permisos`.
 */
class PermisoController
{
    private PermisoModel $model;

    public function __construct()
    {
        $this->model = new PermisoModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }  
    }

    // Mostrar todos los permisos
    public function index()
    {
        $permisos = $this->model->getAllPermisos();
        require __DIR__ . '/../views/permisos/index.php';
    }

    // Mostrar un permiso específico
    public function show($id)
    {
        $permiso = $this->model->getPermiso($id);
        if (!$permiso) {
            $_SESSION['error'] = "Permiso no encontrado";
            header('Location: /permisos');
            exit;
        }
        require __DIR__ . '/../views/permisos/show.php';
    }

    // Formulario de creación
    public function create()
    {
        require __DIR__ . '/../views/permisos/create.php';
    }

    // Guardar nuevo permiso
    public function store()
    {
        $data = $_POST;

        $id = $this->model->insertPermiso(
            $data['id_chofer'],
            $_SESSION['usuario']['id_usuario'],
            $data['id_servicio'],
            $data['tipo'],
            $data['fecha_reserva'],
            $data['fecha_emision'],
            isset($data['es_arribo']) ? 1 : 0,
            $data['observacion'] ?? null
        );

        if ($id) {
            $_SESSION['success'] = "Permiso creado con éxito";
            header('Location: /permisos');
        } else {
            $_SESSION['error'] = "Error al crear el permiso";
            header('Location: /permisos/create');
        }
    }

    // Formulario de edición
    public function edit($id)
    {
        $permiso = $this->model->getPermiso($id);
        if (!$permiso) {
            $_SESSION['error'] = "Permiso no encontrado";
            header('Location: /permisos');
            exit;
        }

        require __DIR__ . '/../views/permisos/edit.php';
    }

    // Actualizar permiso
    public function update($id)
    {
        $data = $_POST;

        $success = $this->model->updatePermiso(
            $id,
            $data['id_chofer'],
            $_SESSION['usuario']['id_usuario'],
            $data['id_servicio'],
            $data['tipo'],
            $data['fecha_reserva'],
            $data['fecha_emision'],
            isset($data['es_arribo']) ? 1 : 0,
            $data['observacion'] ?? null,
            isset($data['activo']) ? 1 : 0
        );

        if ($success) {
            $_SESSION['success'] = "Permiso actualizado correctamente";
        } else {
            $_SESSION['error'] = "No se pudo actualizar el permiso";
        }

        header('Location: /permisos');
    }

    // Desactivar (eliminar lógico) permiso
    public function delete($id)
    {
        $success = $this->model->deletePermiso($id);

        if ($success) {
            $_SESSION['success'] = "Permiso desactivado correctamente";
        } else {
            $_SESSION['error'] = "No se pudo desactivar el permiso";
        }

        header('Location: /permisos');
    }
}
