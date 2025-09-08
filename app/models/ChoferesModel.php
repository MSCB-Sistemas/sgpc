<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auditoriaHelper.php';
require_once __DIR__ . '/../helpers/logHelper.php';
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
        $stmt = $this->db->prepare("SELECT c.*, n.nacionalidad FROM choferes c inner join nacionalidades n on c.id_nacionalidad = n.id_nacionalidad");
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
        $query = "UPDATE choferes SET dni = :dni, nombre = :nombre, apellido = :apellido, id_nacionalidad = :nacionalidad WHERE id_chofer = :id_chofer";
        $stmt = $this->db->prepare($query);

        $params = ['id_chofer' => $id_chofer, 'dni' => $dni, 'nombre' => $nombre, 'apellido' => $apellido, 'nacionalidad' => $nacionalidad];

        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        if ($stmt->execute($params)) {
            return true;
        } else {
            writeLog("❌ Error: No se pudo actualizar el chofer con id " . $id_chofer . " en la base de datos. Query: " . $query . "parametros: " . json_encode($params));
            return false;
        }
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
        $query = "INSERT INTO choferes (dni, nombre, apellido, id_nacionalidad) VALUES (:dni, :nombre, :apellido, :nacionalidad)";
        $stmt = $this->db->prepare($query);

        $params = ['dni' => $dni, 'nombre' => $nombre, 'apellido' => $apellido, 'nacionalidad' => $nacionalidad];
        $stmt->execute($params);
        $result = $this->db->lastInsertId();
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        if (!$result) {
            writeLog("❌ Error: No se pudo insertar el chofer " . $nombre . " " . $apellido . " en la base de datos. Query: " . $query . "parametros: " . json_encode($params));
        }
        return $result;
    }

    /**
     * Elimina un chofer de la base de datos por su ID.
     *
     * @param int $id_chofer ID del chofer a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteChofer($id_chofer) : bool {
        $query = "DELETE FROM choferes WHERE id_chofer = :id_chofer";
        $stmt = $this->db->prepare($query);

        $params = ['id_chofer' => $id_chofer];
        $stmt->execute($params);

        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        if ($stmt->rowCount() === 0) {
            writeLog("❌ Error: No se pudo eliminar el chofer con id " . $id_chofer . " en la base de datos. Query: " . $query . "parametros: " . json_encode($params));
        }

        return $stmt->rowCount() > 0;
    }
}

?>