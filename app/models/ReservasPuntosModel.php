<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ .'/../helpers/auditoriaHelper.php';
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
        $query = "UPDATE reservas_puntos SET fecha_horario = :fecha_horario, id_hotel = :id_hotel, id_permiso = :id_permiso, id_punto_detencion = :id_punto_detencion 
        WHERE id_reserva_punto = :id_reserva_punto";
        
        $stmt = $this->db->prepare($query);
        $params = ['fecha_horario'=> $fecha_horario, 'id_hotel' => $id_hotel, 'id_permiso' => $id_permiso, 'id_punto_detencion' => $id_punto_detencion];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        
        return $stmt->execute($params);
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
        $query = "INSERT INTO reservas_puntos (fecha_horario, id_hotel, id_permiso, id_punto_detencion) VALUES (:fecha_horario, :id_hotel, :id_permiso, :id_punto_detencion)";
        $stmt = $this->db->prepare($query);
        $params = ['fecha_horario'=> $fecha_horario, 'id_hotel' => $id_hotel, 'id_permiso' => $id_permiso, 'id_punto_detencion' => $id_punto_detencion];
        $stmt->execute($params);
        $result = $this->db->lastInsertId();
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        return $result;
    }

    /**
     * Elimina una reserva de punto de la base de datos por su ID.
     *
     * @param int $id_reserva_punto ID de la reserva de punto a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteReservaPunto($id_reserva_punto) : bool {
        $query = "DELETE FROM reservas_puntos WHERE id_reserva_punto = :id_reserva_punto";
        $stmt = $this->db->prepare($query);
        $params = ['id_reserva_punto' => $id_reserva_punto];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function getReservasByPedidoPdf($id_permiso): array {
        $stmt = $this->db->prepare("SELECT 
            c.nombre as calle,
            pd.nombre as parada,
            h.nombre as hotel,
            TIME(rp.fecha_horario) as horario
            from reservas_puntos rp
            inner join puntos_detencion pd on rp.id_punto_detencion = pd.id_punto_detencion 
            inner join calles c on pd.id_calle = c.id_calle 
            left outer join hoteles h ON rp.id_hotel = h.id_hotel
            where rp.id_permiso = :id_permiso
            order by 4 asc;"
        );
        $stmt->execute(['id_permiso' => $id_permiso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHorariosPunto($id_punto, $fecha): array {
        $stmt = $this->db->prepare("
            SELECT TIME(fecha_horario) as hora
            FROM reservas_puntos
            WHERE id_punto_detencion = :id_punto
            AND DATE(fecha_horario) = :fecha;"
        );
        $stmt->execute([
            'id_punto' => $id_punto,
            'fecha' => $fecha
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservasPuntosByHotel($id_hotel): array {
        $stmt = $this->db->prepare("
            SELECT TIME(fecha_horario) as hora
            FROM reservas_puntos
            WHERE id_hotel = :id_hotel"
        );
        $stmt->execute([
            'id_hotel' => $id_hotel,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>