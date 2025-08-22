<?php
/**
 * Controlador para manejar las operaciones relacionadas con los Tipos de Usuario.
 */
class TiposUsuarios extends Control
{
    private TiposUsuariosModel $model;

    public function __construct()
    {
        $this->requireLogin();
        // Cargar el modelo desde la clase base Control
        $this->model = $this->load_model('TiposUsuariosModel');
    }

    // Mostrar todos los tipos de usuario
    public function index()
    {
        $tiposUsuarios = $this->model->getAllTiposUsuarios();
        $this->load_view('tipos_usuarios/index', ['tiposUsuarios' => $tiposUsuarios]);
    }

    // Mostrar un tipo de usuario
    public function show($id)
    {
        $tipoUsuario = $this->model->getTipoUsuario($id);

        if (!$tipoUsuario) {
            $this->load_view('tipos_usuarios/index', [
                'error' => 'Tipo de usuario no encontrado.',
                'tiposUsuarios' => $this->model->getAllTiposUsuarios()
            ]);
            return;
        }

        $this->load_view('tipos_usuarios/show', ['tipoUsuario' => $tipoUsuario]);
    }

    // Formulario de creación
    public function create()
    {
        $this->load_view('tipos_usuarios/create');
    }

    // Procesar creación
    public function store()
    {
        if(isset($_POST['tipo_usuario'])) {
            $tipo_usuario = trim($_POST['tipo_usuario']);
        } else {
            $tipo_usuario = '';
        }

        if ($tipo_usuario === '') {
            $this->load_view('tipos_usuarios/create', [
                'error' => 'El nombre del tipo de usuario es obligatorio.',
                'tipo_usuario' => $tipo_usuario
            ]);
            return;
        }

        $this->model->insertTipoUsuario($tipo_usuario);

        $this->load_view('tipos_usuarios/index', [
            'message' => 'Tipo de usuario creado exitosamente.',
            'tiposUsuarios' => $this->model->getAllTiposUsuarios()
        ]);
    }

    // Formulario de edición
    public function edit($id)
    {
        $tipoUsuario = $this->model->getTipoUsuario($id);

        if (!$tipoUsuario) {
            $this->load_view('tipos_usuarios/index', [
                'error' => 'Tipo de usuario no encontrado.',
                'tiposUsuarios' => $this->model->getAllTiposUsuarios()
            ]);
            return;
        }

        $this->load_view('tipos_usuarios/edit', ['tipoUsuario' => $tipoUsuario]);
    }

    // Procesar actualización
    public function update($id)
    {
        if(isset($_POST['tipo_usuario'])) {
            $tipo_usuario = trim($_POST['tipo_usuario']);
        } else {
            $tipo_usuario = '';
        }

        if ($tipo_usuario === '') {
            $tipoUsuario = $this->model->getTipoUsuario($id);

            $this->load_view('tipos_usuarios/edit', [
                'error' => 'El campo es obligatorio.',
                'tipoUsuario' => $tipoUsuario
            ]);
            return;
        }

        $this->model->updateTipoUsuario($id, $tipo_usuario);

        $this->load_view('tipos_usuarios/index', [
            'message' => 'Tipo de usuario actualizado correctamente.',
            'tiposUsuarios' => $this->model->getAllTiposUsuarios()
        ]);
    }

    // Eliminar un tipo de usuario
    public function delete($id)
    {
        $eliminado = $this->model->deleteTipoUsuario($id);
        $tiposUsuarios = $this->model->getAllTiposUsuarios();

        if (!$eliminado) {
            $this->load_view('tipos_usuarios/index', [
                'error' => 'No se pudo eliminar: el tipo de usuario no existe.',
                'tiposUsuarios' => $tiposUsuarios
            ]);
            return;
        }

        $this->load_view('tipos_usuarios/index', [
            'message' => 'Tipo de usuario eliminado correctamente.',
            'tiposUsuarios' => $tiposUsuarios
        ]);
    }
}
