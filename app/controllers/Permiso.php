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
    public function index($fecha_desde = null, $fecha_hasta = null)
    {
    // Si no hay parámetros → cargar última semana
        if (!$fecha_desde && !$fecha_hasta) {
            $fecha_hasta = date('Y-m-d');
            $fecha_desde = date('Y-m-d', strtotime('-1 week'));
        }

        if ($fecha_desde === '0') {
            $fecha_desde = null; // Caso "0" para omitir fecha desde
        }

        if ($_SESSION['usuario_tipo'] == '1') {
            $permisos = $this->model->getAllPermisos(false, $fecha_desde, $fecha_hasta);
        } else {
            $permisos = $this->model->getAllPermisos(true, $fecha_desde, $fecha_hasta);
        }
        foreach ($permisos as &$permiso) {
            $calles_recorrido = $this->load_model('CalleRecorridoModel')->getCallesByRecorrido($permiso['id_recorrido']);
            $nombres_calles = $calles_recorrido ? array_column($calles_recorrido, 'nombre') : [];
            $permiso['Recorrido'] = $nombres_calles ? implode(', ', $nombres_calles) : 'Sin calles';
            $paradas = $this->load_model('ReservasPuntosModel')->getReservasByPedidoPdf($permiso['Permiso Nro.']);
            $paradasArray = [];
            foreach ($paradas as $parada) {
                $paradaString = $parada['horario'].': '.$parada['calle'].' - '.$parada['parada'];
                if (!empty($parada['hotel'])){
                    $paradaString .= ' (Hotel: '.$parada['hotel'].')';
                }
                $paradasArray[] = $paradaString;
            }
            $permiso['Paradas'] = $paradasArray ? implode('<br>', $paradasArray) : 'Sin paradas';
        }

        unset($permiso);
        $datos = [
            'title' => 'Listado de Permisos',
            'urlCrear' => null, // Cambiado a null para no mostrar botón de crear.
            'columnas' => [
                'Nro. Permiso',
                'Tipo',
                'Fecha Reserva',
                'Fecha Emisión',
                'Chofer',
                'Dominio',
                'Empresa'
            ],
            'columnas_claves' => [
                'Permiso Nro.',
                'Tipo',
                'Fecha reserva',
                'Fecha emision',
                'Chofer',
                'Dominio',
                'Empresa'
            ],
            'data' => $permisos,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'acciones' => function($fila) {
                $id = $fila['Permiso Nro.'];
                $url = URL . '/permiso';
                $botones = '';
                if ($fila['activo']==1){
                    if ($_SESSION['usuario_tipo'] == '1'){
                        $botones .= '<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Desactivar este permiso?\');">Eliminar</a> ';
                    }
                    $botones .= '<a href="'.$url.'/imprimir/'.$id.'" class="btn btn-sm btn-outline-primary" onclick="return confirm(\'¿Imprimir este permiso?\');" target="_blank">Imprimir</a> ';
                }
                            
                $botones .= '<a class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalPermiso" data-permiso="'.$id.'">Ver datos</a>';
                return $botones;                            
            }
        ];

        $this->load_view('partials/tablaAbmPermiso', $datos);
    }

   

    // Mostrar formulario para crear permiso
    public function nuevo()
    {
        $errores = [];
        $mensajes = [];
        $imprimir = [];
        if (!empty( $_SESSION['errores'])){
            $errores = [$_SESSION['errores']];
        }
        if (!empty( $_SESSION['mensajes'])){
            $mensajes = $_SESSION['mensajes'];
        }
        if (!empty( $_SESSION['imprimir_permiso'])){
            $imprimir = $_SESSION['imprimir_permiso'];
        }
        
        unset($_SESSION['errores'], $_SESSION['mensajes'], $_SESSION['imprimir_permiso']);

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
            'mensajes' => $mensajes,
            'imprimir' => $imprimir
        ]);
    }
    

    // Procesar creación
    public function store()
    {
        $id_chofer = $_POST['id_chofer'];
        $id_usuario = $_SESSION['usuario_id'];
        $id_servicio = $_POST['id_servicio'];
        $id_lugar = $_POST['id_lugar'];
        $tipo = $_POST['tipo_permiso'];
        $fecha_reserva = $_POST['fecha_reserva'];
        $fecha_emision = date('Y-m-d H:i:s');
        $arribo_salida = $_POST['arribo_salida'];
        $id_recorrido = $_POST['id_recorrido'];
        $observacion = $_POST['observacion'];
        $puntos_detencion = $_POST['puntos_detencion'];
        $puntos_detencion = json_decode($puntos_detencion, true);
        $pasajeros = $_POST['pasajeros'];
        $errores = [];

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

                if (isset($punto['hotel'])) {
                    $hotel = $punto['hotel'];
                } else {
                    $hotel = null;
                }

                $id_reserva = $modelReservasPuntos->insertReservaPunto(
                   $fecha_horario,
                   $hotel,
                   $idPermiso,
                   $id_punto_detencion
                );
                if(!$id_reserva) {
                    $errores[] = "Error al crear la reserva para el punto de detención {$id_punto_detencion} en el horario {$punto['horario']}.";
                }
            }
        }
        if (empty($errores)) {
            if (!empty($_POST['imprimir'])) {
                // Generar el PDF
                $_SESSION['imprimir_permiso'] = $idPermiso;
            }
        }

        // Después de procesar el guardado
        if (empty($errores)) {
            $_SESSION['mensajes'] = ["Permiso {$idPermiso} creado correctamente."];
        } else {
            $_SESSION['errores'] = $errores;
        }

        // Redirigir al formulario
        header('Location: /sgpc/permiso/nuevo');
        exit;
    }

    public function imprimir($idPermiso) {
        $permiso = $this->model->getPermisoPdf($idPermiso);
        $calles_recorrido = $this->load_model('CalleRecorridoModel')->getCallesByRecorrido($permiso['id_recorrido']);
        $nombres_calles = $calles_recorrido ? array_column($calles_recorrido, 'nombre') : [];
        $paradas = $this->load_model('ReservasPuntosModel')->getReservasByPedidoPdf($idPermiso);
        $datos = [
            'id_permiso' => $idPermiso,
            'tipo' => $permiso['tipo'],
            'arribo_salida' => $permiso['arribo_salida'],
            'fecha_reserva' => $permiso['fecha_reserva'],
            'empresa' => $permiso['empresa'],
            'dominio' => $permiso['dominio'],
            'interno' => $permiso['interno'],
            'pasajeros' => $permiso['pasajeros'],
            'observacion' => $permiso['observacion'],
            'calles_recorrido' => $nombres_calles ? implode(', ', $nombres_calles) : 'Sin calles',
            'paradas' => $paradas,
            'nombre_chofer' => $permiso['nombre_chofer'],
            'apellido_chofer' => $permiso['apellido_chofer'],
            'dni_chofer' => $permiso['dni_chofer'],
            'usuario_nombre' => $permiso['usuario_nombre'],
            'usuario_apellido' => $permiso['usuario_apellido'],
            'usuario_cargo' => $permiso['usuario_cargo'],
            'usuario_sector' => $permiso['usuario_sector']
        ];

        // Cargar plantilla
        ob_start();
        include APP . '/views/pages/partials/permisoPdf.php';
        include APP . '/views/pages/partials/permisoPdf.php';
        $html = ob_get_clean();

        // Cargar css
        $cssPath = APP . '/../public/css/permisoPdf.css';
        $css = file_get_contents($cssPath);

        // Generar PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
        ]);

        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        $mpdf->Output("permiso_$idPermiso.pdf", \Mpdf\Output\Destination::INLINE);
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
