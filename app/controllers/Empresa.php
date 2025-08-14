<?php
/**
 * Controlador de la tabla empresa.
 */
class Empresa extends Control
{
    private EmpresaModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('EmpresaModel');
    }

    // Mostrar listado de empresas
    public function index()
    {
        $empresas = $this->model->getAllEmpresas();
        $datos = [
            'title' => 'Listado de Empresas',
            'urlCrear' => URL . '/empresa/create',
            'columnas' => ['Nombre de Empresa'],
            'columnas_claves' => ['nombre'],
            'data' => $empresas,
            'acciones' => function($fila) {
                $id = $fila['id_empresa'];
                $url = URL . '/empresa';
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar esta Empresa?\');">Eliminar</a>
                ';
            }
        ];
        $this->load_view('partials/tablaAbm', $datos);
    }
    public function edit($id)
    {
        $empresa = $this->model->getEmpresa($id);

        if (!$empresa) {
            die("Empresa no encontrada");
        }

        $this->load_view('empresas/form', [
            'title' => 'Editar empresa',
            'action' => URL . '/empresa/update/' . $id,
            'values' => [
                'nombre' => $empresa['nombre']
            ],
            'errores' => [],
        ]);
    }

    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"]);

            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";

            if (!empty($errores)) {
                $empresa = [
                    'id_empresa' => $id,
                    'nombre' => $nombre
                ];
                $this->load_view('empresas/form', [
                    'title' => 'Editar empresa',
                    'action' => URL . '/empresa/update/' . $id,
                    'values' => $empresa,
                    'errores' => $errores
                ]);
                return;
            }

            if ($this->model->updateEmpresa($id,$nombre)) {
                header("Location: " . URL . "/empresa");
                exit;
            } else {
                die("Error al actualizar la empresa");
            }
        }
    }

    public function create()
    {
        $this->load_view('empresas/form', [
            'title' => 'Crear nueva empresa',
            'action' => URL . '/empresa/save',
            'values' => [],
            'errores' => [],
        ]);
    }
    
    public function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"]);

            // Validaciones simples
            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";

            if (!empty($errores)) {
                $this->load_view('empresas/form', [
                    'title' => 'Crear nueva empresa',
                    'action' => URL . '/empresa/save',
                    'values' => $_POST,
                    'errores' => $errores
                ]);
                return;
            }

            if ($this->model->insertEmpresa($nombre)) {
                header("Location: " . URL . "/empresa");
                exit;
            } else {
                die("Error al guardar la empresa");
            }
        }
    }

    public function delete($id){
        $serviciosModel = $this->load_model("ServicioModel");
        $servicios = $serviciosModel->getServicioByEmpresa($id);
        if (empty($servicios)) {
            $this->model->deleteEmpresa($id);
            header("Location: " . URL . "/empresa");
            exit;
        }
            die("No se puede eliminar la empresa, tiene servicios asignados.");
    }
}