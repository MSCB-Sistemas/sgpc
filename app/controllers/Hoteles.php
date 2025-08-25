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
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar esta Hotel?\');">Eliminar</a>
                ';
            }
            ,'errores' => $errores
        ];
        $this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar formulario para crear hotel.
    public function create()
    {
        $this->load_view('hoteles/form', [
            'title' => 'Crear nuevo hotel',
            'action' => URL . '/hoteles/save',
            'values' => [],
            'errores' => [],
        ]);
    }

    // Procesar creación de hotel.
    public function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"] ?? '');
            $direccion = trim($_POST["direccion"] ?? '');   
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
    }

    // Mostrar formulario para editar hotel.
    public function edit($id)
    {
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
    }

    // Procesar actualización de hotel.
    public function update($id)
    {
         if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"] ?? '');
            $direccion = trim($_POST['direccion'] ??'');

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
    }

    // Eliminar hotel.
    public function delete($id)
    {
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
    }
}
