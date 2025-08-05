<?php
/**
 * Controlador para manejar las operaciones relacionadas con las calles recorridas.
 * Permite crear, editar, eliminar y listar las relaciones entre calles y recorridos.
 */
class CalleRecorrido extends Control
{
    private CalleRecorridoModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('CalleRecorridoModel');
    }

    /**
     * Muestra todos los registros.
     */
    public function index()
    {
        $callesRecorridos = $this->model->getAllCallesRecorridos();
        $this->load_view('calles_recorridos/index', ['callesRecorridos' => $callesRecorridos]);
    }

    /**
     * Muestra un registro por ID.
     */
    public function show($id)
    {
        $calleRecorrido = $this->model->getCalleRecorrido($id);

        if (!$calleRecorrido) {
            $this->load_view('calles_recorridos/index', [
                'error' => 'Registro no encontrado.',
                'callesRecorridos' => $this->model->getAllCallesRecorridos()
            ]);
            return;
        }

        $this->load_view('calles_recorridos/show', ['calleRecorrido' => $calleRecorrido]);
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        $this->load_view('calles_recorridos/create');
    }

    /**
     * Procesa la creación.
     */
    public function store()
    {
        if (empty($id_recorrido) || empty($id_calle)) {
            $this->load_view('calles_recorridos/create', [
                'error' => 'Todos los campos son obligatorios.'
            ]);
            return;
        }

        $this->model->insertCalleRecorrido($id_recorrido, $id_calle);

        $this->load_view('calles_recorridos/index', [
            'message' => 'Registro creado correctamente.',
            'callesRecorridos' => $this->model->getAllCallesRecorridos()
        ]);
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit($id)
    {
        $calleRecorrido = $this->model->getCalleRecorrido($id);

        if (!$calleRecorrido) {
            $this->load_view('calles_recorridos/index', [
                'error' => 'Relación no encontrada.',
                'callesRecorridos' => $this->model->getAllCallesRecorridos()
            ]);
            return;
        }

        $this->load_view('calles_recorridos/edit', ['calleRecorrido' => $calleRecorrido]);
    }

    /**
     * Procesa la edición.
     */
    public function update($id)
    {
        $id_recorrido = $_POST['id_recorrido'] ?? null;
        $id_calle = $_POST['id_calle'] ?? null;

        if (empty($id_recorrido) || empty($id_calle)) {
            $calleRecorrido = $this->model->getCalleRecorrido($id);
            $this->load_view('calles_recorridos/edit', [
                'error' => 'Todos los campos son obligatorios.',
                'calleRecorrido' => $calleRecorrido
            ]);
            return;
        }

        $this->model->updateCalleRecorrido($id, $id_recorrido, $id_calle);

        $this->load_view('calles_recorridos/index', [
            'message' => 'Registro actualizado correctamente.',
            'callesRecorridos' => $this->model->getAllCallesRecorridos()
        ]);
    }

    /**
     * Elimina un registro.
     */
    public function delete($id)
    {
        $eliminado = $this->model->deleteCalleRecorrido($id);

        $callesRecorridos = $this->model->getAllCallesRecorridos();

        if (!$eliminado) {
            $this->load_view('calles_recorridos/index', [
                'error' => 'No se pudo eliminar: el registro no existe.',
                'callesRecorridos' => $callesRecorridos
            ]);
            return;
        }

        $this->load_view('calles_recorridos/index', [
            'message' => 'Registro eliminado correctamente.',
            'callesRecorridos' => $callesRecorridos
        ]);
    }
}
