<?php
/**
 * Controlador para manejar las operaciones relacionadas con los Puntos de Detención.
 */
class PuntosDetencion extends Control
{
    private PuntosDetencionModel $model;
    private $calleModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('PuntosDetencionModel');
        $this->calleModel = $this->load_model('CalleModel');
    }

    // Mostrar todos los puntos de detención
    public function index($errores = null)
    {
        $puntos = $this->model->getAllPuntosDetencion();
        $datos = [
            'title' => 'Listado de Puntos de Detencion',
            'urlCrear' => URL . '/puntosDetencion/create',
            'columnas' => ['Punto', 'Calle'],  
            'columnas_claves' => ['nombre_punto', 'nombre_calle'], 
            'data' => $puntos,
            'acciones' => function($fila) {
                $id = $fila['id_punto_detencion'];
                $url = URL . '/puntosDetencion';
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este punto?\');">Eliminar</a>
                ';
            },
            'errores' => $errores
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
        $calles = $this->calleModel->getAllCalles();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"]);
            $calle = $_POST["calle"];

            $errores = [];
            if(empty($nombre)) { $errores[] = 'El nombre del punto es obligatorio.'; }
            if(empty($calle)) { $errores[] = 'Debe seleccionar una calle.'; }

            if (!empty($errores)) {
                $calles = $this->calleModel->getAllCalles();
                $this->load_view('puntos_detencion/form', [
                    'title' => 'Crear nuevo punto de detención',
                    'action' => URL . '/puntosDetencion/guardar',
                    'values' => $_POST,
                    'errores' => $errores,
                    'calles' => $calles
                ]);

                return;
            }
            try {
                if ($this->model->insertPuntoDetencion($nombre, $calle)) {
                    header("Location: " . URL . "/puntosDetencion/index");
                    exit;
                } else {
                    die("Error al insertar el punto de detención.");
                }
            } catch (Exception $e) {
                if ($e->getCode() == 23000) {
                    $errores[] = "El punto de detencion '{$nombre}' ya existe en el sistema.";
                } else {
                    $errores[] = "Error al guardar el punto de detencion: " . $e->getMessage();
                }
                
                $this->load_view('puntos_detencion/form', [
                    'title' => 'Crear nuevo punto de detención',
                    'action' => URL . '/puntosDetencion/save',
                    'values' => $_POST,
                    'errores' => $errores,
                    'calles' => $calles
                ]);
            }
        }
    }

    // Formulario para editar un punto
    public function edit($id)
    {
        $punto_detencion = $this->model->getPuntoDetencion($id);
        $calles = $this->calleModel->getAllCalles();

        if (!$punto_detencion) {
            die("Punto de detención no encontrado.");
        }

        $this->load_view('puntos_detencion/form', [
            'title' => 'Editar punto de detención',
            'action' => URL . '/puntosDetencion/update/' . $id,
            'values' => [
                'id_punto_detencion' => $punto_detencion['id_punto_detencion'],
                'nombre' => $punto_detencion['nombre'],
                'calle' => $punto_detencion['id_calle']
            ],
            'errores' => [],
            'calles' => $calles
        ]);
    }

    // Procesar actualización
    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"]);
            $calle = $_POST["calle"];

            $reservas = $this->model->getReservaByPunto($id);

            $errores = [];
            if (empty($nombre)) { $errores[] = 'El nombre del punto es obligatorio.'; }
            if (empty($calle)) { $errores[] = 'Debe seleccionar una calle.'; }

            if (!empty($errores)) {
                $punto_detencion = [
                    'id_punto_detencion' => $id,
                    'nombre' => $nombre,
                    'calle' => $calle
                ];
                $calles = $this->calleModel->getAllCalles();
                $this->load_view('puntos_detencion/form', [
                    'title' => 'Editar punto de detención',
                    'action' => URL . '/puntosDetencion/update/' . $id,
                    'values' => $punto_detencion,
                    'errores' => $errores,
                    'calles' => $calles
                ]);

                return;
            }
            if(!empty($reservas)){
                $this->model->desactivarPuntoDetencion($id);
                $this->model->insertPuntoDetencion($nombre, $calle);
                header("Location: " . URL . "/puntosDetencion");
                exit;
            } else {
                if ($this->model->updatePuntoDetencion($id, $nombre, $calle)) {
                    header("Location: " . URL . "/puntosDetencion");
                    exit;
                } else {
                    die("Error al actualizar el punto de detención.");
                }
            }
        }
    }

    // Eliminar un punto
    public function delete($id)
    {
        $reservas = $this->model->getReservaByPunto($id);
        if (empty($reservas)) {
            $eliminado = $this->model->deletePuntoDetencion($id);
            if (!$eliminado) {
                $this->index(["No se pudo eliminar el punto de detención."]);
            }

            header("Location: " . URL . "/puntosDetencion");
            exit;
        }

        $nombres_reservas = $reservas ? array_column($reservas, 'id_permiso') : [];
        $string_reservas = implode(', ', $nombres_reservas);
        $this->index(["No se puede eliminar el punto de detencion, esta reservado por los siguientes permisos: ". $string_reservas]);

    }
}
