<?php
require_once __DIR__ . '/../models/CalleRecorridoModel.php';

/**
 * Controlador de la tabla `calles_recorridos`
 *
 * Gestiona las modificaciones de la tabla calles_recorridos.
 */
class CalleRecorridoController
{
    private CalleRecorridoModel $model;

    public function __construct()
    {
        $this->model = new CalleRecorridoModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } 
    }

    /**
     * Muestra todos los registros. 
     */
    public function index()
    {
        $callesRecorridos = $this->model->getAllCallesRecorridos();
        require __DIR__ . '/../views/calles_recorridos/index.php';
    }

    /**
     * Muestra un registro por ID.
     */
    public function show($id)
    {
        $calleRecorrido = $this->model->getCalleRecorrido($id);
        if (!$calleRecorrido) {
            $_SESSION['error'] = "Registro no encontrado.";
            header("Location: calles_recorridos.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/calles_recorridos/show.php';
    }

    /**
     * Muestra el formulario de creación.
     */
    public function create()
    {
        
        require __DIR__ . '/../views/calles_recorridos/create.php';
    }

    /**
     * Procesa el formulario y guarda en DB.
     */
    public function store()
    {
        $id_recorrido = $_POST['id_recorrido'] ?? null;
        $id_calle = $_POST['id_calle'] ?? null;

        if (empty($id_recorrido) || empty($id_calle)) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header("Location: calles_recorridos.php?action=create");
            exit();
        }

        $this->model->insertCalleRecorrido($id_recorrido, $id_calle);
        header("Location: calles_recorridos.php?action=index");
        exit();
    }

    /**
     * Muestra formulario de edición.
     */
    public function edit($id)
    {
        $calleRecorrido = $this->model->getCalleRecorrido($id);
        if (!$calleRecorrido) {
            $_SESSION['error'] = "Relación no encontrada.";
            header("Location: calles_recorridos.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/calles_recorridos/edit.php';
    }

    /**
     * Procesa la actualización.
     */
    public function update($id)
    {
        $id_recorrido = $_POST['id_recorrido'] ?? null;
        $id_calle = $_POST['id_calle'] ?? null;

        if (empty($id_recorrido) || empty($id_calle)) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header("Location: calles_recorridos.php?action=edit&id=$id");
            exit();
        }
        $this->model->updateCalleRecorrido($id, $id_recorrido, $id_calle);
        header("Location: calles_recorridos.php?action=index");
        exit();
    }

    /**
     * Elimina un registro.
     */
    public function delete($id)
    {
        if (!$this->model->deleteCalleRecorrido($id)) {
            $_SESSION['error'] = "No se pudo eliminar: el registro no existe.";
            header("Location: calles_recorridos.php?action=index");
            exit();
        }
        header("Location: calles_recorridos.php?action=index");
        exit();
    }
}
