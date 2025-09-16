<?php
/**
 * Controlador para manejar las operaciones relacionadas con los hoteles.
 */
class Hoteles extends Control
{
    private HotelesModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('HotelesModel');
    }

    // Listar todos los hoteles.
    public function index()
    {
        if ($this->tienePermiso("ver abm")){
            $errores = [];
            if (isset($_SESSION['error_hoteles'])) {
                $errores[] = $_SESSION['error_hoteles'];
                unset($_SESSION['error_hoteles']); // Borramos el mensaje después de usarlo
            }
            $datos = [
                'title' => 'Listado de Hoteles',
                'urlCrear' => URL . '/hoteles/create',
                'urlAjax' => URL . '/hoteles/ajaxList',
                'columnas' => ['Nombre de Hotel','Direccion'],
                'columnas_claves' => ['nombre', 'direccion'],
                'acciones' => true,
                'errores' => $errores
            ];
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Mostrar formulario para crear hotel.
    public function create()
    {
        if ($this->tienePermiso("cargar abm")) {
            $this->load_view('hoteles/form', [
                'title' => 'Crear nuevo hotel',
                'action' => URL . '/hoteles/save',
                'values' => [],
                'errores' => [],
            ]);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Procesar creación de hotel.
    public function save()
    {
        if ($this->tienePermiso("cargar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty($_POST['nombre'])){
                    $nombre = trim($_POST['nombre']);
                }
                if (!empty($_POST['direccion'])){
                    $direccion = trim($_POST['direccion']);
                }

                // Validaciones simples
                $errores = [];
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
                if (empty($direccion)) $errores[] = "La dirección es obligatoria.";

                if (!empty($errores)) {
                    $this->load_view('hoteles/form', [
                        'title' => 'Crear nuevo hotel',
                        'action' => URL . '/hoteles/guardar',
                        'values' => $_POST,
                        'errores' => $errores,
                    ]);
                    return;
                }
                try{
                    if ($this->model->insertHotel($nombre, $direccion)) {
                        header("Location: " . URL . "/hoteles");
                        exit;
                    } else {
                        $_SESSION['error_hoteles'] = "Error al guardar hotel.";
                        header("Location: " . URL . "/hoteles");
                        exit;
                    }
                } catch (Exception $e) {
                    if ($e->getCode() == 23000) {
                        $errores[] = "El hotel '{$nombre}' en '{$direccion}' ya existe en el sistema.";
                    } else {
                        $errores[] = "Error al guardar el Hotel: " . $e->getMessage();
                    }
                    $this->load_view('hoteles/form', [
                        'title' => 'Crear nuevo hotel',
                        'action' => URL . '/hoteles/save',
                        'values' => $_POST,
                        'errores' => $errores,
                    ]);
                }
            }   
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Mostrar formulario para editar hotel.
    public function edit($id)
    {
        if ($this->tienePermiso("editar abm")) {
            $hotel = $this->model->getHotel($id);  

            if (!$hotel) {
                $_SESSION['error_hoteles'] = "Hotel no encontrado.";
                header("Location: " . URL . "/hoteles");
                exit;
            }

            $this->load_view('hoteles/form', [
                'title' => 'Editar hotel',
                'action' => URL . '/hoteles/update/' . $id,
                'values' => [
                    'nombre' => $hotel['nombre'],
                    'direccion' => $hotel['direccion']  
                ],
                'errores' => [],

            ]);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Procesar actualización de hotel.
    public function update($id)
    {
        if ($this->tienePermiso("editar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!empty($_POST['nombre'])){
                    $nombre = trim($_POST['nombre']);
                }
                if (!empty($_POST['direccion'])){
                    $direccion = trim($_POST['direccion']);
                }
                

            $reservas = $this->model->getReservasByHotel($id);


                $errores = [];
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
                if (empty($direccion)) $errores[] = "La dirección es obligatoria.";

                if (!empty($errores)) {
                    $hotel = [
                        'id_hotel' => $id,
                        'nombre' => $nombre,
                        'direccion'=> $direccion 
                    ];
                    $this->load_view('hoteles/form', [
                        'title' => 'Editar hotel',
                        'action' => URL . '/hoteles/update/' . $id,
                        'values' => $hotel,
                        'errores' => $errores,
                    ]);
                    return;
                }
                if (!empty($reservas)) {
                    $this->model->desactivarHotel($id);
                    $this->model->insertHotel($nombre, $direccion);
                    header("Location: " . URL . "/hoteles/index");
                    exit;
                } else {

                    if ($this->model->updateHotel($id, $nombre, $direccion)) { 
                        header("Location: " . URL . "/hoteles/index");
                        exit;
                    } else {
                        
                        $_SESSION['error_hoteles'] = "Error al actualizar el hotel.";
                        header("Location: " . URL . "/hoteles");
                        exit;
                    }
                }
            }
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Eliminar hotel.
    public function delete($id)
    {
        if ($this->tienePermiso("borrar abm")) {
            $reservasModel = $this->load_model("ReservasPuntosModel");
            $reservas = $reservasModel->getReservasPuntosByHotel($id);
            if (empty($reservas)) {
                $eliminado = $this->model->deleteHotel($id);
                if (!$eliminado) {
                        
                    $_SESSION['error_hoteles'] = "No se pudo eliminar el Hotel.";
                    header("Location: " . URL . "/hoteles");
                    exit;
                }

                header("Location: " . URL . "/hoteles");
                exit;
            }
            $nombres_reservas = $reservas ? array_column($reservas, 'id_permiso') : [];
            $string_reservas = implode(', ', $nombres_reservas);
            $_SESSION['error_hoteles'] = "No se puede eliminar el hotel, está asignado a los siguientes permisos: ". $string_reservas;
            header("Location: " . URL . "/hoteles");
            exit;
        } else {
            header("Location: " . URL);
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

        $columnas = ['nombre', 'direccion'];

        $orderColumn = 'nombre';
        if (isset($columnas[$orderColumnIndex])) {
            $orderColumn = $columnas[$orderColumnIndex];
        }

        // Total de registros (sin filtro)
        $recordsTotal = $this->model->contarHoteles();

        // Registros filtrados y paginados
        $records = $this->model->getHotelesServerSide($start, $length, $searchValue, $orderColumn, $orderDir);

        // Total de registros filtrados
        $recordsFiltered = $this->model->contarHotelesFiltrados($searchValue);

        // Preparar data con botones de acciones
        $data = [];
        foreach ($records as $fila) {
            $acciones = '';
            $id = $fila['id_hotel'];
            $url = URL . '/hoteles';
            if ($this->tienePermiso('editar abm')){
                $acciones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a>';
            }
            if ($this->tienePermiso('borrar abm')){
                $acciones .= '<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Eliminar este Hotel?\');">Eliminar</a>';
            }

            $data[] = [
                'nombre' => ucfirst(htmlspecialchars($fila['nombre'])),
                'direccion' => ucfirst(htmlspecialchars($fila['direccion'])),
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
