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
        if ($this->tienePermiso("ver abm")){
            if (!$fecha_desde && !$fecha_hasta) {
                $fecha_hasta = date('Y-m-d');
                $fecha_desde = date('Y-m-d', strtotime('-1 week'));
            }

            if ($fecha_desde === '0') {
                $fecha_desde = null; // Caso "0" para omitir fecha desde
            }

            if ($this->tienePermiso("eliminar permiso")) {
                $permisos = $this->model->getAllPermisos(false, $fecha_desde, $fecha_hasta);
            } else {
                $permisos = $this->model->getAllPermisos(true, $fecha_desde, $fecha_hasta);
            }
            foreach ($permisos as &$permiso) {
                $calles_permiso = $this->load_model('PermisosCallesModel')->getCallesByPermiso($permiso['Permiso Nro.']);
                $nombres_calles = $calles_permiso ? array_column($calles_permiso, 'nombre') : [];
                $permiso['Calles'] = $nombres_calles ? implode(', ', $nombres_calles) : 'Sin calles';
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
                        if ($this->tienePermiso("eliminar permiso")){
                            $botones .= '<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Desactivar este permiso?\');">Eliminar</a> ';
                        }
                        if ($this->tienePermiso("cargar permiso")){
                            $botones .= '<a href="'.$url.'/imprimir/'.$id.'" class="btn btn-sm btn-primary" onclick="return confirm(\'¿Imprimir este permiso?\');" target="_blank">Imprimir</a> ';
                        }
                    }
                                
                    $botones .= '<a class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalPermiso" data-permiso="'.$id.'">Ver datos</a>';
                    return $botones;                            
                }
            ];

            $this->load_view('partials/tablaAbmPermiso', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

   

    // Mostrar formulario para crear permiso
    public function nuevo()
    {
        if ($this->tienePermiso("cargar permiso")) {
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
        } else {
            header("Location: " . URL);
            exit;
        }
    }
    

    // Procesar creación
    public function store()
    {
        if ($this->tienePermiso("cargar permiso")) {
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
            $calles = $_POST['calles_permiso'];
            $calles = json_decode($calles, true);
            $pasajeros = $_POST['pasajeros'];
            $cta_cte = $_POST['chk_cta_cte'];
            $errores = [];

            if (!isset($cta_cte)) {
                $cta_cte = 0; // No está seleccionado
            }

            $modelRecorridosPermisos = $this->load_model('RecorridosPermisosModel');
            $modelPermisosCalles = $this->load_model('PermisosCallesModel');
            $modelReservasPuntos = $this->load_model('ReservasPuntosModel');

            if (!$id_chofer || !$id_usuario || !$id_servicio || $tipo === '' || $fecha_reserva === '' || $arribo_salida === '' || !$id_lugar) {
                
                var_dump($id_chofer, $id_usuario, $id_servicio, $tipo, $fecha_reserva, $fecha_emision, $arribo_salida, $observacion, $pasajeros, $id_lugar, $id_recorrido, $puntos_detencion, $calles);
                
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
                $id_lugar,
                $cta_cte
            );

            if (!$idPermiso) {
                $errores[] = 'Error al crear el permiso.';
            }

            $idPermisoRecorrido = $modelRecorridosPermisos->insertRecorrido($idPermiso, $id_recorrido);
            if (!$idPermisoRecorrido) {
                $errores[] = 'Error al asociar el recorrido al permiso.';
            }

            if (!empty($calles)){
                foreach ($calles as $calle){
                    $idPermisoCalle = $modelPermisosCalles->insertPermisosCalles($idPermiso, $calle['id_calle']);
                    if (!$idPermisoCalle) {
                        $errores[] = "Error al asociar la calle {$calle['id_calle']} al permiso.";
                    }
                }
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
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    public function imprimir($idPermiso) {
        var_dump($this->tienePermiso("cargar permiso"));
        exit;
        if ($this->tienePermiso("cargar permiso")) {
            $permiso = $this->model->getPermisoPdf($idPermiso);
            $calles_permiso = $this->load_model('PermisosCallesModel')->getCallesByPermiso($idPermiso);
            $nombres_calles = $calles_permiso ? array_column($calles_permiso, 'nombre') : [];
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
                'usuario_sector' => $permiso['usuario_sector'],
                'recorrido' => $permiso['recorrido']
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
            // salto de página
            $mpdf->AddPage();

            // insertar el mapa en la página nueva
           // $mpdf->Image(APP . '/../public/img/mapa.jpeg', 0, 0, 210, 297, 'jpg', '', true, false);
            

           // $mpdf->Output("permiso_$idPermiso.pdf", \Mpdf\Output\Destination::INLINE);
            header("Location: " . URL);
            exit;
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Desactivar permiso (activo = 0)
    public function delete($id)
    {
        if ($this->tienePermiso("eliminar permiso")) {
            $eliminado = $this->model->deletePermiso($id);

            if (!$eliminado) {
                die ('No se pudo desactivar el permiso.');
            }
            header("Location: " . URL . "/permiso");
        exit;
        } else {
            header("Location: " . URL);
            exit;
        }
    }
}
