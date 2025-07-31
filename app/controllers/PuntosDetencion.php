<?php
/**
 * Controlador para manejar las operaciones relacionadas con los Puntos de Detención.
 */
class PuntosDetencion extends Control
{
    private PuntosDetencionModel $model;

    public function __construct()
    {
        $this->model = $this->load_model('PuntosDetencionModel');
    }

    // Mostrar todos los puntos de detención
    public function index()
    {
        $puntos = $this->model->getAllPuntosDetencion();
        $datos = [
            'title' => 'Listado de Puntos de Detencion',
            'urlCrear' => URL . '/puntosDetencion/create',
            'columnas' => ['Nombre del Punto', 'Calle'],  
            'columnas_claves' => ['nombre_punto', 'nombre_calle'], 
            'data' => $puntos,
            'acciones' => function($fila) {
                $id = $fila['id_punto_detencion'];
                $url = URL . '/puntosDetencion';
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este punto?\');">Eliminar</a>
                ';
            }
        ];

        $this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar un punto específico
    public function show($id)
    {
        $punto = $this->model->getPuntoDetencion($id);

        if (!$punto) {
            $this->load_view('puntos_detencion/index', [
                'error' => 'Punto de detención no encontrado.',
                'puntos' => $this->model->getAllPuntosDetencion()
            ]);
            return;
        }

        $this->load_view('puntos_detencion/show', ['punto' => $punto]);
    }

    // Formulario para crear un nuevo punto
    public function create()
    {
        $calles = $this->load_model('CallesModel')->getAllCalles();
        $this->load_view('puntos_detencion/create', ['calles' => $calles]);
    }

    // Procesar la creación
    public function store()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $id_calle = $_POST['id_calle'] ?? '';

        if ($nombre === '' || $id_calle === '') {
            $calles = $this->load_model('CallesModel')->getAllCalles();
            $this->load_view('puntos_detencion/create', [
                'error' => 'Todos los campos son obligatorios.',
                'nombre' => $nombre,
                'id_calle' => $id_calle,
                'calles' => $calles
            ]);
            return;
        }

        $this->model->insertPuntoDetencion($nombre, $id_calle);

        $this->load_view('puntos_detencion/index', [
            'message' => 'Punto de detención creado exitosamente.',
            'puntos' => $this->model->getAllPuntosDetencion()
        ]);
    }

    // Formulario para editar un punto
    public function edit($id)
    {
        $punto = $this->model->getPuntoDetencion($id);
        if (!$punto) {
            $this->load_view('puntos_detencion/index', [
                'error' => 'Punto de detención no encontrado.',
                'puntos' => $this->model->getAllPuntosDetencion()
            ]);
            return;
        }

        $calles = $this->load_model('CallesModel')->getAllCalles();
        $this->load_view('puntos_detencion/edit', [
            'punto' => $punto,
            'calles' => $calles
        ]);
    }

    // Procesar actualización
    public function update($id)
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $id_calle = $_POST['id_calle'] ?? '';

        if ($nombre === '' || $id_calle === '') {
            $punto = $this->model->getPuntoDetencion($id);
            $calles = $this->load_model('CallesModel')->getAllCalles();

            $this->load_view('puntos_detencion/edit', [
                'error' => 'Todos los campos son obligatorios.',
                'punto' => $punto,
                'calles' => $calles
            ]);
            return;
        }

        $this->model->updatePuntoDetencion($id, $nombre, $id_calle);

        $this->load_view('puntos_detencion/index', [
            'message' => 'Punto de detención actualizado correctamente.',
            'puntos' => $this->model->getAllPuntosDetencion()
        ]);
    }

    // Eliminar un punto
    public function delete($id)
    {
        $eliminado = $this->model->deletePuntoDetencion($id);
        $puntos = $this->model->getAllPuntosDetencion();

        if (!$eliminado) {
            $this->load_view('puntos_detencion/index', [
                'error' => 'No se pudo eliminar el punto.',
                'puntos' => $puntos
            ]);
            return;
        }

        $this->load_view('puntos_detencion/index', [
            'message' => 'Punto de detención eliminado correctamente.',
            'puntos' => $puntos
        ]);
    }
}
