<?php
/**
 * Controlador para manejar las operaciones relacionadas con los Puntos de Detención.
 */
class PuntosDetencion extends Control
{
    private $model;
    private $calleModel;

    public function __construct()
    {
        $this->model = $this->load_model('PuntosDetencionModel');
        $this->calleModel = $this->load_model('CalleModel');
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

    // Formulario para crear un nuevo punto
    public function create()
    {
        $calles = $this->calleModel->getAllCalles();
        $datos = [
            'title' => 'Crear nuevo punto de detención',
            'action' => URL . '/puntosDetencion/save',
            'values' => [],
            'errores' => [],
            'calles' => $calles
        ];
        $this->load_view('puntos_detencion/form', $datos);
    }

    // Procesar la creación
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $calle = $_POST['calle'] ?? '';

            $errores = [];
            if(empty($nombre)) { $errores[] = 'El nombre del punto es obligatorio.'; }
            if(empty($calles)) { $errores[] = 'Debe seleccionar una calle.'; }

            if (!empty($errores)) {
                $calles = $this->calleModel->getAllCalles();
                $this->load_view('puntos_detencion/form', [
                    'title' => 'Crear nuevo punto de detención',
                    'action' => URL . '/puntosDetencion/save',
                    'values' => $_POST,
                    'errores' => $errores,
                    'calles' => $calles
                ]);

                return;
            }

            if ($this->model->insertPuntoDetencion($nombre, $calle)) {
                header("Location: " . URL . "/puntosDetencion");
                exit;
            } else {
                die("Error al insertar el punto de detención.");
            }
        }
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
