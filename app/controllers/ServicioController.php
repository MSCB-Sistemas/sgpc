<?php
require_once __DIR__ . '/../models/ServicioModel.php';

/**
 * Controlador para la tabla `servicios`
 *
 * Gestiona la table servicios asociados a empresas.
 */
class ServicioController
{
    private ServicioModel $model;

    public function __construct()
    {
        $this->model = new ServicioModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } 
    }

    /**
     * Muestra todos los servicios.
     */
    public function index()
    {
        $servicios = $this->model->getAllServicios();
        require __DIR__ . '/../views/servicios/index.php';
    }

    /**
     * Muestra un servicio por su ID.
     */
    public function show($id)
    {
        $servicio = $this->model->getServicio($id);
        if (!$servicio) {
            $_SESSION['error'] = "Servicio no encontrado.";
            header("Location: servicios.php?action=index");
            exit();
        }
        require __DIR__ . '/../views/servicios/show.php';
    }

    /**
     * Muestra el formulario de creación. 
     */
    public function create()
    {
        require_once __DIR__ . '/../models/EmpresaModel.php';
        $empresas = (new EmpresaModel())->getAllEmpresas();
        require __DIR__ . '/../views/servicios/create.php';
    }

    /**
     * Procesa el formulario y guarda en DB.
     */
    public function store()
    {
        $id_empresa = $_POST['id_empresa'] ?? null;
        $interno = $_POST['interno'] ?? null;
        $dominio = $_POST['dominio'] ?? null;

        if (empty($id_empresa) || empty($interno) || empty($dominio)) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header("Location: servicios.php?action=create");
            exit();
        }

        $this->model->insertServicio($id_empresa, $interno, $dominio);
        header("Location: /servicios");
        exit();
    }

    /**
     * Muestra formulario de edición.
     */
    public function edit($id)
    {
        $servicio = $this->model->getServicio($id);
        if (!$servicio) {
            $_SESSION['error'] = "Servicio no encontrado.";
            header("Location: servicios.php?action=index");
            exit();
        }
        require_once __DIR__ . '/../models/EmpresaModel.php';
        $empresas = (new EmpresaModel())->getAllEmpresas();
        require __DIR__ . '/../views/servicios/edit.php';
    }

    /**
     * Procesa la actualización.
     */
    public function update($id)
    {
        $id_empresa = $_POST['id_empresa'] ?? null;
        $interno = $_POST['interno'] ?? null;
        $dominio = $_POST['dominio'] ?? null;

        if (empty($id_empresa) || empty($interno) || empty($dominio)) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header("Location: servicios.php?action=edit&id=$id");
            exit();
        }

        $this->model->updateServicio($id, $id_empresa, $interno, $dominio);
        header("Location: /servicios");
        exit();
    }

    /**
     * Elimina un servicio.
     */
    public function delete($id)
    {
        if (!$this->model->deleteServicio($id)) {
            $_SESSION['error'] = "No se pudo eliminar: el servicio no existe.";
            header("Location: servicios.php?action=index");
            exit();
        }

        header("Location: /servicios");
        exit();
    }
}
