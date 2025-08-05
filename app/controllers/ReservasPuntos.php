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
        $fecha_horario = trim($_POST['fecha_horario'] ?? '');
        $id_hotel = $_POST['id_hotel'] ?? '';
        $id_permiso = $_POST['id_permiso'] ?? '';
        $id_punto_detencion = $_POST['id_punto_detencion'] ?? '';

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
        $fecha_horario = trim($_POST['fecha_horario'] ?? '');
        $id_hotel = $_POST['id_hotel'] ?? '';
        $id_permiso = $_POST['id_permiso'] ?? '';
        $id_punto_detencion = $_POST['id_punto_detencion'] ?? '';

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


    public function getCantidadPorTipo()
{
    $stmt = $this->db->query("
        SELECT tipo_permiso, COUNT(*) as cantidad
        FROM permisos
        GROUP BY tipo_permiso
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    /**
     * Obtiene los puntos más usados en las reservas.
     * @return array
     */
    public function getPuntosMasUsados()
    {
        $stmt = $this->db->query("
            SELECT pd.nombre_punto, COUNT(*) as cantidad
            FROM reservas_puntos rp
            JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion
            GROUP BY pd.nombre_punto
            ORDER BY cantidad DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene las empresas más frecuentes en las reservas.
     * @return array
     */
    public function getEmpresasMasFrecuentes()
    {
        $stmt = $this->db->query("
            SELECT e.nombre_empresa, COUNT(*) as cantidad
            FROM permisos p
            JOIN empresas e ON p.id_empresa = e.id_empresa
            GROUP BY e.nombre_empresa
            ORDER BY cantidad DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el promedio de ingresos diarios por reservas.
     * @return array
     */
    public function getPromedioIngresos()
    {
        $stmt = $this->db->query("
            SELECT 
                ROUND(COUNT(*) / COUNT(DISTINCT DATE(fecha_horario)), 2) as promedio_diario
            FROM reservas_puntos
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
