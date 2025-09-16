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
        if ($this->tienePermiso("ver abm")){
            $errores = [];
            if (isset($_SESSION['error_empresa'])) {
                $errores[] = $_SESSION['error_empresa'];
                unset($_SESSION['error_empresa']); // Borramos el mensaje después de usarlo
            }
            $datos = [
                'title' => 'Listado de Empresas',
                'urlCrear' => URL . '/empresa/create',
                'urlAjax' => URL . '/empresa/ajaxList',
                'columnas' => ['Nombre de Empresa'],
                'columnas_claves' => ['nombre'],
                'acciones' => true,
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
        if ($this->tienePermiso("editar abm")) {
            $empresa = $this->model->getEmpresa($id);

            if (!$empresa) {
                $_SESSION['error_empresa'] = "Empresa no encontrada.";
                header("Location: " . URL . "/empresa");
                exit;
            }

            $this->load_view('empresas/form', [
                'title' => 'Editar empresa',
                'action' => URL . '/empresa/update/' . $id,
                'values' => [
                    'nombre' => $empresa['nombre']
                ],
                'errores' => [],
            ]);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function update($id)
    {
        if ($this->tienePermiso("editar abm")) {
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
                        $_SESSION['error_empresa'] = "Error al actualizar la empresa.";
                        header("Location: " . URL . "/empresa");
                        exit;
                    }
                }
            }
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function create()
    {
        if ($this->tienePermiso("cargar abm")) {
            $this->load_view('empresas/form', [
                'title' => 'Crear nueva empresa',
                'action' => URL . '/empresa/save',
                'values' => [],
                'errores' => [],
            ]);
        } else {
            header("Location: " . URL);
            exit;
        }
    }
    
    public function save()
    {
        if ($this->tienePermiso("cargar abm")) {
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
                        $_SESSION['error_empresa'] = "Error al guardar la empresa.";
                        header("Location: " . URL . "/empresa");
                        exit;
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
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function delete($id){
        if ($this->tienePermiso("borrar abm")) {
            $serviciosModel = $this->load_model("ServicioModel");
            $servicios = $serviciosModel->getServicioByEmpresa($id);
            if (empty($servicios)) {
                $eliminado = $this->model->deleteEmpresa($id);

                if (!$eliminado) {
                    $_SESSION['error_empresa'] = "Error al eliminar la empresa.";
                    header("Location: " . URL . "/empresa");
                    exit;
                }
                header("Location: " . URL . "/empresa");
                exit;
            }
            
            $internos = $servicios ? array_column($servicios, 'interno') : [];
            $string_internos = implode(', ', $internos);
            $_SESSION['error_empresa'] = "No se puede eliminar la empresa, tiene asignados los servicios con numero de interno: ". $string_internos;
            header("Location: " . URL . "/empresa");
            exit;
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function ajaxList()
    {
        // Solo permitir acceso con permisos
        if (!$this->tienePermiso("ver abm")) {
            header("Location: " . URL);
            exit;
        }

        // Parámetros que envía DataTables
        $draw = 1;
        if (isset($_GET['draw'])) {
            $draw = $_GET['draw'];
        }
        $start = 0;
        if (isset($_GET['start'])) {
            $start = $_GET['start'];
        }
        $length = 10;
        if (isset($_GET['length'])) {
            $length = $_GET['length'];
        }
        $searchValue = '';
        if (isset($_GET['search']['value'])) {
            $searchValue = $_GET['search']['value'];
        }

        // Orden
        $orderColumnIndex = 0;
        if (isset($_GET['order'][0]['column'])) {
            $orderColumnIndex = $_GET['order'][0]['column'];
        }
        $orderDir = 'asc';
        if (isset($_GET['order'][0]['dir'])) {
            $orderDir = $_GET['order'][0]['dir'];
        }

        $columnas = ['nombre'];

        $orderColumn = 'nombre';
        if (isset($columnas[$orderColumnIndex])) {
            $orderColumn = $columnas[$orderColumnIndex];
        }

        // Total de registros (sin filtro)
        $recordsTotal = $this->model->contarEmpresas();

        // Registros filtrados y paginados
        $records = $this->model->getEmpresasServerSide($start, $length, $searchValue, $orderColumn, $orderDir);

        // Total de registros filtrados
        $recordsFiltered = $this->model->contarEmpresasFiltradas($searchValue);

        // Preparar data con botones de acciones
        $data = [];
        foreach ($records as $fila) {
            $acciones = '';
            $id = $fila['id_empresa'];
            $url = URL . '/empresa';
            if ($this->tienePermiso('editar abm')){
                $acciones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a>';
            }
            if ($this->tienePermiso('borrar abm')){
                $acciones .= '<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Eliminar esta Empresa?\');">Eliminar</a>';
            }

            $data[] = [
                'nombre' => ucfirst(htmlspecialchars($fila['nombre'])),
                'acciones' => $acciones
            ];
        }

        // Respuesta en JSON
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => intval($recordsTotal),
            "recordsFiltered" => intval($recordsFiltered),
            "data" => $data
        ]);
        exit;
    }
}