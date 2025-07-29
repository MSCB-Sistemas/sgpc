<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Class ChoferesModel
 *
 * Modelo para gestionar operaciones CRUD sobre la tabla 'choferes'.
 */
class ChoferesModel {
    /**
     * Instancia de la conexión PDO a la base de datos.
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase ChoferesModel.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los choferes de la base de datos.
     *
     * @return array Arreglo asociativo con todos los choferes.
     */
    public function getAllChoferes(): array {
        $stmt = $this->db->prepare("SELECT * FROM choferes");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la información de un chofer específico por su ID.
     *
     * @param int $id_chofer ID del chofer a consultar.
     * @return array|false Arreglo asociativo con los datos del chofer o false si no existe.
     */
    public function getChofer($id_chofer) : array {
        $stmt = $this->db->prepare("SELECT * FROM choferes WHERE id_chofer = :id_chofer");
        $stmt->execute(['id_chofer' => $id_chofer]);
        return $stmt->fetch();
    }

    /**
     * Actualiza los datos de un chofer existente.
     *
     * @param int $id_chofer ID del chofer a actualizar.
     * @param string $dni Nuevo DNI del chofer.
     * @param string $nombre Nuevo nombre del chofer.
     * @param string $apellido Nuevo apellido del chofer.
     * @param string $nacionalidad Nueva nacionalidad del chofer.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updateChofer($id_chofer, $dni, $nombre, $apellido, $nacionalidad) : bool {
        $stmt = $this->db->prepare("UPDATE choferes SET dni = :dni, nombre = :nombre, apellido = :apellido, nacionalidad = :nacionalidad WHERE id_chofer = :id_chofer");
        $stmt->execute(['id_chofer' => $id_chofer, 'dni' => $dni, 'nombre' => $nombre, 'apellido' => $apellido, 'nacionalidad' => $nacionalidad]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Inserta un nuevo chofer en la base de datos.
     *
     * @param string $dni DNI del chofer.
     * @param string $nombre Nombre del chofer.
     * @param string $apellido Apellido del chofer.
     * @param string $nacionalidad Nacionalidad del chofer.
     * @return int|string ID del chofer insertado.
     */
    public function insertChofer($dni, $nombre, $apellido, $nacionalidad) {
        $stmt = $this->db->prepare("INSERT INTO choferes (dni, nombre, apellido, nacionalidad) VALUES (:dni, :nombre, :apellido, :nacionalidad)");
        $stmt->execute(['dni' => $dni, 'nombre' => $nombre, 'apellido' => $apellido, 'nacionalidad' => $nacionalidad]);
        return $this->db->lastInsertId();
    }

    /**
     * Elimina un chofer de la base de datos por su ID.
     *
     * @param int $id_chofer ID del chofer a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteChofer($id_chofer) : bool {
        $stmt = $this->db->prepare("DELETE FROM choferes WHERE id_chofer = :id_chofer");
        $stmt->execute(['id_chofer' => $id_chofer]);
        return $stmt->rowCount() > 0;
    }
}

?>