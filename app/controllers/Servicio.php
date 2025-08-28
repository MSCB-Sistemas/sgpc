<?php
/**
 * Controlador para manejar las operaciones relacionadas con los servicios.
 */
class Servicio extends Control
{
    private $model;
    private $empresaModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('ServicioModel');
        $this->empresaModel = $this->load_model('EmpresaModel');
    }

    // Mostrar todos los servicios
    public function index($errores = [])
    {
        if ($this->tienePermiso('ver abm')) {
            $servicios = $this->model->getAllServicios();
            $datos = [
                'title' => 'Listado de Servicios',
                'urlCrear' => URL . '/servicio/create',
                'columnas' => ['Nro Servicio', 'Empresa', 'Interno', 'Dominio'],
                'columnas_claves' => ['id_servicio', 'nombre_empresa', 'interno', 'dominio'],
                'data' => $servicios,
                'acciones' => function ($fila) {
                    $id = $fila['id_servicio'];
                    $url = URL . '/servicio';
                    $botones = '';

                    if ($this->tienePermiso('editar abm') && $this->tienePermiso('borrar abm')) {
                        $botones .= '
                            <a href="' . $url . '/edit/' . $id . '" class="btn btn-sm btn-outline-primary">Editar</a>
                        ';
                    }

                    if ($this->tienePermiso('borrar abm')) {
                        $botones .= '
                            <a href="' . $url . '/delete/' . $id . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este servicio?\');">Eliminar</a>
                        ';
                    }

                    return $botones;
                },
                'errores' => $errores
            ];
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function create()
    {
        if ($this->tienePermiso('cargar abm')) {
            $empresas = $this->empresaModel->getAllEmpresas();
            $this->load_view('servicios/form', [
                'title' => 'Crear nuevo servicio',
                'action' => URL . '/servicio/save',
                'values' => [],
                'errores' => [],
                'empresas' => $empresas
            ]);
        }
    }

    // Procesar el formulario de creación
    public function save()
    {
        if ($this->tienePermiso('cargar abm')) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $empresa = isset($_POST["empresa"]) ? trim($_POST['empresa']) : '';
                $interno = isset($_POST['interno']) ? trim($_POST['interno']) : '';
                $dominio = isset($_POST['dominio']) ? trim($_POST['dominio']) : '';

                $errores = [];
                if ($empresa === '') $errores[] = 'La empresa es obligatoria';
                if ($interno === '') $errores[] = 'El interno es obligatorio.';
                if ($dominio === '') $errores[] = 'El dominio es obligatorio';

                if (!empty($errores)) {
                    $empresas = $this->empresaModel->getAllEmpresas();
                    $this->load_view('servicios/form', [
                        'title' => 'Crear nuevo servicio',
                        'action' => URL . '/servicio/guardar',
                        'values' => $_POST,
                        'errores' => $errores,
                        'empresas' => $empresas
                    ]);
                    return;
                }

                try {
                    $this->model->insertServicio($empresa, $interno, $dominio);
                    header("Location: " . URL . "/servicio/index");
                    exit;
                } catch (\PDOException $e) {
                    $empresaData = $this->empresaModel->getEmpresa($empresa);
                    $empresaNombre = $empresaData['nombre'];

                    if ($e->getCode() == 23000) {
                        $errores[] = "El servicio ($empresaNombre, $interno, $dominio) ya existe.";
                    } else {
                        $errores[] = "Error al guardar el servicio: " . $e->getMessage();
                    }
                    $empresas = $this->empresaModel->getAllEmpresas();
                    $this->load_view('servicios/form', [
                        'title' => 'Crear nuevo servicio',
                        'action' => URL . '/servicio/save',
                        'values' => $_POST,
                        'errores' => $errores,
                        'empresas' => $empresas
                    ]);
                }
            }
        }
    }

    public function edit($id)
    {
        if ($this->tienePermiso('editar abm')) {
            $servicio = $this->model->getServicio($id);
            $empresas = $this->empresaModel->getAllEmpresas();
            
            $permisos = $this->load_model("permisoModel")->getPermisosByServicio($id);

            if (!$servicio) {
                die("Servicio no encontrado.");
            }

            if (!empty($permisos)){
                $errores[] = 'Error: No se puede editar un servicio con permisos asignados.';
                $this->index($errores);
            }

            $this->load_view('servicios/form', [
                'title' => 'Editar servicio',
                'action' => URL . '/servicio/update/' . $id,
                'values' => [
                    'empresa' => $servicio['id_empresa'],
                    'interno' => $servicio['interno'],
                    'dominio' => $servicio['dominio'],
                ],
                'errores' => [],
                'empresas' => $empresas
            ]);
        }
    }

    // Procesar actualización
    public function update($id)
    {
        if ($this->tienePermiso('editar abm')) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST['empresa'])) {
                    $empresa = trim($_POST['empresa'] );
                } else {
                    $empresa = '';
                }
                
                if(isset($_POST['interno'])) {
                    $interno = trim($_POST['interno'] );
                } else {
                    $interno = '';
                }

                if(isset($_POST['dominio'])) {
                    $dominio = trim($_POST['dominio'] );
                } else {
                    $dominio = '';
                }

                $errores = [];
                if ($empresa === '') {
                    $errores[] = 'La empresa es obligatoria';
                    }
                if ($interno === '') {
                    $errores[] = 'El interno es obligatorio.'; 
                    }
                if ($dominio === '') { 
                    $errores[] = 'El dominio es obligatorio'; }

                if (!empty($errores)) {
                    $servicio = [
                        'id_servicio' => $id,
                        'empresa' => $empresa,
                        'interno' => $interno,
                        'dominio' => $dominio
                    ];
                    $empresas = $this->empresaModel->getAllEmpresas();
                    $this->load_view('servicios/form', [
                        'title' => 'Editar Servicio',
                        'action' => URL . '/servicio/update/' . $id,
                        'values' => $servicio,
                        'errores' => $errores,
                        'empresas' => $empresas
                    ]);
                    return;
                }
                try {
                    if ($this->model->updateServicio($id, $empresa, $interno, $dominio)) {
                        header("Location: " . URL . "/servicio/index");
                        exit;
                    } else {
                        die("Error al actualizar el servicio.");
                    }
                } catch (\PDOException $e) {
                    $empresaData = $this->empresaModel->getEmpresa($empresa);
                    $empresaNombre = $empresaData['nombre'];

                    if ($e->getCode() == 23000) {
                        $errores[] = "El servicio ($empresaNombre, $interno, $dominio) ya existe.";
                    } else {
                        $errores[] = "Error al guardar el servicio: " . $e->getMessage();
                    }
                    $empresas = $this->empresaModel->getAllEmpresas();
                    $this->load_view('servicios/form', [
                        'title' => 'Crear nuevo servicio',
                        'action' => URL . '/servicio/save',
                        'values' => $_POST,
                        'errores' => $errores,
                        'empresas' => $empresas
                    ]);
                }
            }
        }
    }

    public function delete($id)
    {
        if ($this->tienePermiso('borrar abm')) {
            $permisos = $this->load_model("permisoModel")->getPermisosByServicio($id);

            if (empty($permisos)) {
                $eliminado = $this->model->deleteServicio($id);

                if (!$eliminado) {
                    $this->index(["Error al eliminar el servicio"]);
                }
                header("Location: " . URL . "/servicio");
                exit;
            }
            
            $ids_permisos = $permisos ? array_column($permisos, 'id_permiso') : [];
            $string_permisos = implode(', ', $ids_permisos);
            $this->index(["No se puede eliminar el servicio, tiene los siguientes permisos asignados: ". $string_permisos]);
        }
    }
    
    public function saveAjax()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $interno = trim($_POST['interno']);
            $dominio = trim($_POST['dominio']);
            if (!empty($_POST['nueva_empresa'])) {
                $id_empresa = $this->empresaModel->insertEmpresa(trim($_POST['nueva_empresa']));
            } else {
                $id_empresa = intval($_POST['empresa']);
            }
            

            if ($interno === '' || $dominio === '' || !$id_empresa) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                return;
            }

            $idServicio = $this->model->insertServicio($id_empresa, $interno, $dominio);
            if ($idServicio) {
                echo json_encode([
                    'success' => true,
                    'id_servicio' => $idServicio,
                    'internoDominio' => $interno . ' - ' . $dominio
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar servicio']);
            }
        }
    }

}

