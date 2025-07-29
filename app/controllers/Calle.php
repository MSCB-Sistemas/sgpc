<?php

/**
 * Controlador de CalleModel.php
 */
class Calle extends Control
{
    private CalleModel $model;

    public function __construct()
    {
        $this->model = $this->load_model('CalleModel');
    }

    // Mostrar todas las calles en una vista.
    public function index()
    {
        $calles = $this->model->getAllCalles();
        $this->load_view('calles/index', ['calles' => $calles]);
    }

    // Mostrar una calle específica.
    public function show($id_calle)
    {
        $calle = $this->model->getCalle($id_calle);

        if (!$calle) {
            $calles = $this->model->getAllCalles();
            $this->load_view('calles/index', [
                'error' => 'Calle no encontrada.',
                'calles' => $calles
            ]);
            return;
        }

        $this->load_view('calles/show', ['calle' => $calle]);
    }

    // Mostrar formulario para crear una calle nueva.
    public function create()
    {
        $this->load_view('calles/create');
    }

    // Procesar el formulario para guardar calle nueva.
    public function store()
    {
        if (empty($_POST['nombre'])) {
            $this->load_view('calles/create', [
                'error' => 'El nombre es obligatorio.'
            ]);
            return;
        }

        $this->model->insertCalle($_POST['nombre']);
        $calles = $this->model->getAllCalles();
        $this->load_view('calles/index', [
            'message' => 'Calle creada correctamente.',
            'calles' => $calles
        ]);
    }

    // Mostrar formulario para editar una calle.
    public function edit($id_calle)
    {
        $calle = $this->model->getCalle($id_calle);
        if (!$calle) {
            $calles = $this->model->getAllCalles();
            $this->load_view('calles/index', [
                'error' => 'Calle no encontrada.',
                'calles' => $calles
            ]);
            return;
        }
        $this->load_view('calles/edit', ['calle' => $calle]);
    }

    // Procesar la actualización de calle.
    public function update($id_calle)
    {
        if (empty($_POST['nombre'])) {
            $calle = $this->model->getCalle($id_calle);
            $this->load_view('calles/edit', [
                'error' => 'El nombre es obligatorio.',
                'calle' => $calle
            ]);
            return;
        }

        $this->model->updateCalle($id_calle, $_POST['nombre']);
        $calles = $this->model->getAllCalles();
        $this->load_view('calles/index', [
            'message' => 'Calle actualizada correctamente.',
            'calles' => $calles
        ]);
    }

    // Eliminar una calle.
    public function delete($id_calle)
    {
        $this->model->deleteCalle($id_calle);
        $calles = $this->model->getAllCalles();
        $this->load_view('calles/index', [
            'message' => 'Calle eliminada correctamente.',
            'calles' => $calles
        ]);
    }
}
