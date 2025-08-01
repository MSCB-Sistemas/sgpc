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
            $recorrido['calles'] = $calles ? implode(', ', $calles) : 'Sin calles';
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
            $this->load_view('recorridos/index', [
                'error' => 'Recorrido no encontrado.',
                'recorridos' => $this->model->getAllRecorridos()
            ]);
            return;
        }

        $this->load_view('recorridos/edit', ['recorrido' => $recorrido]);
    }

    // Procesar edición
    public function update($id)
    {
        $nombre = trim($_POST['nombre'] ?? '');

        if ($nombre === '') {
            $recorrido = $this->model->getRecorrido($id);
            $this->load_view('recorridos/edit', [
                'error' => 'El nombre es obligatorio.',
                'recorrido' => $recorrido
            ]);
            return;
        }

        $this->model->updateRecorrido($id, $nombre);

        $this->load_view('recorridos/index', [
            'message' => 'Recorrido actualizado correctamente.',
            'recorridos' => $this->model->getAllRecorridos()
        ]);
    }

    // Eliminar un recorrido
    public function delete($id)
    {
        $eliminado = $this->model->deleteRecorrido($id);

        $recorridos = $this->model->getAllRecorridos();

        if (!$eliminado) {
            $this->load_view('recorridos/index', [
                'error' => 'No se pudo eliminar el recorrido.',
                'recorridos' => $recorridos
            ]);
            return;
        }

        $this->load_view('recorridos/index', [
            'message' => 'Recorrido eliminado correctamente.',
            'recorridos' => $recorridos
        ]);
    }
}
