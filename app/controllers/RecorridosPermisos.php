<?php
/**
 * Controlador para gestionar operaciones sobre recorridos_permisos.
 */
class RecorridosPermisos extends Control
{
    private RecorridosPermisosModel $model;

    public function __construct()
    {
        $this->model = $this->load_model('RecorridosPermisosModel');

    }

    // Mostrar todos los recorridos_permisos
    public function index()
    {
        $recorridosPermisos = $this->model->getAllRecorridosPermisos();
        $datos = [
            'title' => 'Recorridos por Permiso',
            'urlCrear' => URL . '/recorridosPermisos/create',
            'columnas' => ['Permiso', 'Recorrido'],
            'columnas_claves' => ['id_permiso', 'recorrido'],
            'data' => $recorridosPermisos,
            'acciones' => function($fila) {
                $id = $fila['id_recorrido_permiso'];
                $url = URL . '/recorridosPermisos';
                return;
            }
        ];
        $this->load_view('partials/tablaAbm', $datos);

    }

    // Mostrar detalles de un recorrido_permiso específico
    public function show($id)
    {
        $recorridoPermiso = $this->model->getRecorridoPermiso($id);

        if (!$recorridoPermiso) {
            $this->load_view('recorridos_permisos/index', [
                'error' => 'Recorrido-Permiso no encontrado.',
                'recorridos_permisos' => $this->model->getAllRecorridosPermisos()
            ]);
            return;
        }

        $this->load_view('recorridos_permisos/show', [
            'recorrido_permiso' => $recorridoPermiso
        ]);
    }

    // Formulario para crear un nuevo recorrido_permiso
    public function create()
    {
        $this->load_view('recorridos_permisos/create');
    }

    // Procesar creación
    public function store()
    {
        $id_permiso = $_POST['id_permiso'] ?? null;
        $id_recorrido = $_POST['id_recorrido'] ?? null;

        if (!$id_permiso || !$id_recorrido) {
            $this->load_view('recorridos_permisos/create', [
                'error' => 'Todos los campos son obligatorios.',
                'data' => $_POST
            ]);
            return;
        }

        $this->model->insertRecorrido($id_permiso, $id_recorrido);

        $this->load_view('recorridos_permisos/index', [
            'message' => 'Recorrido-Permiso creado correctamente.',
            'recorridos_permisos' => $this->model->getAllRecorridosPermisos()
        ]);
    }

    // Formulario para editar recorrido_permiso
    public function edit($id)
    {
        $recorridoPermiso = $this->model->getRecorridoPermiso($id);

        if (!$recorridoPermiso) {
            $this->load_view('recorridos_permisos/index', [
                'error' => 'Recorrido-Permiso no encontrado.',
                'recorridos_permisos' => $this->model->getAllRecorridosPermisos()
            ]);
            return;
        }

        $this->load_view('recorridos_permisos/edit', [
            'recorrido_permiso' => $recorridoPermiso
        ]);
    }

    // Procesar actualización
    public function update($id)
    {
        $id_permiso = $_POST['id_permiso'] ?? null;
        $id_recorrido = $_POST['id_recorrido'] ?? null;

        if (!$id_permiso || !$id_recorrido) {
            $recorridoPermiso = $this->model->getRecorridoPermiso($id);
            $this->load_view('recorridos_permisos/edit', [
                'error' => 'Todos los campos son obligatorios.',
                'recorrido_permiso' => $recorridoPermiso
            ]);
            return;
        }

        $this->model->updateRecorrido($id, $id_permiso, $id_recorrido);

        $this->load_view('recorridos_permisos/index', [
            'message' => 'Recorrido-Permiso actualizado correctamente.',
            'recorridos_permisos' => $this->model->getAllRecorridosPermisos()
        ]);
    }

    // Eliminar recorrido_permiso
    public function delete($id)
    {
        $eliminado = $this->model->deleteRecorrido($id);
        $recorridosPermisos = $this->model->getAllRecorridosPermisos();

        if (!$eliminado) {
            $this->load_view('recorridos_permisos/index', [
                'error' => 'No se pudo eliminar el recorrido_permiso.',
                'recorridos_permisos' => $recorridosPermisos
            ]);
            return;
        }

        $this->load_view('recorridos_permisos/index', [
            'message' => 'Recorrido-Permiso eliminado correctamente.',
            'recorridos_permisos' => $recorridosPermisos
        ]);
    }
}
