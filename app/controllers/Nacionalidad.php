<?php

class Nacionalidad extends Control
{
    private NacionalidadModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model("NacionalidadModel");
    }

    // Listar todas las nacionalidades.
    public function index()
    {
        $nacionalidades = $this->model->getAllNacionalidades();
        $this->load_view('nacionalidades/index', ['nacionalidades' => $nacionalidades]);
    }

    // Mostrar una nacionalidad específica.
    public function show($id)
    {
        $nacionalidad = $this->model->getNacionalidad($id);
        if (!$nacionalidad) {
            $nacionalidades = $this->model->getAllNacionalidades();
            $this->load_view('nacionalidades/index', [
                'error' => 'Nacionalidad no encontrada.',
                'nacionalidades' => $nacionalidades
            ]);
            return;
        }
        $this->load_view('nacionalidades/show', ['nacionalidad' => $nacionalidad]);
    }

    // Mostrar formulario para crear nacionalidad.
    public function create()
    {
        $this->load_view('nacionalidades/create');
    }

    // Procesar creación de nacionalidad.
    public function store()
    {
        $nacionalidad = trim($_POST['nacionalidad']);

        if (empty($nacionalidad)) {
            $this->load_view('nacionalidades/create', [
                'error' => 'El nombre de la nacionalidad no puede estar vacío.'
            ]);
            return;
        }

        $this->model->insertNacionalidad($nacionalidad);

        $nacionalidades = $this->model->getAllNacionalidades();
        $this->load_view('nacionalidades/index', [
            'message' => 'Nacionalidad creada correctamente.',
            'nacionalidades' => $nacionalidades
        ]);
    }

    // Mostrar formulario para editar nacionalidad.
    public function edit($id)
    {
        $nacionalidad = $this->model->getNacionalidad($id);
        if (!$nacionalidad) {
            $nacionalidades = $this->model->getAllNacionalidades();
            $this->load_view('nacionalidades/index', [
                'error' => 'Nacionalidad no encontrada.',
                'nacionalidades' => $nacionalidades
            ]);
            return;
        }
        $this->load_view('nacionalidades/edit', ['nacionalidad' => $nacionalidad]);
    }

    // Procesar actualización de nacionalidad.
    public function update($id)
    {
        $nuevaNacionalidad = trim($_POST['nacionalidad']);

        if (empty($nuevaNacionalidad)) {
            $nacionalidad = $this->model->getNacionalidad($id);
            $this->load_view('nacionalidades/edit', [
                'error' => 'El nombre de la nacionalidad no puede estar vacío.',
                'nacionalidad' => $nacionalidad
            ]);
            return;
        }

        $actualizado = $this->model->updateNacionalidad($id, $nuevaNacionalidad);

        $nacionalidades = $this->model->getAllNacionalidades();

        if (!$actualizado) {
            $this->load_view('nacionalidades/index', [
                'error' => 'No se pudo actualizar la nacionalidad o no hubo cambios.',
                'nacionalidades' => $nacionalidades
            ]);
            return;
        }

        $this->load_view('nacionalidades/index', [
            'message' => 'Nacionalidad actualizada correctamente.',
            'nacionalidades' => $nacionalidades
        ]);
    }

    // Eliminar una nacionalidad.
    public function delete($id)
    {
        $eliminado = $this->model->deleteNacionalidad($id);
        $nacionalidades = $this->model->getAllNacionalidades();

        if (!$eliminado) {
            $this->load_view('nacionalidades/index', [
                'error' => 'No se pudo eliminar la nacionalidad o no existe.',
                'nacionalidades' => $nacionalidades
            ]);
            return;
        }

        $this->load_view('nacionalidades/index', [
            'message' => 'Nacionalidad eliminada correctamente.',
            'nacionalidades' => $nacionalidades
        ]);
    }
}
