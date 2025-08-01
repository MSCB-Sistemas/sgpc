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
        $this->model = $this->load_model("RecorridoModel"); 
        $this->calleRecorridoModel = $this->load_model("CalleRecorridoModel");
        $this->calleModel = $this->load_model("CalleModel");
    }

    // Mostrar todos los recorridos
    public function index()
    {
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
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este recorrido?\');">Eliminar</a>
                ';
            }
        ];
        $this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar formulario de creación
    public function create()
    {
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

    // Procesar creación
    public function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $nombre = trim($_POST['nombre']);
            $calles = $_POST['calles'] ?? [];

            $errores = [];
            if ($nombre === '') {
                $errores[] = "El nombre es obligatorio.";
            }
            if (empty($calles)) {
                $errores[] = "Debe seleccionar al menos una calle.";
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

            $idRecorrido = $this->model->insertRecorrido($nombre);
            if ($idRecorrido) {
                foreach ($calles as $idCalle) {
                    if(!$this->calleRecorridoModel->insertCalleRecorrido($idRecorrido, $idCalle)){
                        die("Error al guardar la calle del recorrido");
                    };
                }
            } else {
                die("Error al guardar el recorrido");
            }

            header('Location: ' . URL . '/recorrido');
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
    
    // Mostrar formulario de edición
    public function edit($id)
    {
        $recorrido = $this->model->getRecorrido($id);
        if (!$recorrido) {
            die("Recorrido no encontrado");
        }

        $calles = $this->calleModel->getAllCalles();
        $callesAsociadas = $this->calleRecorridoModel->getCallesByRecorrido($id);

        // mapear calles asociadas a id => nombre (para precargar tabla)
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

    // Procesar edición
    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $nombre = trim($_POST['nombre']);
            $calles = $_POST['calles'] ?? [];

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

            // actualizar nombre
            if (!$this->model->updateRecorrido($id, $nombre)) {
                die("Error al actualizar el recorrido");
            }

            // borrar calles viejas e insertar nuevas
            $this->calleRecorridoModel->deleteByRecorrido($id);
            foreach ($calles as $idCalle) {
                if (!$this->calleRecorridoModel->insertCalleRecorrido($id, $idCalle)) {
                    die("Error al guardar las calles del recorrido");
                }
            }

            header('Location: ' . URL . '/recorrido');
        }
    }


    // Eliminar un recorrido
    public function delete($id)
    {
        $eliminado = $this->model->deleteRecorrido($id);

        if (!$eliminado) {
            die("Error al eliminar el recorrido");
        }
        header("Location: " . URL . "/recorrido/index");
        exit;
    }

    public function saveAjax()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre']);

            if ($nombre === '') {
                echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
                return;
            }

            $idRecorrido = $this->model->insertRecorrido($nombre);
            if ($idRecorrido) {
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
}
