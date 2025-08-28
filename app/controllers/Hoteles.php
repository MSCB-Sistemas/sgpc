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
    public function index($errores = [])
    {
        if ($this->tienePermiso("ver abm")){
            $hoteles = $this->model->getAllHoteles();
            $datos = [
                'title' => 'Listado de Hoteles',
                'urlCrear' => URL . '/hoteles/create',
                'columnas' => ['Nombre de Hotel','Direccion'],
                'columnas_claves' => ['nombre', 'direccion'],
                'data' => $hoteles,
                'acciones' => function($fila) {
                    $id = $fila['id_hotel'];
                    $url = URL . '/hoteles';
                    $botones = '';
                    if ($this->tienePermiso('editar abm')){
                        $botones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>';
                    }
                    if ($this->tienePermiso('borrar abm')){
                        $botones .= '<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este Hotel?\');">Eliminar</a>';
                    }
                }
                ,'errores' => $errores
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
                        die("Error al guardar hotel");
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
                die("Hotel no encontrado");
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
                        die("Error al actualizar el hotel");
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
                    $this->index(["No se pudo eliminar el Hotel."]);
                }

                header("Location: " . URL . "/hoteles");
                exit;
            }
            $nombres_reservas = $reservas ? array_column($reservas, 'id_permiso') : [];
            $string_reservas = implode(', ', $nombres_reservas);
            $this->index(["No se puede eliminar el hotel, esta asignado a los siguientes permisos: ". $string_reservas]);
        } else {
            header("Location: " . URL);
            exit;
        }
    }
}
