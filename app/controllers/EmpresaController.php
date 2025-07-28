<?php
require_once __DIR__ . '/../models/EmpresaModel.php';

/**
 * Controlador de la tabla empresa.
 */
class EmpresaController
{
    private $model;

    public function __construct()
    {
        $this->model = new EmpresaModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Mostrar listado de empresas
    public function index()
    {
        $empresas = $this->model->getAllEmpresas();
        include __DIR__ . '/../views/empresas/list.php';
    }

    // Mostrar formulario para crear empresa.
    public function create()
    {
        include __DIR__ . '/../views/empresas/create.php';
    }

    // Formulario para guardar una empresa.
    public function store()
    {
        if (isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
            if ($nombre !== '') {
                $this->model->insertEmpresa($nombre);
                header('Location: empresas.php?action=index');
                exit;
            } else {
                $_SESSION['error'] = "El nombre no puede estar vacío";
                header('Location: empresas.php?action=create');
                exit;
            }
        } else {
            $_SESSION['error'] = "Solicitud inválida";
            header('Location: empresas.php?action=create');
            exit;
        }
    }

    // Mostrar formulario para editar empresa.
    public function edit($id)
    {
        $empresa = $this->model->getEmpresa($id);
        if (!$empresa) {
            $_SESSION['error'] = "Empresa no encontrada.";
            header('Location: empresas.php?action=index');
            exit;
        }
        include __DIR__ . '/../views/empresas/edit.php';
    }

    // Actualizar empresa (desde formulario).
    public function update()
    {
        if (isset($_POST['id_empresa'], $_POST['nombre'])) {
            $id = intval($_POST['id_empresa']);
            $nombre = trim($_POST['nombre']);
            if ($nombre !== '') {
                $this->model->updateEmpresa($id, $nombre);
                header('Location: empresas.php?action=index');
                exit;
            } else {
                $_SESSION['error'] = "El nombre no puede estar vacío";
                header('Location: empresas.php?action=edit&id=' . $id);
                exit;
            }
        } else {
            $_SESSION['error'] = "Solicitud inválida";
            header('Location: empresas.php?action=index');
            exit;
        }
    }

    // Eliminar empresa.
    public function delete($id)
    {
        $this->model->deleteEmpresa($id);
        header('Location: empresas.php?action=index');
        exit;
    }
}
