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
        $stmt->execute([
            'id' => $id,
            'id_recorrido' => $id_recorrido,
            'id_calle' => $id_calle
        ]);
        return $stmt->rowCount() > 0;
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
}
