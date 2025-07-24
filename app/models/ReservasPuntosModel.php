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
        $stmt = $this->db->prepare("SELECT * FROM reservas_puntos");
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
}

?>