<?php
/**
 * Controlador para manejar las operaciones relacionadas con las Reservas de Puntos.
 */
class ReservasPuntos extends Control
{
    private ReservasPuntosModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('ReservasPuntosModel');
    }

    // Mostrar todas las reservas
    public function index()
    {
        if(in_array('ver abm', $_SESSION['usuario_derechos'])) {
            $reservas = $this->model->getAllReservasPuntos();
            $datos = [
                'title' => 'Listado de Puntos Reservados',
                'urlCrear' => URL . '/reservaspuntos/create',
                'columnas' => ['Fecha', 'Hotel','Punto de Detencion'.'Permiso'],
                'columnas_claves' => ['fecha_horario','hotel','punto_detencion','id_permiso'],
                'data' => $reservas,
                'acciones' => function($fila) {
                    $id = $fila['id_reserva_punto'];
                    $url = URL . '/reservaspuntos';
                    return '
                        <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                        <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar esta reservacion?\');">Eliminar</a>
                    ';
                }
            ];    
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Mostrar una reserva específica
    public function show($id)
    {
        $reserva = $this->model->getReservaPunto($id);

        if (!$reserva) {
            $this->load_view('reservas_puntos/index', [
                'error' => 'Reserva no encontrada.',
                'reservas' => $this->model->getAllReservasPuntos()
            ]);
            return;
        }

        $this->load_view('reservas_puntos/show', ['reserva' => $reserva]);
    }

    // Formulario de creación
    public function create()
    {
        $hoteles = $this->load_model('HotelesModel')->getAllHoteles();
        $permisos = $this->load_model('PermisosModel')->getAllPermisos();
        $puntos = $this->load_model('PuntosDetencionModel')->getAllPuntosDetencion();

        $this->load_view('reservas_puntos/create', [
            'hoteles' => $hoteles,
            'permisos' => $permisos,
            'puntos' => $puntos
        ]);
    }

    // Procesar la creación
    public function store()
    {
        if (isset($_POST['fecha_horario'])) {
            $fecha_horario = trim($_POST['fecha_horario']);
        } else {
            $fecha_horario = '';
        }   

        if(isset($_POST['id_hotel'])) {
            $id_hotel = $_POST['id_hotel'];
        } else {
            $id_hotel = '';
        }
        if(isset($_POST['id_permiso'])) {
            $id_permiso = $_POST['id_permiso'];
        } else {
            $id_permiso = '';
        }
        if(isset($_POST['id_punto_detencion'])) {
            $id_punto_detencion = $_POST['id_punto_detencion'];
        } else {
            $id_punto_detencion = '';
        }

        if ($fecha_horario === '' || $id_hotel === '' || $id_permiso === '' || $id_punto_detencion === '') {
            $this->create(); // reutiliza el mismo formulario
            return;
        }

        $this->model->insertReservaPunto($fecha_horario, $id_hotel, $id_permiso, $id_punto_detencion);

        $this->load_view('reservas_puntos/index', [
            'message' => 'Reserva creada exitosamente.',
            'reservas' => $this->model->getAllReservasPuntos()
        ]);
    }

    // Formulario de edición
    public function edit($id)
    {
        $reserva = $this->model->getReservaPunto($id);
        if (!$reserva) {
            $this->load_view('reservas_puntos/index', [
                'error' => 'Reserva no encontrada.',
                'reservas' => $this->model->getAllReservasPuntos()
            ]);
            return;
        }

        $hoteles = $this->load_model('HotelesModel')->getAllHoteles();
        $permisos = $this->load_model('PermisosModel')->getAllPermisos();
        $puntos = $this->load_model('PuntosDetencionModel')->getAllPuntosDetencion();

        $this->load_view('reservas_puntos/edit', [
            'reserva' => $reserva,
            'hoteles' => $hoteles,
            'permisos' => $permisos,
            'puntos' => $puntos
        ]);
    }

    // Procesar actualización
    public function update($id)
    {
        if(isset($_POST['fecha_horario'])) {
            $fecha_horario = trim($_POST['fecha_horario']);
        } else {
            $fecha_horario = '';
        }

        if(isset($_POST['id_hotel'])) {
            $id_hotel = $_POST['id_hotel'];
        } else {
            $id_hotel = '';
        }

        if(isset($_POST['id_permiso'])) {
            $id_permiso = $_POST['id_permiso'];
        } else {
            $id_permiso = '';
        }
     
        if(isset($_POST['id_punto_detencion'])) {
            $id_punto_detencion = $_POST['id_punto_detencion'];
        } else {
            $id_punto_detencion = '';
        }

        if ($fecha_horario === '' || $id_hotel === '' || $id_permiso === '' || $id_punto_detencion === '') {
            $this->edit($id); // reutiliza formulario con datos
            $this->load_view('reservas_puntos/edit', [
                'error' => 'Todos los campos son obligatorios.',
                'reserva' => $this->model->getReservaPunto($id),
                'hoteles' => $this->load_model('HotelesModel')->getAllHoteles(),
                'permisos' => $this->load_model('PermisosModel')->getAllPermisos(),
                'puntos' => $this->load_model('PuntosDetencionModel')->getAllPuntosDetencion()
            ]);
            return;
        }

        $this->model->updateReservaPunto($id, $fecha_horario, $id_hotel, $id_permiso, $id_punto_detencion);

        $this->load_view('reservas_puntos/index', [
            'message' => 'Reserva actualizada correctamente.',
            'reservas' => $this->model->getAllReservasPuntos()
        ]);
    }

    // Eliminar una reserva
    public function delete($id)
    {
        $eliminado = $this->model->deleteReservaPunto($id);
        $reservas = $this->model->getAllReservasPuntos();

        if (!$eliminado) {
            $this->load_view('reservas_puntos/index', [
                'error' => 'No se pudo eliminar la reserva.',
                'reservas' => $reservas
            ]);
            return;
        }

        $this->load_view('reservas_puntos/index', [
            'message' => 'Reserva eliminada correctamente.',
            'reservas' => $reservas
        ]);
    }

    public function horariosDisponibles($id_punto, $fecha)
    {
        // Generar todos los horarios fijos (06:00 a 23:30)
        $horarios = [];
        $horaInicio = strtotime('06:00');
        $horaFin = strtotime('23:30');

        for ($t = $horaInicio; $t <= $horaFin; $t += 1800) { // 1800 seg = 30 min
            $horarios[] = date('H:i', $t);
        }

        // Consultar horarios ocupados
        $ocupados = $this->model->getHorariosPunto($id_punto, $fecha);
        $ocupadosFormateados = array_map(function($r) {
            return date('H:i', strtotime($r['hora']));
        }, $ocupados);

        // Filtrar horarios libres
        $libres = array_values(array_diff($horarios, $ocupadosFormateados));

        header('Content-Type: application/json');
        echo json_encode($libres);
        exit;
    }


}
