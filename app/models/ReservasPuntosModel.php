<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Class ReservasPuntosModel
 *
 * Modelo para gestionar operaciones CRUD sobre la tabla 'reservas_puntos'.
 */
class ReservasPuntosModel {
    /**
     * Instancia de la conexión PDO a la base de datos.
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase ReservasPuntosModel.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todas las reservas de puntos de la base de datos.
     *
     * @return array Arreglo asociativo con todas las reservas de puntos.
     */
    public function getAllReservasPuntos(): array {
        $stmt = $this->db->prepare("SELECT 
            rp.id_reserva_punto,
            rp.fecha_horario,
            h.nombre AS hotel,
            pd.nombre AS punto_detencion,
            rp.id_permiso
        FROM 
            reservas_puntos rp
        JOIN hoteles h ON rp.id_hotel = h.id_hotel
        JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion;
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la información de una reserva de punto específica por su ID.
     *
     * @param int $id_reserva_punto ID de la reserva de punto a consultar.
     * @return array|false Arreglo asociativo con los datos de la reserva o false si no existe.
     */
    public function getReservaPunto($id_reserva_punto) : array {
        $stmt = $this->db->prepare("SELECT * FROM reservas_puntos WHERE id_reserva_punto = :id_reserva_punto");
        $stmt->execute(['id_reserva_punto' => $id_reserva_punto]);
        return $stmt->fetch();
    }

    /**
     * Actualiza los datos de una reserva de punto existente.
     *
     * @param int $id_reserva_punto ID de la reserva de punto a actualizar.
     * @param string $fecha_horario Nueva fecha y horario de la reserva.
     * @param int $id_hotel Nuevo ID del hotel asociado.
     * @param int $id_permiso Nuevo ID del permiso asociado.
     * @param int $id_punto_detencion Nuevo ID del punto de detención asociado.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updateReservaPunto($id_reserva_punto, $fecha_horario, $id_hotel, $id_permiso, $id_punto_detencion) : bool {
        $stmt = $this->db->prepare("UPDATE reservas_puntos SET fecha_horario = :fecha_horario, id_hotel = :id_hotel, id_permiso = :id_permiso, id_punto_detencion = :id_punto_detencion WHERE id_reserva_punto = :id_reserva_punto");
        $stmt->execute(['fecha_horario'=> $fecha_horario, 'id_hotel' => $id_hotel, 'id_permiso' => $id_permiso, 'id_punto_detencion' => $id_punto_detencion]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Inserta una nueva reserva de punto en la base de datos.
     *
     * @param string $fecha_horario Fecha y horario de la reserva.
     * @param int $id_hotel ID del hotel asociado.
     * @param int $id_permiso ID del permiso asociado.
     * @param int $id_punto_detencion ID del punto de detención asociado.
     * @return int|string ID de la reserva de punto insertada.
     */
    public function insertReservaPunto($fecha_horario, $id_hotel, $id_permiso, $id_punto_detencion) {
        $stmt = $this->db->prepare("INSERT INTO reservas_puntos (fecha_horario, id_hotel, id_permiso, id_punto_detencion) VALUES (:fecha_horario, :id_hotel, :id_permiso, :id_punto_detencion)");
        $stmt->execute(['fecha_horario'=> $fecha_horario, 'id_hotel' => $id_hotel, 'id_permiso' => $id_permiso, 'id_punto_detencion' => $id_punto_detencion]);
        return $this->db->lastInsertId();
    }

    /**
     * Elimina una reserva de punto de la base de datos por su ID.
     *
     * @param int $id_reserva_punto ID de la reserva de punto a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteReservaPunto($id_reserva_punto) : bool {
        $stmt = $this->db->prepare("DELETE FROM reservas_puntos WHERE id_reserva_punto = :id_reserva_punto");
        $stmt->execute(['id_reserva_punto' => $id_reserva_punto]);
        return $stmt->rowCount() > 0;
    }

    // Obtener cantidad de permisos por tipo (charter, línea, otros)
    public function getCantidadPorTipo(): array {
        $stmt = $this->db->prepare("
            SELECT tipo, COUNT(*) as cantidad 
            FROM permisos 
            GROUP BY tipo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener los puntos de ingreso más utilizados
    public function getPuntosMasUsados(): array {
        $stmt = $this->db->prepare("
            SELECT pd.nombre AS nombre_punto, COUNT(*) AS cantidad
            FROM reservas_puntos rp
            JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion
            GROUP BY pd.nombre
            ORDER BY cantidad DESC
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener empresas que más permisos generaron
    public function getEmpresasMasFrecuentes(): array {
        $stmt = $this->db->prepare("
            SELECT e.nombre AS nombre_empresa, COUNT(*) AS cantidad
            FROM permisos p
            JOIN servicios s ON p.id_servicio = s.id_servicio
            JOIN empresas e ON s.id_empresa = e.id_empresa
            GROUP BY e.nombre
            ORDER BY cantidad DESC
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener promedio de ingresos por día
    public function getPromedioIngresos(): array {
        $stmt = $this->db->prepare("
            SELECT ROUND(COUNT(*) / COUNT(DISTINCT DATE(fecha_horario)), 2) as promedio_diario
            FROM reservas_puntos
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

        // 1. Hoteles más utilizados
    public function getHotelesMasUsados(): array {
        $stmt = $this->db->prepare("
            SELECT h.nombre AS nombre_hotel, COUNT(*) AS cantidad
            FROM reservas_puntos rp
            JOIN hoteles h ON rp.id_hotel = h.id_hotel
            WHERE rp.id_hotel IS NOT NULL
            GROUP BY h.nombre
            ORDER BY cantidad DESC
            LIMIT 5
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservasDesdeHoy(): array {
    $stmt = $this->db->prepare("
        SELECT 
            pd.nombre AS punto,
            c.nombre AS calle,
            rp.fecha_horario
        FROM reservas_puntos rp
        JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion
        JOIN calles c ON pd.id_calle = c.id_calle
        WHERE rp.fecha_horario >= CURRENT_DATE
        ORDER BY rp.fecha_horario ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





    
}

?>