<?php
/**
 * Controlador para manejar las operaciones relacionadas con los recorridos.
 */
class Recorrido extends Control
{
    private RecorridoModel $model;

    public function __construct()
    {
        $this->model = $this->load_model("RecorridoModel");   
             
    }

    // Mostrar todos los recorridos
    public function index()
    {
        $recorridos = $this->model->getAllRecorridos();
        $this->load_view('recorridos/index', ['recorridos' => $recorridos]);
    }

    // Mostrar un recorrido específico
    public function show($id)
    {
        $recorrido = $this->model->getRecorrido($id);

        if (!$recorrido) {
            $this->load_view('recorridos/index', [
                'error' => 'Recorrido no encontrado.',
                'recorridos' => $this->model->getAllRecorridos()
            ]);
            return;
        }

        $this->load_view('recorridos/show', ['recorrido' => $recorrido]);
    }

    // Mostrar formulario de creación
    public function create()
    {
        $this->load_view('recorridos/create');
    }

    // Procesar creación
    public function store()
    {
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            $this->load_view('recorridos/create', [
                'error' => 'El nombre es obligatorio.'
            ]);
            return;
        }

        $this->model->insertRecorrido($nombre);

        $this->load_view('recorridos/index', [
            'message' => 'Recorrido creado exitosamente.',
            'recorridos' => $this->model->getAllRecorridos()
        ]);
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $recorrido = $this->model->getRecorrido($id);

        if (!$recorrido) {
            $this->load_view('recorridos/index', [
                'error' => 'Recorrido no encontrado.',
                'recorridos' => $this->model->getAllRecorridos()
            ]);
            return;
        }

        $this->load_view('recorridos/edit', ['recorrido' => $recorrido]);
    }

    // Procesar edición
    public function update($id)
    {
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            $recorrido = $this->model->getRecorrido($id);
            $this->load_view('recorridos/edit', [
                'error' => 'El nombre es obligatorio.',
                'recorrido' => $recorrido
            ]);
            return;
        }

        $this->model->updateRecorrido($id, $nombre);

        $this->load_view('recorridos/index', [
            'message' => 'Recorrido actualizado correctamente.',
            'recorridos' => $this->model->getAllRecorridos()
        ]);
    }

    // Eliminar un recorrido
    public function delete($id)
    {
        $eliminado = $this->model->deleteRecorrido($id);

        $recorridos = $this->model->getAllRecorridos();

        if (!$eliminado) {
            $this->load_view('recorridos/index', [
                'error' => 'No se pudo eliminar el recorrido.',
                'recorridos' => $recorridos
            ]);
            return;
        }

        $this->load_view('recorridos/index', [
            'message' => 'Recorrido eliminado correctamente.',
            'recorridos' => $recorridos
        ]);
    }
}
