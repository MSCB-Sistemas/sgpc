<?php
/**
 * Controlador para gestionar operaciones relacionadas con los permisos.
 */
class Permiso extends Control
{
    private PermisoModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('PermisoModel');
    }

    // Mostrar lista de permisos
    public function index()
    {
        $permisos = $this->model->getAllPermisos();
        $datos = [
        'title' => 'Listado de Permisos',
        'urlCrear' => URL . '/permisos/create',
        'columnas' => [
            'Tipo',
            'Fecha Reserva',
            'Fecha Emisión',
            'Chofer',
            'Nacionalidad',
            'Usuario',
            'Cargo',
            'Servicio',
            'Dominio',
            'Empresa',
            'Observación',
            'Arribo'
        ],
        'columnas_claves' => [
            'tipo',
            'fecha_reserva',
            'fecha_emision',
            'chofer',
            'chofer_nacionalidad',
            'usuario',
            'usuario_cargo',
            'servicio_interno',
            'servicio_dominio',
            'empresa_nombre',
            'observacion',
            'arribo'
        ],
        'data' => $permisos,
        'acciones' => function($fila) {
            $id = $fila['id_permiso'];
            $url = URL . '/permisos';
            return '
                <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este permiso?\');">Eliminar</a>
            ';
        }
    ];

$this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar detalles de un permiso específico
    public function show($id)
    {
        $permiso = $this->model->getPermiso($id);
        if (!$permiso) {
            $this->load_view('permisos/index', [
                'error' => 'Permiso no encontrado.',
                'permisos' => $this->model->getAllPermisos()
            ]);
            return;
        }
        $this->load_view('permisos/show', ['permiso' => $permiso]);
    }

    // Mostrar formulario para crear permiso
    public function create()
    {
        $this->load_view('permisos/create');
    }

    // Procesar creación
    public function store()
    {
        $id_chofer = $_POST['id_chofer'] ?? null;
        $id_usuario = $_POST['id_usuario'] ?? null;
        $id_servicio = $_POST['id_servicio'] ?? null;
        $tipo = $_POST['tipo'] ?? '';
        $fecha_reserva = $_POST['fecha_reserva'] ?? '';
        $fecha_emision = $_POST['fecha_emision'] ?? '';
        $es_arribo = isset($_POST['es_arribo']) ? 1 : 0;
        $observacion = $_POST['observacion'] ?? null;

        if (!$id_chofer || !$id_usuario || !$id_servicio || $tipo === '' || $fecha_reserva === '' || $fecha_emision === '') {
            $this->load_view('permisos/create', [
                'error' => 'Todos los campos obligatorios deben estar completos.',
                'data' => $_POST
            ]);
            return;
        }

        $this->model->insertPermiso(
            $id_chofer,
            $id_usuario,
            $id_servicio,
            $tipo,
            $fecha_reserva,
            $fecha_emision,
            $es_arribo,
            $observacion
        );

        $this->load_view('permisos/index', [
            'message' => 'Permiso creado correctamente.',
            'permisos' => $this->model->getAllPermisos()
        ]);
    }

    // Mostrar formulario para editar permiso
    public function edit($id)
    {
        $permiso = $this->model->getPermiso($id);
        if (!$permiso) {
            $this->load_view('permisos/index', [
                'error' => 'Permiso no encontrado.',
                'permisos' => $this->model->getAllPermisos()
            ]);
            return;
        }
        $this->load_view('permisos/edit', ['permiso' => $permiso]);
    }

    // Procesar actualización
    public function update($id)
    {
        $id_chofer = $_POST['id_chofer'] ?? null;
        $id_usuario = $_POST['id_usuario'] ?? null;
        $id_servicio = $_POST['id_servicio'] ?? null;
        $tipo = $_POST['tipo'] ?? '';
        $fecha_reserva = $_POST['fecha_reserva'] ?? '';
        $fecha_emision = $_POST['fecha_emision'] ?? '';
        $es_arribo = isset($_POST['es_arribo']) ? 1 : 0;
        $observacion = $_POST['observacion'] ?? null;
        $activo = isset($_POST['activo']) ? 1 : 0;

        if (!$id_chofer || !$id_usuario || !$id_servicio || $tipo === '' || $fecha_reserva === '' || $fecha_emision === '') {
            $permiso = $this->model->getPermiso($id);
            $this->load_view('permisos/edit', [
                'error' => 'Todos los campos obligatorios deben estar completos.',
                'permiso' => $permiso
            ]);
            return;
        }

        $this->model->updatePermiso(
            $id,
            $id_chofer,
            $id_usuario,
            $id_servicio,
            $tipo,
            $fecha_reserva,
            $fecha_emision,
            $es_arribo,
            $observacion,
            $activo
        );

        $this->load_view('permisos/index', [
            'message' => 'Permiso actualizado correctamente.',
            'permisos' => $this->model->getAllPermisos()
        ]);
    }

    // Desactivar permiso (activo = 0)
    public function delete($id)
    {
        $eliminado = $this->model->deletePermiso($id);
        $permisos = $this->model->getAllPermisos();

        if (!$eliminado) {
            $this->load_view('permisos/index', [
                'error' => 'No se pudo desactivar el permiso.',
                'permisos' => $permisos
            ]);
            return;
        }

        $this->load_view('permisos/index', [
            'message' => 'Permiso desactivado correctamente.',
            'permisos' => $permisos
        ]);
    }
}
