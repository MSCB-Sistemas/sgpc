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
    public function index()
    {
        if (in_array('ver abm',$_SESSION['usuario_derechos'])){
            $errores = [];
            if (isset($_SESSION['error_pd'])) {
                $errores[] = $_SESSION['error_pd'];
                unset($_SESSION['error_pd']); // Borramos el mensaje después de usarlo
            }
            $datos = [
                'title' => 'Listado de Puntos de Detencion',
                'urlCrear' => URL . '/puntosDetencion/create',
                'urlAjax' => URL . '/puntosDetencion/ajaxList',
                'columnas' => ['Punto', 'Calle'],  
                'columnas_claves' => ['nombre_punto', 'nombre_calle'],
                'acciones' => true,
                'errores' => $errores
            ];

            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Formulario para crear un nuevo punto
    public function create($id_calle = null)
    {
        if ($this->tienePermiso('cargar abm')) {
            $calles = $this->calleModel->getAllCalles();
            $datos = [
                'title' => 'Crear nuevo punto de detención',
                'action' => URL . '/puntosDetencion/save',
                'values' => [],
                'errores' => [],
                'calle' => $id_calle,
                'calles' => $calles
            ];
            $this->load_view('puntos_detencion/form', $datos);
        }
    }

    // Procesar la creación
    public function save()
    {
        if ($this->tienePermiso('cargar abm')) {
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
                        header("Location: " . URL . "/puntosDetencion");
                        exit;
                    } else {
                        $_SESSION['error_pd'] = "Error al insertar el punto de detención.";
                        header("Location: " . URL . "/PuntosDetencion");
                        exit;
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
    }

    // Formulario para editar un punto
    public function edit($id)
    {
        if ($this->tienePermiso('editar abm')) {
            $punto_detencion = $this->model->getPuntoDetencion($id);
            $calles = $this->calleModel->getAllCalles();

            if (!$punto_detencion) {
                $_SESSION['error_pd'] = "Punto de detención no encontrado.";
                header("Location: " . URL . "/PuntosDetencion");
                exit;
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
    }

    // Procesar actualización
    public function update($id)
    {
        if ($this->tienePermiso('editar abm')) {
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
                        $_SESSION['error_pd'] = "Error al actualizar el punto de detención.";
                        header("Location: " . URL . "/PuntosDetencion");
                        exit;
                    }
                }
            }
        }
    }

    // Eliminar un punto
    public function delete($id)
    {
        if ($this->tienePermiso('borrar abm')) {
            $reservas = $this->model->getReservaByPunto($id);
            if (empty($reservas)) {
                $eliminado = $this->model->deletePuntoDetencion($id);
                if (!$eliminado) {
                    $_SESSION['error_pd'] = "No se pudo eliminar el punto de detención.";
                    header("Location: " . URL . "/PuntosDetencion");
                    exit;
                }

                header("Location: " . URL . "/puntosDetencion");
                exit;
            }

            $nombres_reservas = $reservas ? array_column($reservas, 'id_permiso') : [];
            $string_reservas = implode(', ', $nombres_reservas);
            $_SESSION['error_pd'] = "No se puede eliminar el punto de detencion, esta reservado por los siguientes permisos: ". $string_reservas;
            header("Location: " . URL . "/PuntosDetencion");
            exit;
        }
    }

    public function ajaxList()
    {
        // Solo permitir acceso con permisos
        if (!$this->tienePermiso("ver abm")) {
            header("Location: " . URL);
            exit;
        }

        // Parámetros que envía DataTables
        $draw = 1;
        if (isset($_GET['draw'])) {
            $draw = $_GET['draw'];
        }
        $start = 0;
        if (isset($_GET['start'])) {
            $start = $_GET['start'];
        }
        $length = 10;
        if (isset($_GET['length'])) {
            $length = $_GET['length'];
        }
        $searchValue = '';
        if (isset($_GET['search']['value'])) {
            $searchValue = $_GET['search']['value'];
        }

        // Orden
        $orderColumnIndex = 0;
        if (isset($_GET['order'][0]['column'])) {
            $orderColumnIndex = $_GET['order'][0]['column'];
        }
        $orderDir = 'asc';
        if (isset($_GET['order'][0]['dir'])) {
            $orderDir = $_GET['order'][0]['dir'];
        }

        $columnas = ['nombre_punto', 'nombre_calle'];

        $orderColumn = 'nombre';
        if (isset($columnas[$orderColumnIndex])) {
            $orderColumn = $columnas[$orderColumnIndex];
        }

        // Total de registros (sin filtro)
        $recordsTotal = $this->model->contarPuntosDetencion();

        // Registros filtrados y paginados
        $records = $this->model->getPuntosDetencionServerSide($start, $length, $searchValue, $orderColumn, $orderDir);

        // Total de registros filtrados
        $recordsFiltered = $this->model->contarPuntosDetencionFiltrados($searchValue);

        // Preparar data con botones de acciones
        $data = [];
        foreach ($records as $fila) {
            $acciones = '';
            $id = $fila['id_punto_detencion'];
            $url = URL . '/puntosDetencion';

            if ($this->tienePermiso('editar abm')) {
                $acciones .= '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a>
                '; 
            }

            if ($this->tienePermiso('borrar abm')) {
                $acciones .= '
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Eliminar este punto?\');">Eliminar</a>
                ';
            }

            $data[] = [
                'nombre_punto' => ucfirst(htmlspecialchars($fila['nombre_punto'])),
                'nombre_calle' => ucfirst(htmlspecialchars($fila['nombre_calle'])),
                'acciones' => $acciones
            ];
        }

        // Respuesta en JSON
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $data
        ]);
        exit;
    }
}
