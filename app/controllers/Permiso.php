<?php
/**
 * Controlador para gestionar operaciones relacionadas con los permisos.
 */
require_once __DIR__ . '/../../vendor/autoload.php';
use Mpdf\Mpdf;

class Permiso extends Control
{
    private PermisoModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('PermisoModel');
    }

    // Mostrar lista de permisos
    public function index()
    {
        $permisos = $this->model->getAllPermisos();
        $datos = [
            'title' => 'Listado de Permisos',
            'urlCrear' => null, // Cambiado a null para no mostrar botón de crear''
            'columnas' => [
                'Tipo',
                'Fecha Reserva',
                'Fecha Emisión',
                'Chofer',
                'Usuario',
                'Servicio',
                'Dominio',
                'Empresa',
                'Pasajeros',
                'Origen/Destino',
                'Observación',
                'Arribo/Salida'
            ],
            'columnas_claves' => [
                'tipo',
                'fecha_reserva',
                'fecha_emision',
                'chofer',
                'usuario',
                'servicio_interno',
                'servicio_dominio',
                'empresa_nombre',
                'pasajeros',
                'lugar',
                'observacion',
                'arribo_salida'
            ],
            'data' => $permisos, 
            'acciones' => $_SESSION['usuario_tipo'] == '1' ? function($fila) {
                $id = $fila['id_permiso'];
                $url = URL . '/permisos';
                return '
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Desactivar este permiso?\');">Eliminar</a>
                ';
            } : null,
        ];

        $this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar detalles de un permiso específico
    public function show($id)
    {
        $permiso = $this->model->getPermiso($id);
        if (!$permiso) {
            $this->load_view('permisos/index', [
                'error' => 'Permiso no encontrado.',
                'permisos' => $this->model->getAllPermisos()
            ]);
            return;
        }
        $this->load_view('permisos/show', ['permiso' => $permiso]);
    }

    // Mostrar formulario para crear permiso
    public function nuevo($errores = [], $mensajes = [])
    {
        $servicios = $this->load_model('ServicioModel')->getAllServicios();
        $recorridos = $this->load_model('RecorridoModel')->getAllRecorridos();
        $choferes = $this->load_model('ChoferesModel')->getAllChoferes();
        $nacionalidades = $this->load_model('NacionalidadModel')->getAllNacionalidades();
        $empresas = $this->load_model('EmpresaModel')->getAllEmpresas();
        $calles = $this->load_model('CalleModel')->getAllCalles();
        $hoteles = $this->load_model('HotelesModel')->getAllHoteles();
        $lugares = $this->load_model('LugarModel')->getAllLugares();

        $this->load_view('permisos/form', [
            'title' => 'Nuevo Permiso',
            'action' => URL . '/permiso/store',
            'values' => [],
            'choferes' => $choferes,
            'servicios' => $servicios,
            'recorridos' => $recorridos,
            'nacionalidades' => $nacionalidades,
            'empresas' => $empresas,
            'calles' => $calles,
            'hoteles'=> $hoteles,
            'lugares' => $lugares,
            'errores' => $errores,
            'mensajes' => $mensajes
        ]);
    }
    

    // Procesar creación
    public function store()
    {
        $id_chofer = $_POST['id_chofer'] ?? null;
        $id_usuario = $_SESSION['usuario_id']  ?? null;
        $id_servicio = $_POST['id_servicio'] ?? null;
        $id_lugar = $_POST['id_lugar'] ?? null;
        $tipo = $_POST['tipo_permiso'] ?? '';
        $fecha_reserva = $_POST['fecha_reserva'] ?? '';
        $fecha_emision = date('Y-m-d H:i:s');
        $arribo_salida = $_POST['arribo_salida'] ?? '';
        $id_recorrido = $_POST['id_recorrido'] ?? null;
        $observacion = $_POST['observacion'] ?? null;
        $puntos_detencion = $_POST['puntos_detencion'] ?? '';
        $puntos_detencion = json_decode($puntos_detencion, true);
        $pasajeros = $_POST['pasajeros'] ?? 0;
        $errores = [];
        $mensajes = [];

        $modelRecorridosPermisos = $this->load_model('RecorridosPermisosModel');
        $modelReservasPuntos = $this->load_model('ReservasPuntosModel');

        if (!$id_chofer || !$id_usuario || !$id_servicio || $tipo === '' || $fecha_reserva === '' || $arribo_salida === '' || !$id_lugar) {
            
            var_dump($id_chofer, $id_usuario, $id_servicio, $tipo, $fecha_reserva, $fecha_emision, $arribo_salida, $observacion, $pasajeros, $id_lugar,$puntos_detencion,$id_recorrido);
            
            return;
        }

       $idPermiso = $this->model->insertPermiso(
            $id_chofer,
            $id_usuario,
            $id_servicio,
            $tipo,
            $fecha_reserva,
            $fecha_emision,
            $arribo_salida,
            $observacion,
            $pasajeros,
            $id_lugar
        );

        if (!$idPermiso) {
            $errores[] = 'Error al crear el permiso.';
        }

        $idPermisoRecorrido = $modelRecorridosPermisos->insertRecorrido($idPermiso, $id_recorrido);
        if (!$idPermisoRecorrido) {
            $errores[] = 'Error al asociar el recorrido al permiso.';
        }

        foreach ($puntos_detencion as $id_punto_detencion => $punto) {
            if (!empty($punto['horario'])) {
                $fecha_horario = $fecha_reserva . ' ' . $punto['horario'] . ':00';
                $id_reserva = $modelReservasPuntos->insertReservaPunto(
                   $fecha_horario,
                   isset($punto['hotel']) ? $punto['hotel'] : null,
                   $idPermiso,
                   $id_punto_detencion
                );
                if(!$id_reserva) {
                    $errores[] = "Error al crear la reserva para el punto de detención {$id_punto_detencion} en el horario {$punto['horario']}.";
                }
            }
        }
        if (empty($errores)) {
            $mensajes[] = "Permiso {$idPermiso} creado correctamente.";
            if (!empty($_POST['imprimir'])) {
                // Generar el PDF
                $this->generarPDFyMostrar($idPermiso); // función que devuelve la ruta del PDF
            }
        }

        $this->nuevo($errores, $mensajes);
    }

    public function generarPDFyMostrar($idPermiso) {
        $datos = $this->model->getPermiso($idPermiso);

        // Cargar plantilla en variable
        ob_start();
        include APP . '/views/pages/partials/permisoPdf.php';
        $html = ob_get_clean();

        // Generar PDF
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output("permiso_$idPermiso.pdf", \Mpdf\Output\Destination::INLINE);
    }

    // Mostrar formulario para editar permiso
    public function edit($id)
    {
        $permiso = $this->model->getPermiso($id);
        if (!$permiso) {
            $this->load_view('permisos/index', [
                'error' => 'Permiso no encontrado.',
                'permisos' => $this->model->getAllPermisos()
            ]);
            return;
        }
        $this->load_view('permisos/edit', ['permiso' => $permiso]);
    }

    // Procesar actualización
    public function update($id)
    {
        $id_chofer = $_POST['id_chofer'] ?? null;
        $id_usuario = $_POST['id_usuario'] ?? null;
        $id_servicio = $_POST['id_servicio'] ?? null;
        $tipo = $_POST['tipo'] ?? '';
        $fecha_reserva = $_POST['fecha_reserva'] ?? '';
        $fecha_emision = $_POST['fecha_emision'] ?? '';
        $es_arribo = isset($_POST['es_arribo']) ? 1 : 0;
        $observacion = $_POST['observacion'] ?? null;
        $activo = isset($_POST['activo']) ? 1 : 0;

        if (!$id_chofer || !$id_usuario || !$id_servicio || $tipo === '' || $fecha_reserva === '' || $fecha_emision === '') {
            $permiso = $this->model->getPermiso($id);
            $this->load_view('permisos/edit', [
                'error' => 'Todos los campos obligatorios deben estar completos.',
                'permiso' => $permiso
            ]);
            return;
        }

        $this->model->updatePermiso(
            $id,
            $id_chofer,
            $id_usuario,
            $id_servicio,
            $tipo,
            $fecha_reserva,
            $fecha_emision,
            $es_arribo,
            $observacion,
            $activo
        );

        $this->load_view('permisos/index', [
            'message' => 'Permiso actualizado correctamente.',
            'permisos' => $this->model->getAllPermisos()
        ]);
    }

    // Desactivar permiso (activo = 0)
    public function delete($id)
    {
        $eliminado = $this->model->deletePermiso($id);

        if (!$eliminado) {
            die ('No se pudo desactivar el permiso.');
        }
        header("Location: " . URL . "/permiso");
        exit;
    }
}
