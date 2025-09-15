<?php

class Chofer extends Control
{
    private ChoferesModel $modelo;
    private NacionalidadModel $modeloNacionalidades;

    public function __construct()
    {
        $this->requireLogin();
        $this->modelo = $this->load_model('ChoferesModel');
        $this->modeloNacionalidades = $this->load_model('NacionalidadModel');
    }

    public function index()
    {
        if ($this->tienePermiso("ver abm")) {
            $errores = [];
            if (isset($_SESSION['error_chofer'])) {
                $errores[] = $_SESSION['error_chofer'];
                unset($_SESSION['error_chofer']); // Borramos el mensaje después de usarlo
            }
            $datos = [
                'title' => 'Listado de Choferes',
                'urlCrear' => URL . '/chofer/create',
                'urlAjax' => URL . '/chofer/ajaxList', // <--- lo nuevo
                'columnas' => ['Nombre', 'Apellido', 'DNI', 'Nacionalidad'],
                'columnas_claves' => ['nombre', 'apellido', 'dni', 'nacionalidad'],
                'acciones' => true,
                'errores' => $errores
            ];    
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }
    
    public function edit($id)
    {
        if ($this->tienePermiso("editar abm")) {
            $chofer = $this->modelo->getChofer($id);  
            $nacionalidades = $this->modeloNacionalidades->getAllNacionalidades();
            $permisosModel = $this->load_model("PermisoModel");
            $permisos = $permisosModel->getPermisosByChofer($id);

            if (!$chofer) {
                $_SESSION['error_chofer'] = "Chofer no encontrado.";
                header("Location: " . URL . "/chofer");
                exit;
            }

            if (!empty($permisos)){
                $_SESSION['error_chofer'] = "Error: No se puede editar un chofer con permisos asignados.";
                header("Location: " . URL . "/chofer");
                exit;
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
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function update($id)
    {
        if ($this->tienePermiso("editar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre = trim($_POST["nombre"]);
                $apellido = trim($_POST["apellido"]);
                $dni = trim($_POST["dni"]);
                $nacionalidad = $_POST["nacionalidad"];

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
                try {
                    if ($this->modelo->updateChofer($id, $dni, $nombre, $apellido, $nacionalidad)) {
                        header("Location: " . URL . "/chofer");
                        exit;
                    } else {
                        $_SESSION['error_chofer'] = "Error al actualizar el chofer.";
                        header("Location: " . URL . "/chofer");
                        exit;
                    }
                } catch (Exception $e) {
                    $nombre_nacionalidad = $this->modeloNacionalidades->getNacionalidad($nacionalidad)['nacionalidad'];
                    if ($e->getCode() == 23000) {
                        $errores[] = "El chofer '{$dni}' de nacionalidad '{$nombre_nacionalidad}'  ya está registrado en el sistema.";
                    } else {
                        $errores[] = "Error al guardar el chofer: " . $e->getMessage();
                    }
                    $nacionalidades = $this->modeloNacionalidades->getAllNacionalidades();
                    $this->load_view('choferes/form', [
                        'title' => 'Crear nuevo chofer',
                        'action' => URL . '/chofer/save',
                        'values' => $_POST,
                        'errores' => $errores,
                        'nacionalidades' => $nacionalidades
                    ]);
                }
            }
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function create()
    {
        if ($this->tienePermiso("cargar abm")) {
            $nacionalidades = $this->modeloNacionalidades->getAllNacionalidades();
            $this->load_view('choferes/form', [
                'title' => 'Crear nuevo chofer',
                'action' => URL . '/chofer/save',
                'values' => [],
                'errores' => [],
                'nacionalidades' => $nacionalidades
            ]);
        } else {
            header("Location: " . URL);
        }
    }
    
    public function save()
    {
        if ($this->tienePermiso("cargar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre = trim($_POST["nombre"]);
                $apellido = trim($_POST["apellido"]);
                $dni = trim($_POST["dni"]);
                $nacionalidad = $_POST["nacionalidad"];
                $nacionalidades = $this->modeloNacionalidades->getAllNacionalidades();
                
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
                try{
                    if ($this->modelo->insertChofer($dni, $nombre, $apellido, $nacionalidad)) {
                        header("Location: " . URL . "/chofer");
                        exit;
                    } else {
                        $_SESSION['error_chofer'] = "Error al guardar el chofer.";
                        header("Location: " . URL . "/chofer");
                        exit;
                    }
                } catch (Exception $e) {
                    $nombre_nacionalidad = $this->modeloNacionalidades->getNacionalidad($nacionalidad)['nacionalidad'];
                    if ($e->getCode() == 23000) {
                        $errores[] = "El chofer '{$dni}' de nacionalidad '{$nombre_nacionalidad}'  ya está registrado en el sistema.";
                    } else {
                        $errores[] = "Error al guardar el chofer: " . $e->getMessage();
                    }
                    $this->load_view('choferes/form', [
                        'title' => 'Crear nuevo chofer',
                        'action' => URL . '/chofer/save',
                        'values' => $_POST,
                        'errores' => $errores,
                        'nacionalidades' => $nacionalidades
                    ]);
                }
            }
        } else {
            header("Location: " . URL);
        }
    }
    

    public function delete($id){
        if ($this->tienePermiso("borrar abm")) {
            $permisosModel = $this->load_model("PermisoModel");
            $permisos = $permisosModel->getPermisosByChofer($id);
            if (empty($permisos)) {
                $this->modelo->deleteChofer($id);
                header("Location: " . URL . "/chofer");
                exit;
            }
            $ids_permisos = $permisos ? array_column($permisos, 'id_permiso') : [];
            $string_permisos = implode(', ', $ids_permisos);
            $_SESSION['error_chofer'] = "No se puede eliminar el chofer, tiene los siguientes permisos asignados: ". $string_permisos;
            header("Location: " . URL . "/chofer");
            exit;
        } else {
            header("Location: " . URL);
        }
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

    public function ajaxList()
    {
        // Solo permitir acceso con permisos
        if (!$this->tienePermiso("ver abm")) {
            header("Location: " . URL);
            exit;
        }

        // Parámetros que envía DataTables
        $draw   = $_GET['draw'] ?? 1;
        $start  = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $searchValue = $_GET['search']['value'] ?? '';

        // Orden
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderDir = $_GET['order'][0]['dir'] ?? 'asc';

        // Definí las columnas en el mismo orden que en tu JS
        $columnas = ['nombre', 'apellido', 'dni', 'nacionalidad'];

        $orderColumn = $columnas[$orderColumnIndex] ?? 'nombre';

        // Total de registros (sin filtro)
        $recordsTotal = $this->modelo->contarChoferes();

        // Registros filtrados y paginados
        $choferes = $this->modelo->getChoferesServerSide($start, $length, $searchValue, $orderColumn, $orderDir);

        // Total de registros filtrados
        $recordsFiltered = $this->modelo->contarChoferesFiltrados($searchValue);

        // Preparar data con botones de acciones
        $data = [];
        foreach ($choferes as $fila) {
            $acciones = '';
            $id = $fila['id_chofer'];
            $url = URL . '/chofer';
            if ($this->tienePermiso('editar abm')) {
                $acciones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a> ';
            }
            if ($this->tienePermiso('borrar abm')) {
                $acciones .= '<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Eliminar este chofer?\');">Eliminar</a>';
            }

            $data[] = [
                'nombre' => ucfirst(htmlspecialchars($fila['nombre'])),
                'apellido' => ucfirst(htmlspecialchars($fila['apellido'])),
                'dni' => htmlspecialchars($fila['dni']),
                'nacionalidad' => htmlspecialchars($fila['nacionalidad']),
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
