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
    public function index($errores = [])
    {
        if (in_array('ver abm',$_SESSION['usuario_derechos'])){
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
                },
                'errores' => $errores
            ];
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
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
            $permisos = $this->model->getPermisosByEmpresa($id);

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

            if(!empty($permisos)){
                $this->model->desactivarEmpresa($id);
                $this->model->insertEmpresa($nombre);
                header("Location: " . URL . "/empresa");
                exit;
            } else {
                if ($this->model->updateEmpresa($id,$nombre)) {
                    header("Location: " . URL . "/empresa");
                    exit;
                } else {
                    die("Error al actualizar la empresa");
                }
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

            try{
                if ($this->model->insertEmpresa($nombre)) {
                    header("Location: " . URL . "/empresa");
                    exit;
                } else {
                    die("Error al guardar la empresa");
                }
            } catch (Exception $e) {
                if ($e->getCode() == 23000) {
                    $errores[] = "La empresa '{$_POST['nombre']}' ya existe en el sistema.";
                } else {
                    $errores[] = "Error al guardar la empresa: " . $e->getMessage();
                }
                $this->load_view('empresas/form', [
                    'title' => 'Crear nueva empresa',
                    'action' => URL . '/empresa/save',
                    'values' => $_POST,
                    'errores' => $errores
                ]);
                return;
            }    
        }
    }

    public function delete($id){
        $serviciosModel = $this->load_model("ServicioModel");
        $servicios = $serviciosModel->getServicioByEmpresa($id);
        if (empty($servicios)) {
            $eliminado = $this->model->deleteEmpresa($id);

            if (!$eliminado) {
                $this->index(["Error al eliminar la empresa"]);
            }
            header("Location: " . URL . "/empresa");
            exit;
        }
        
        $internos = $servicios ? array_column($servicios, 'interno') : [];
        $string_internos = implode(', ', $internos);
        $this->index(["No se puede eliminar la empresa, tiene asignados los servicios con numero de interno: ". $string_internos]);
    }
}