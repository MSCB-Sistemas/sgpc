<?php
/**
 * Controlador de la tabla empresa.
 */
class Empresa extends Control
{
    private EmpresaModel $model;

    public function __construct()
    {
        $this->model = $this->load_model('EmpresaModel');
    }

    // Mostrar listado de empresas
    public function index()
    {
        $empresas = $this->model->getAllEmpresas();
        $this->load_view('/empresas/list');
    }

    // Mostrar formulario para crear empresa.
    public function create()
    {
        $this->load_view('empresas/create');
    }

    // Formulario para guardar una empresa.
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

            if ($nombre !== '') {
                $this->model->insertEmpresa($nombre);
                $this->load_view('empresas/create', [
                    'message' => 'Empresa creada exitosamente.'
                ]);
            } else {
                $this->load_view('empresas/create', [
                    'error' => 'El nombre no puede estar vacío.'
                ]);
            }
        } else {
            $this->load_view('empresas/create', [
                'error' => 'Solicitud inválida.'
            ]);
        }
    }


    // Mostrar formulario para editar empresa.
    public function edit($id)
    {
        $empresa = $this->model->getEmpresa($id);

        if (!$empresa) {
            $this->load_view('empresas/index', [
                'error' => 'Empresa no encontrada.'
            ]);
            return;
        }

        $this->load_view('empresas/edit', [
            'empresa' => $empresa
        ]);
    }


    // Actualizar empresa (desde formulario).
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_empresa'], $_POST['nombre'])) {
            $id = intval($_POST['id_empresa']);
            $nombre = trim($_POST['nombre']);

            if ($nombre !== '') {
                $this->model->updateEmpresa($id, $nombre);

                // Obtener todas las empresas para mostrar el index con mensaje
                $empresas = $this->model->getAllEmpresas(); 
                $this->load_view('empresas/index', [
                    'message' => 'Empresa actualizada correctamente.',
                    'empresas' => $empresas
                ]);
            } else {
                // Obtener la empresa para volver a mostrar el formulario con error
                $empresa = $this->model->getEmpresa($id);
                $this->load_view('empresas/edit', [
                    'empresa' => $empresa,
                    'error' => 'El nombre no puede estar vacío.'
                ]);
            }
        } else {
            $empresas = $this->model->getAllEmpresas(); 
            $this->load_view('empresas/index', [
                'error' => 'Solicitud inválida.',
                'empresas' => $empresas
            ]);
        }
    }


    // Eliminar empresa.
    public function delete($id)
    {
        $this->model->deleteEmpresa($id);
        $empresas = $this->model->getAllEmpresas();
        $this->load_view('empresas/index', [
        'message' => 'Empresa eliminada correctamente.',
        'empresas' => $empresas
        ]);

    }
}