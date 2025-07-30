<?php
/**
 * Controlador para manejar las operaciones relacionadas con los servicios.
 */
class Servicio extends Control
{
    private ServicioModel $model;

    public function __construct()
    {
        $this->model = $this->load_model('ServicioModel');
        
    }

    // Mostrar todos los servicios
    public function index()
    {
        $servicios = $this->model->getAllServicios();
        $datos = [
            'title' => 'Listado de Servicios',
            'urlCrear' => URL . '/servicios/create',
            'columnas' => ['Nombre','Dominio','Empresa'],
            'columnas_claves' => ['nombre','dominio','empresa'],
            'data' => $servicios,
            'acciones' => function($fila) {
                $id = $fila['id_servicio'];
                $url = URL . '/servicios';
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
        $empresas = (new EmpresaModel())->getAllEmpresas();
        $this->load_view('servicios/create', ['empresas' => $empresas]);
    }

    // Procesar el formulario de creación
    public function store()
    {
        $id_empresa = $_POST['id_empresa'] ?? '';
        $interno = trim($_POST['interno'] ?? '');
        $dominio = trim($_POST['dominio'] ?? '');

        if ($id_empresa === '' || $interno === '' || $dominio === '') {
            $empresas = (new EmpresaModel())->getAllEmpresas();
            $this->load_view('servicios/create', [
                'error' => 'Todos los campos son obligatorios.',
                'empresas' => $empresas,
                'interno' => $interno,
                'dominio' => $dominio,
                'id_empresa' => $id_empresa
            ]);
            return;
        }

        $this->model->insertServicio($id_empresa, $interno, $dominio);
        $this->load_view('servicios/index', [
            'message' => 'Servicio creado exitosamente.',
            'servicios' => $this->model->getAllServicios()
        ]);
    }

    // Formulario para editar un servicio
    public function edit($id)
    {
        $servicio = $this->model->getServicio($id);
        $empresas = (new EmpresaModel())->getAllEmpresas();

        if (!$servicio) {
            $this->load_view('servicios/index', [
                'error' => 'Servicio no encontrado.',
                'servicios' => $this->model->getAllServicios()
            ]);
            return;
        }

        $this->load_view('servicios/edit', [
            'servicio' => $servicio,
            'empresas' => $empresas
        ]);
    }

    // Procesar actualización
    public function update($id)
    {
        $id_empresa = $_POST['id_empresa'] ?? '';
        $interno = trim($_POST['interno'] ?? '');
        $dominio = trim($_POST['dominio'] ?? '');

        if ($id_empresa === '' || $interno === '' || $dominio === '') {
            $empresas = (new EmpresaModel())->getAllEmpresas();
            $servicio = $this->model->getServicio($id);

            $this->load_view('servicios/edit', [
                'error' => 'Todos los campos son obligatorios.',
                'servicio' => $servicio,
                'empresas' => $empresas
            ]);
            return;
        }

        $this->model->updateServicio($id, $id_empresa, $interno, $dominio);

        $this->load_view('servicios/index', [
            'message' => 'Servicio actualizado correctamente.',
            'servicios' => $this->model->getAllServicios()
        ]);
    }

    // Eliminar un servicio
    public function delete($id)
    {
        $eliminado = $this->model->deleteServicio($id);
        $servicios = $this->model->getAllServicios();

        if (!$eliminado) {
            $this->load_view('servicios/index', [
                'error' => 'No se pudo eliminar: el servicio no existe.',
                'servicios' => $servicios
            ]);
            return;
        }

        $this->load_view('servicios/index', [
            'message' => 'Servicio eliminado correctamente.',
            'servicios' => $servicios
        ]);
    }
}
