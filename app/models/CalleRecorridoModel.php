<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Modelo para manejar las operaciones CRUD sobre la tabla `calles_recorridos`.
 *
 * Esta clase permite gestionar la relación entre recorridos y calles, incluyendo la inserción,
 * actualización, consulta y eliminación de asociaciones entre ellos.
 */
class CalleRecorridoModel
{
    /**
     * Instancia de conexión PDO a la base de datos.
     *
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase.
     *
     * Establece la conexión con la base de datos utilizando la clase `Database`.
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todas las asociaciones de calles con recorridos.
     *
     * @return array Arreglo asociativo con todos los registros de `calles_recorridos`.
     */
    public function getAllCallesRecorridos(): array
    {
        $stmt = $this->db->query("SELECT * FROM calles_recorridos");
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una asociación específica entre una calle y un recorrido por su ID.
     *
     * @param int|string $id ID del registro en `calles_recorridos`.
     * @return array Arreglo asociativo con los datos del registro correspondiente.
     */
    public function getCalleRecorrido($id_calle_recorrido): array
    {
        $stmt = $this->db->prepare("SELECT * FROM calles_recorridos WHERE id_calle_recorrido = :id_calle_recorrido");
        $stmt->execute(['id_calle_recorrido' => $id_calle_recorrido]);
        return $stmt->fetch();
    }

    /**
     * Inserta una nueva relación entre un recorrido y una calle.
     *
     * @param int|string $id_recorrido ID del recorrido.
     * @param int|string $id_calle ID de la calle.
     * @return bool|string ID del nuevo registro insertado o false en caso de error.
     */
    public function insertCalleRecorrido($id_recorrido, $id_calle): bool|string
    {
        $stmt = $this->db->prepare(
            "INSERT INTO calles_recorridos (id_recorrido, id_calle) VALUES (:id_recorrido, :id_calle)"
        );
        $stmt->execute([
            'id_recorrido' => $id_recorrido,
            'id_calle' => $id_calle
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza una relación existente entre un recorrido y una calle.
     *
     * @param int|string $id ID del registro a actualizar.
     * @param int|string $id_recorrido Nuevo ID del recorrido.
     * @param int|string $id_calle Nuevo ID de la calle.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updateCalleRecorrido($id, $id_recorrido, $id_calle): bool|string
    {
        $stmt = $this->db->prepare(
            "UPDATE calles_recorridos 
             SET id_recorrido = :id_recorrido, id_calle = :id_calle 
             WHERE id_calle_recorrido = :id"
        );
        return $stmt->execute([
            'id' => $id,
            'id_recorrido' => $id_recorrido,
            'id_calle' => $id_calle
        ]);
    }

    /**
     * Elimina una relación entre una calle y un recorrido.
     *
     * @param int|string $id ID del registro a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteCalleRecorrido($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM calles_recorridos WHERE id_calle_recorrido = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Obtiene las calles asociadas a un recorrido específico.
     *
     * @param  mixed $id_recorrido ID del recorrido.
     * @return bool|array Lista de nombres de calles asociadas al recorrido, o false si no hay calles. 
     */
    public function getCallesByRecorrido($id_recorrido): bool|array
    {
        $stmt = $this->db->prepare(
            "SELECT calles.id_calle, calles.nombre FROM calles 
             JOIN calles_recorridos ON calles.id_calle = calles_recorridos.id_calle 
             WHERE calles_recorridos.id_recorrido = :id_recorrido"
        );
        $stmt->execute(['id_recorrido' => $id_recorrido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecorridosByCalle($id_calle): bool|array
    {
        $stmt = $this->db->prepare(
            "SELECT r.nombre FROM recorridos r
             JOIN calles_recorridos ON r.id_recorrido = calles_recorridos.id_recorrido 
             WHERE calles_recorridos.id_calle = :id_calle"
        );
        $stmt->execute(['id_calle' => $id_calle]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Elimina todas las calles asociadas a un recorrido específico.
     *
     * @param  mixed $id ID del recorrido.
     * @return bool True si se eliminaron las calles, false en caso contrario.
     */
    public function deleteByRecorrido($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM calles_recorridos WHERE id_recorrido = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
