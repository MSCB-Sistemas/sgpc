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
        $this->model = $this->load_model('ServicioModel');
        $this->empresaModel = $this->load_model('EmpresaModel');
    }

    // Mostrar todos los servicios
    public function index()
    {
        $servicios = $this->model->getAllServicios();
        $datos = [
            'title' => 'Listado de Servicios',
            'urlCrear' => URL . '/servicio/create',
            'columnas' => ['Nro Servicio', 'Empresa', 'Interno', 'Dominio'],
            'columnas_claves' => ['id_servicio', 'nombre_empresa', 'interno', 'dominio'],
            'data' => $servicios,
            'acciones' => function($fila) {
                $id = $fila['id_servicio'];
                $url = URL . '/servicio';
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este servicio?\');">Eliminar</a>
                ';
            }
        ];
        $this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar detalles de un servicio
    public function show($id)
    {
        $servicio = $this->model->getServicio($id);

        if (!$servicio) {
            $this->load_view('servicios/index', [
                'error' => 'Servicio no encontrado.',
                'servicios' => $this->model->getAllServicios()
            ]);
            return;
        }

        $this->load_view('servicios/show', ['servicio' => $servicio]);
    }

    // Formulario para crear un nuevo servicio
    public function create()
    {
        $empresas = $this->empresaModel->getAllEmpresas();
        $this->load_view('servicios/form', [
            'title' => 'Crear nuevo servicio',
            'action' => URL . '/servicio/save',
            'values' => [],
            'errores' => [],
            'empresas' => $empresas
        ]);
    }

    // Procesar el formulario de creación
    public function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $empresa = $_POST["empresa"] ?? '';
            $interno = trim($_POST["interno"] ?? '');
            $dominio = trim($_POST["dominio"] ?? '');

            $errores = [];
            if (empty($empresa)) { $errores[] = 'La empresa es obligatoria'; }
            if (empty($interno)) { $errores[] = 'El interno es obligatorio'; }
            if (empty($dominio)) { $errores[] = 'El dominio es obligatorio'; }

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
            
            if ($this->model->insertServicio($empresa, $interno, $dominio)) {
                header("Location: " . URL . "/servicio/index");
                exit;
            } else {
                die("Error al guardar el servicio.");
            }
        }
    }

    // Formulario para editar un servicio
    public function edit($id)
    {
        $servicio = $this->model->getServicio($id);
        $empresas = $this->empresaModel->getAllEmpresas();

        if (!$servicio) {
            die("Servicio no encontrado.");
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

    // Procesar actualización
    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $empresa = $_POST['empresa'] ?? '';
            $interno = trim($_POST['interno'] ?? '');
            $dominio = trim($_POST['dominio'] ?? '');

            $errores = [];
            if (empty($empresa)) { $errores[] = 'La empresa es obligatoria.'; }
            if (empty($interno)) { $errores[] = 'El interno es obligatorio.'; }
            if (empty($dominio)) { $errores[] = 'El dominio es obligatorio'; }

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

            if ($this->model->updateServicio($id, $empresa, $interno, $dominio)) {
                header("Location: " . URL . "/servicio/index");
                exit;
            } else {
                die("Error al actualizar el servicio.");
            }
        }
    }

    // Eliminar un servicio
    public function delete($id)
    {
        $eliminado = $this->model->deleteServicio($id);

        if (!$eliminado) {
            die("Error al eliminar el servicio.");
        }
        header("Location: " . URL . "/servicio/index");
        exit;
    }
}
