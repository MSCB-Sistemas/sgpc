<?php

/**
 * Controlador de LugarModel.php
 */
class Lugar extends Control
{
    private LugarModel $model;
    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('LugarModel');
    }

    // Mostrar todos los lugares en una vista.
    public function index()
    {
        if ($this->tienePermiso("ver abm")){
            $errores = [];
            if (isset($_SESSION['error_lugar'])) {
                $errores[] = $_SESSION['error_lugar'];
                unset($_SESSION['error_lugar']); // Borramos el mensaje después de usarlo
            }
            $datos = [
                'title' => 'Listado de Lugares',
                'urlCrear' => URL . '/lugar/create',
                'urlAjax' => URL. '/lugar/ajaxList',
                'columnas' => ['Nombre'],
                'columnas_claves' => ['nombre'],
                'acciones' => true,
                'errores' => $errores
            ];    
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Mostrar formulario para crear un nuevo.
    public function create()
    {
        if ($this->tienePermiso("cargar abm")) {
            $this->load_view('lugares/form', [
                'title' => 'Crear nuevo Lugar',
                'action' => URL . '/lugar/save',
                'values' => [],
                'errores' => [],
            ]);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Procesar el formulario para guardar lugar nuevo.
    public function save()
    {
        if ($this->tienePermiso("cargar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre = trim($_POST["nombre"]);

                // Validaciones simples
                $errores = [];
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
                if (!empty($errores)) {
                        $this->load_view('lugares/form', [
                            'title' => 'Crear nuevo Lugar',
                            'action' => URL . '/lugar/save',
                            'values' => $_POST,
                            'errores' => $errores,
                        ]);
                        return;
                    }
                try {
                    if ($this->model->insertLugar( $nombre)) {
                        header("Location: " . URL . "/lugar");
                        exit;
                    } else {
                        $_SESSION['error_lugar'] = "Error al guardar el lugar.";
                        header("Location: " . URL . "/lugar");
                        exit;
                    }
                    
                } catch (Exception $e) {
                    if ($e->getCode() == 23000) {
                        $errores[] = "El lugar '{$_POST['nombre']}' ya existe en el sistema.";
                    } else {
                        $errores[] = "Error al guardar lugar: " . $e->getMessage();
                    }
                    $this->load_view('lugares/form', [
                        'title' => 'Crear nuevo Lugar',
                        'action' => URL . '/lugar/save',
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

    // Mostrar formulario para editar un lugar.
    public function edit($id)
    {
        if ($this->tienePermiso("editar abm")) {
            $lugar = $this->model->getLugar($id);  

            if (!$lugar) {
                $_SESSION['error_lugar'] = "Lugar no encontrado.";
                header("Location: " . URL . "/lugar");
                exit;
            }

            $this->load_view('lugares/form', [
                'title' => 'Editar lugar',
                'action' => URL . '/lugar/update/' . $id,
                'values' => [
                    'nombre' => $lugar['nombre']
                ],
                'errores' => [],
            ]);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Procesar la actualización de lugar.
    public function update($id)
    {
        if ($this->tienePermiso("editar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre = trim($_POST["nombre"]);

                $permisos = $this->model->getPermisosByLugarId($id);


                $errores = [];
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";

                if (!empty($errores)) {
                    $lugar = [
                        'nombre' => $nombre
                    ];
                    $this->load_view('lugares/form', [
                        'title' => 'Editar lugar',
                        'action' => URL . '/lugar/update/' . $id,
                        'values' => $lugar,
                        'errores' => $errores,
                    ]);
                    return;
                }
                if(!empty($permisos)){
                    $this->model->desactivarLugar($id);
                    $this->model->insertLugar($nombre);
                    header("Location: " . URL . "/lugar");
                    exit;
                } else {

                    if ($this->model->updateLugar($id,  $nombre)) {
                        header("Location: " . URL . "/lugar");
                        exit;
                    } else {
                        $_SESSION['error_lugar'] = "Error al actualizar lugar.";
                        header("Location: " . URL . "/lugar");
                        exit;
                    }
                }
            }
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Eliminar un lugar.
    public function delete($id)
    {
        if ($this->tienePermiso("borrar abm")) {
            $permiso = $this->model->getPermisosByLugarId($id);
            if (empty($permiso)) {
                $eliminado = $this->model->deleteLugar($id);
                if (!$eliminado) {
                    $_SESSION['error_lugar'] = "No se pudo eliminar el Lugar.";
                    header("Location: " . URL . "/lugar");
                    exit;
                }

                header("Location: " . URL . "/lugar");
                exit;
            }
            $nombres_permisos = $permiso ? array_column($permiso, 'id_permiso') : [];
            $string_permisos = implode(', ', $nombres_permisos);
            $_SESSION['error_lugar'] = "No se puede eliminar el lugar, esta asignado a los siguientes permisos: ". $string_permisos;
            header("Location: " . URL . "/lugar");
            exit;
        } else {
            header("Location: " . URL);
            exit;
        }
    }
    
    public function saveAjax()
    {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre']);

            if ($nombre === '') {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                return;
            }
            $idLugar = $this->model->insertLugar($nombre);
            if ($idLugar) {
                echo json_encode([
                    'success' => true,
                    'id_lugar' => $idLugar,
                    'nombre' => $nombre
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar el lugar']);
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

        $columnas = ['nombre'];

        $orderColumn = 'nombre';
        if (isset($columnas[$orderColumnIndex])) {
            $orderColumn = $columnas[$orderColumnIndex];
        }

        // Total de registros (sin filtro)
        $recordsTotal = $this->model->contarLugares();

        // Registros filtrados y paginados
        $records = $this->model->getLugaresServerSide($start, $length, $searchValue, $orderColumn, $orderDir);

        // Total de registros filtrados
        $recordsFiltered = $this->model->contarLugaresFiltrados($searchValue);

        // Preparar data con botones de acciones
        $data = [];
        foreach ($records as $fila) {
            $acciones = '';
            $id = $fila['id_lugar'];
            $url = URL . '/lugar';
            if ($this->tienePermiso('editar abm')){
                $acciones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a>';
            }
            if ($this->tienePermiso('borrar abm')){
                $acciones .= '<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Eliminar este Lugar?\');">Eliminar</a>';
            }

            $data[] = [
                'nombre' => ucfirst(htmlspecialchars($fila['nombre'])),
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