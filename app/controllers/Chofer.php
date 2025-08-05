<?php
class Chofer extends Control
{
    private $modelo;
    private $modeloNacionalidades;

    public function __construct()
    {
        $this->modelo = $this->load_model('ChoferesModel');
        $this->modeloNacionalidades = $this->load_model('NacionalidadModel');
    }

    public function index()
    {
        $choferes = $this->modelo->getAllChoferes();
        $datos = [
            'title' => 'Listado de Choferes',
            'urlCrear' => URL . '/chofer/create',
            'columnas' => ['Nombre', 'Apellido', 'DNI', 'Nacionalidad'],
            'columnas_claves' => ['nombre', 'apellido', 'dni', 'nacionalidad'],
            'data' => $choferes,
            'acciones' => function($fila) {
                $id = $fila['id_chofer'];
                $url = URL . '/chofer';
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este chofer?\');">Eliminar</a>
                ';
            }
        ];    
        $this->load_view('partials/tablaAbm', $datos);
    }
    
    public function edit($id)
    {
        $chofer = $this->modelo->getChofer($id);  
        $nacionalidades = $this->modeloNacionalidades->getAllNacionalidades();

        if (!$chofer) {
            die("Chofer no encontrado");
        }

        $this->load_view('choferes/form', [
            'title' => 'Editar chofer',
            'action' => URL . '/chofer/update/' . $id,
            'values' => [
                'nombre' => $chofer['nombre'],
                'apellido' => $chofer['apellido'],
                'dni' => $chofer['dni'],
                'nacionalidad' => $chofer['id_nacionalidad']
            ],
            'errores' => [],
            'nacionalidades' => $nacionalidades
        ]);
    }

    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"] ?? '');
            $apellido = trim($_POST["apellido"] ?? '');
            $dni = trim($_POST["dni"] ?? '');
            $nacionalidad = $_POST["nacionalidad"] ?? '';

            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
            if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
            if (empty($dni)) $errores[] = "El DNI es obligatorio.";
            if (empty($nacionalidad)) $errores[] = "Debe seleccionar una nacionalidad.";

            if (!empty($errores)) {
                $chofer = [
                    'id_chofer' => $id,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'dni' => $dni,
                    'id_nacionalidad' => $nacionalidad
                ];
                $nacionalidades = $this->modeloNacionalidades->getAllNacionalidades();
                $this->load_view('choferes/form', [
                    'title' => 'Editar chofer',
                    'action' => URL . '/chofer/update/' . $id,
                    'values' => $chofer,
                    'errores' => $errores,
                    'nacionalidades' => $nacionalidades
                ]);
                return;
            }

            if ($this->modelo->updateChofer($id, $dni, $nombre, $apellido, $nacionalidad)) {
                header("Location: " . URL . "/chofer");
                exit;
            } else {
                die("Error al actualizar el chofer");
            }
        }
    }

    public function create()
    {
        $nacionalidades = $this->modeloNacionalidades->getAllNacionalidades();
        $this->load_view('choferes/form', [
            'title' => 'Crear nuevo chofer',
            'action' => URL . '/chofer/save',
            'values' => [],
            'errores' => [],
            'nacionalidades' => $nacionalidades
        ]);
    }
    
    public function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"] ?? '');
            $apellido = trim($_POST["apellido"] ?? '');
            $dni = trim($_POST["dni"] ?? '');
            $nacionalidad = $_POST["nacionalidad"] ?? '';

            // Validaciones simples
            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
            if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
            if (empty($dni)) $errores[] = "El DNI es obligatorio.";
            if (empty($nacionalidad)) $errores[] = "Debe seleccionar una nacionalidad.";

            if (!empty($errores)) {
                $nacionalidades = $this->modeloNacionalidades->getAll();
                $this->load_view('choferes/form', [
                    'title' => 'Crear nuevo chofer',
                    'action' => URL . '/chofer/save',
                    'values' => $_POST,
                    'errores' => $errores,
                    'nacionalidades' => $nacionalidades
                ]);
                return;
            }

            if ($this->modelo->insertChofer($dni, $nombre, $apellido, $nacionalidad)) {
                header("Location: " . URL . "/chofer");
                exit;
            } else {
                die("Error al guardar el chofer");
            }
        }
    }

    public function delete($id){
        $permisosModel = $this->load_model("PermisoModel");
        $permisos = $permisosModel->getPermisosByChofer($id);
        if (empty($permisos)) {
            $this->modelo->deleteChofer($id);
            header("Location: " . URL . "/chofer");
            exit;
        }
            die("No se puede eliminar la chofer, tiene permisos asignados.");
    }

    public function saveAjax()
    {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre']);
            $apellido = trim($_POST['apellido']);
            $dni = trim($_POST['dni']);
            $nacionalidad = $_POST['nacionalidad'];

            if ($nombre === '' || $apellido === '' || $dni === '' || $nacionalidad === '') {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                return;
            }
            $idChofer = $this->modelo->insertChofer($dni, $nombre, $apellido, $nacionalidad);
            if ($idChofer) {
                echo json_encode([
                    'success' => true,
                    'id_chofer' => $idChofer,
                    'nombreCompleto' => $dni . ' - ' . $nombre . ' ' . $apellido
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar chofer']);
            }
        }
    }

}
