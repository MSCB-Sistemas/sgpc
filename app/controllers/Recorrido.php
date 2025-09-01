<?php
/**
 * Controlador para manejar las operaciones relacionadas con los recorridos.
 */
class Recorrido extends Control
{
    private RecorridoModel $model;
    private CalleRecorridoModel $calleRecorridoModel;
    private CalleModel $calleModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model("RecorridoModel"); 
        $this->calleRecorridoModel = $this->load_model("CalleRecorridoModel");
        $this->calleModel = $this->load_model("CalleModel");
    }

    // Mostrar todos los recorridos
    public function index()
    {
        if ($this->tienePermiso('ver abm')){
            $errores = [];
            if (isset($_SESSION['error_recorrido'])) {
                $errores[] = $_SESSION['error_recorrido'];
                unset($_SESSION['error_recorrido']); // Borramos el mensaje después de usarlo
            }
            $recorridos = $this->model->getAllRecorridos();
            foreach ($recorridos as &$recorrido) {
                $calles = $this->calleRecorridoModel->getCallesByRecorrido($recorrido['id_recorrido']);
                $nombres = $calles ? array_column($calles, 'nombre') : [];
                $recorrido['calles'] = $nombres ? implode(', ', $nombres) : 'Sin calles';
            }

            unSet($recorrido);

            $datos = [
                'title' => 'Listado de Recorridos',
                'urlCrear' => URL . '/recorrido/create',
                'columnas' => ['ID', 'Nombre', 'Calles'],
                'columnas_claves' => ['id_recorrido','nombre','calles'],
                'data' => $recorridos,
                'acciones' => function($fila) {
                    $id = $fila['id_recorrido'];
                    $url = URL . '/recorrido';
                    $botones = '';

                    if ($this->tienePermiso('editar abm')) {
                        $botones .= '
                            <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a>
                        ';
                    }

                    if ($this->tienePermiso('borrar abm')) {
                        $botones .= '
                            <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Eliminar este recorrido?\');">Eliminar</a>
                        ';
                    }

                    return $botones;
                },
                'errores' => $errores
            ];
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Mostrar formulario de creación
    public function create()
    {
        if ($this->tienePermiso('cargar abm')) {
            $calles = $this->calleModel->getAllCalles();
            $datos = [
                'title' => 'Crear Recorrido',
                'action' => URL . '/recorrido/save',
                'values' => [],
                'errores' => [],
                'calles' => $calles
            ];
            
            $this->load_view('recorridos/form', $datos);
        }
    }

    // Procesar creación a
    public function save()
    {
        if ($this->tienePermiso('cargar abm')) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                
                $nombre = trim($_POST['nombre']);
                $calles = $_POST['calles'];

                $errores = [];
                if ($nombre === '') {
                    $errores[] = "El nombre es obligatorio.";
                }

                if (!empty($errores)) {
                    $datos = [
                        'title' => 'Nuevo Recorrido',
                        'action' => URL . '/recorrido/save',
                        'values' => [
                            'nombre' => $nombre,
                            'calles_array' => $this->mapCalles($calles)
                        ],
                        'calles' => $this->calleModel->getAllCalles(),
                        'errores' => $errores
                    ];
                    $this->load_view('recorridos/form', $datos);
                    return;
                }

                try{
                    $idRecorrido = $this->model->insertRecorrido($nombre);
                    if ($idRecorrido) {
                        foreach ($calles as $idCalle) {
                            if(!$this->calleRecorridoModel->insertCalleRecorrido($idRecorrido, $idCalle)){
                                $_SESSION['error_recorrido'] = "Error al guardar la calle del recorrido.";
                                header("Location: " . URL . "/recorrido");
                                exit;
                            };
                        }
                    } else {
                        $_SESSION['error_recorrido'] = "Error al guardar el recorrido.";
                        header("Location: " . URL . "/recorrido");
                        exit;
                    }

                    header('Location: ' . URL . '/recorrido');
                } catch (Exception $e) {
                    if ($e->getCode() == 23000) {
                        $errores[] = "El recorrido '{$nombre}' ya existe.";
                    } else {
                        $errores[] = "Error al guardar el recorrido: " . $e->getMessage();
                    }
                    $datos = [
                        'title' => 'Nuevo Recorrido',
                        'action' => URL . '/recorrido/save',
                        'values' => [
                            'nombre' => $nombre,
                            'calles_array' => $this->mapCalles($calles)
                        ],
                        'calles' => $this->calleModel->getAllCalles(),
                        'errores' => $errores
                    ];
                    $this->load_view('recorridos/form', $datos);
                }
            }
        }
    }
    
    private function mapCalles($ids)
    {
        $res = [];
        foreach ($ids as $id) {
            $calle = $this->calleModel->getCalle($id);
            if ($calle) {
                $res[$id] = $calle['nombre'];
            }
        }
        return $res;
    }    
    
    public function edit($id)
    {
        if ($this->tienePermiso('editar abm')) {
            $recorrido = $this->model->getRecorrido($id);
            if (!$recorrido) {
                $_SESSION['error_recorrido'] = "Recorrido no encontrado.";
                header("Location: " . URL . "/recorrido");
                exit;
            }

            $calles = $this->calleModel->getAllCalles();
            $callesAsociadas = $this->calleRecorridoModel->getCallesByRecorrido($id);

            $calles_array = [];
            foreach ($callesAsociadas as $c) {
                $calles_array[$c['id_calle']] = $c['nombre'];
            }

            $datos = [
                'title' => 'Editar Recorrido',
                'action' => URL . '/recorrido/update/' . $id,
                'values' => [
                    'nombre' => $recorrido['nombre'],
                    'calles_array' => $calles_array
                ],
                'calles' => $calles,
                'errores' => []
            ];

            $this->load_view('recorridos/form', $datos);
        }
    }

    // Procesar edición
    public function update($id)
    {
        if ($this->tienePermiso('editar abm')) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $nombre = trim($_POST['nombre']);
                $calles = $_POST['calles'];

                $errores = [];
                if ($nombre === '') {
                    $errores[] = "El nombre es obligatorio.";
                }
                if (empty($calles)) {
                    $errores[] = "Debe seleccionar al menos una calle.";
                }

                if (!empty($errores)) {
                    $datos = [
                        'title' => 'Editar Recorrido',
                        'action' => URL . '/recorrido/update/' . $id,
                        'values' => [
                            'nombre' => $nombre,
                            'calles_array' => $this->mapCalles($calles)
                        ],
                        'calles' => $this->calleModel->getAllCalles(),
                        'errores' => $errores
                    ];
                    $this->load_view('recorridos/form', $datos);
                    return;
                }
                try {
                    $permisos = $this->load_model("RecorridosPermisosModel")->getPermisosByRecorrido($id);
                    if (empty($permisos)) {
                        // actualizar nombre
                        if (!$this->model->updateRecorrido($id, $nombre)) {
                            $_SESSION['error_recorrido'] = "Error al actualizar el recorrido.";
                            header("Location: " . URL . "/recorrido");
                            exit;
                        }

                        // borrar calles viejas e insertar nuevas
                        $this->calleRecorridoModel->deleteByRecorrido($id);
                        foreach ($calles as $idCalle) {
                            if (!$this->calleRecorridoModel->insertCalleRecorrido($id, $idCalle)) {
                                $_SESSION['error_recorrido'] = "Error al guardar las calles del recorrido.";
                                header("Location: " . URL . "/recorrido");
                                exit;
                            }
                        }
                    } else {
                        $this->model->desactivarRecorrido($id);
                        $id_nuevo = $this->model->insertRecorrido($nombre);
                        if(empty($id_nuevo)) {
                            $_SESSION['error_recorrido'] = "Error al actualizar el recorrido.";
                            header("Location: " . URL . "/recorrido");
                            exit;
                        }
                        
                        foreach ($calles as $idCalle) {
                            if (!$this->calleRecorridoModel->insertCalleRecorrido($id_nuevo, $idCalle)) {
                                $_SESSION['error_recorrido'] = "Error al guardar las calles del recorrido.";
                                header("Location: " . URL . "/recorrido");
                                exit;
                            }
                        }
                    }
                    header('Location: ' . URL . '/recorrido');
                } catch (Exception $e) {
                    if ($e->getCode() == 23000) {
                        $errores[] = "El recorrido '{$nombre}' ya existe.";
                    } else {
                        $errores[] = "Error al guardar el recorrido: " . $e->getMessage();
                    }
                    $datos = [
                        'title' => 'Nuevo Recorrido',
                        'action' => URL . '/recorrido/save',
                        'values' => [
                            'nombre' => $nombre,
                            'calles_array' => $this->mapCalles($calles)
                        ],
                        'calles' => $this->calleModel->getAllCalles(),
                        'errores' => $errores
                    ];
                    $this->load_view('recorridos/form', $datos);
                }
            }
        }
    }


    // Eliminar un recorrido
    public function delete($id)
    {
        if ($this->tienePermiso('borrar abm')) {
            $permisos = $this->load_model("RecorridosPermisosModel")->getPermisosByRecorrido($id);
            if (empty($permisos)) {

                $eliminado = $this->model->deleteRecorrido($id);
                if (!$eliminado) {
                    $_SESSION['error_recorrido'] = "Error al eliminar el recorrido.";
                    header("Location: " . URL . "/recorrido");
                    exit;
                }
                header("Location: " . URL . "/recorrido");
                exit;
            }
            
            $ids_permisos = $permisos ? array_column($permisos, 'id_permiso') : [];
            $string_permisos = implode(', ', $ids_permisos);
            $_SESSION['error_recorrido'] = "No se puede eliminar el recorrido, tiene los siguientes permisos asignados: ". $string_permisos;
            header("Location: " . URL . "/recorrido");
            exit;
        }
    }

    public function saveAjax()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre']);
            $calles = $_POST['calles'];

            if ($nombre === '') {
                echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
                return;
            }

            $idRecorrido = $this->model->insertRecorrido($nombre);

            if ($idRecorrido) {
                foreach ($calles as $idCalle) {
                    if(!$this->calleRecorridoModel->insertCalleRecorrido($idRecorrido, id_calle: $idCalle)){
                        echo json_encode(['success' => false, 'message' => 'Error al guardar las calles del recorrido']);
                    };
                }
                echo json_encode([
                    'success' => true,
                    'id_recorrido' => $idRecorrido,
                    'nombre' => $nombre
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar recorrido']);
            }
        }
    }

    public function calles($id_recorrido)
    {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $calles = $this->calleRecorridoModel->getCallesByRecorrido($id_recorrido);
            if ($calles !== false) {
                echo json_encode($calles);
            } else {
                echo json_encode([]);
            }
        } else {
        }
    }
}
