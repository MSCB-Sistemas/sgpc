<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Class PuntosDetencionModel
 *
 * Modelo para gestionar operaciones CRUD sobre la tabla 'puntos_detencion'.
 */
class PuntosDetencionModel {
    /**
     * Instancia de la conexión PDO a la base de datos.
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase PuntosDetencionModel.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los puntos de detención de la base de datos.
     *
     * @return array Arreglo asociativo con todos los puntos de detención.
     */
    public function getAllPuntosDetencion(): array {
        $stmt = $this->db->prepare("SELECT 
            pd.id_punto_detencion,
            pd.nombre AS nombre_punto,
            c.nombre AS nombre_calle
        FROM 
            puntos_detencion pd
        JOIN 
            calles c ON pd.id_calle = c.id_calle
        where pd.activo = 1;");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la información de un punto de detención específico por su ID.
     *
     * @param int $id_punto_detencion ID del punto de detención a consultar.
     * @return array|false Arreglo asociativo con los datos del punto de detención o false si no existe.
     */
    public function getPuntoDetencion($id_punto_detencion) : array {
        $stmt = $this->db->prepare("SELECT * FROM puntos_detencion WHERE id_punto_detencion = :id_punto_detencion");
        $stmt->execute(['id_punto_detencion' => $id_punto_detencion]);
        return $stmt->fetch();
    }

    /**
     * Actualiza los datos de un punto de detención existente.
     *
     * @param int $id_punto_detencion ID del punto de detención a actualizar.
     * @param string $nombre Nuevo nombre del punto de detención.
     * @param int $id_calle Nueva calle asociada al punto de detención.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updatePuntoDetencion($id_punto_detencion, $nombre, $id_calle) : bool {
        $stmt = $this->db->prepare("UPDATE puntos_detencion SET nombre = :nombre, id_calle = :id_calle WHERE id_punto_detencion = :id_punto_detencion");
        
        return $stmt->execute(['id_punto_detencion' => $id_punto_detencion, 'nombre' => $nombre, 'id_calle' => $id_calle]);
    }

    /**
     * Inserta un nuevo punto de detención en la base de datos.
     *
     * @param string $nombre Nombre del punto de detención.
     * @param int $id_calle ID de la calle asociada.
     * @return int|string ID del punto de detención insertado.
     */
    public function insertPuntoDetencion($nombre, $id_calle) {
        $stmt = $this->db->prepare("INSERT INTO puntos_detencion (nombre, id_calle) VALUES (:nombre, :id_calle)");
        $stmt->execute(['nombre' => $nombre, 'id_calle' => $id_calle]);
        return $this->db->lastInsertId();
    }

    /**
     * Elimina un punto de detención de la base de datos por su ID.
     *
     * @param int $id_punto_detencion ID del punto de detención a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deletePuntoDetencion($id_punto_detencion) : bool {
        $stmt = $this->db->prepare("DELETE FROM puntos_detencion WHERE id_punto_detencion = :id_punto_detencion");
        $stmt->execute(['id_punto_detencion' => $id_punto_detencion]);
        return $stmt->rowCount() > 0;
    }

    public function getPuntosByCalle($id_calle): array
    {
        $stmt = $this->db->prepare("SELECT * FROM puntos_detencion WHERE id_calle = :id_calle order by nombre asc");
        $stmt->execute(['id_calle' => $id_calle]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservaByPunto($id_punto_detencion): array
    {
        $stmt = $this->db->prepare("SELECT * FROM reservas_puntos WHERE id_punto_detencion = :id_punto_detencion");
        $stmt->execute(['id_punto_detencion' => $id_punto_detencion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desactivarPuntoDetencion($id_punto_detencion): bool
    {
        $stmt = $this->db->prepare("UPDATE puntos_detencion SET activo = 0 WHERE id_punto_detencion = :id_punto_detencion");
        // Ejecuta la consulta pasando los valores
        return $stmt->execute(['id_punto_detencion' => $id_punto_detencion]);
    }
}

?>