<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Modelo para manejar las operaciones CRUD sobre la tabla `nacionalidades`.
 *
 * Esta clase se encarga de acceder a la base de datos para obtener, insertar,
 * actualizar y eliminar registros de la tabla `nacionalidades`.
 */
class NacionalidadModel
{
    /**
     * Instancia de la conexión PDO a la base de datos.
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
     * Obtiene todas las nacionalidades registradas en la base de datos.
     *
     * @return array Arreglo asociativo con todas las nacionalidades.
     */
    public function getAllNacionalidades(): array
    {
        $stmt = $this->db->query("SELECT * FROM nacionalidades");
        return $stmt->fetchAll();
    }

    /**
     * Obtiene una nacionalidad específica por su ID.
     *
     * @param int|string $id ID de la nacionalidad a consultar.
     * @return array Arreglo asociativo con los datos de la nacionalidad.
     */
    public function getNacionalidad($id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM nacionalidades WHERE id_nacionalidad = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Inserta una nueva nacionalidad en la base de datos.
     *
     * @param string $nacionalidad Nombre de la nacionalidad a insertar.
     * @return bool|string ID del nuevo registro insertado, o false en caso de error.
     */
    public function insertNacionalidad($nacionalidad): bool|string
    {
        $stmt = $this->db->prepare("INSERT INTO nacionalidades (nacionalidad) VALUES (:nacionalidad)");
        $stmt->execute(['nacionalidad' => $nacionalidad]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza el nombre de una nacionalidad existente.
     *
     * @param int|string $id ID de la nacionalidad a actualizar.
     * @param string $nuevaNacionalidad Nuevo nombre de la nacionalidad.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updateNacionalidad($id, $nuevaNacionalidad): bool
    {
        $stmt = $this->db->prepare("UPDATE nacionalidades SET nacionalidad = :nuevaNacionalidad WHERE id_nacionalidad = :id");
        
        return $stmt->execute([
            'id' => $id,
            'nuevaNacionalidad' => $nuevaNacionalidad
        ]);
    }

    /**
     * Elimina una nacionalidad de la base de datos.
     *
     * @param int|string $id ID de la nacionalidad a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteNacionalidad($id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM nacionalidades WHERE id_nacionalidad = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
