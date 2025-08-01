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
            $id_recorrido = $_POST["id_recorrido"] ?? null;
            $calle = $_POST["calle"] ?? '';
            $nombre = trim($_POST['nombre']);

            $errores = [];
            if (!empty($calle)) { $errores[] = 'La calle es obligatoria'; }
            if (!empty($nombre)) { $errores[] = 'El nombre de la ruta es obligatorio'; }

            if (!empty($errores)) {
                $calles = $this->calleModel->getAllCalles();
                $this->load_view('recorridos/form', [
                    'title' => 'Crear Recorrido',
                    'action'=> URL . 'recorrido/save',
                    'values' => $_POST,
                    'errores' => $errores,
                    'calles' => $calles
                ]);

                return;
            }

            if ($this->model->insertRecorrido($nombre) && $this->calleRecorridoModel->insertCalleRecorrido($id_recorrido, $calle)) {
                header("Location : " . URL . "/recorrido/index");
                exit;
            } else {
                die("Error al guardar el recorrido.");
            }
        }
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
