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
        $stmt = $this->db->prepare("SELECT c.*, n.nacionalidad FROM choferes c inner join nacionalidades n on c.id_nacionalidad = n.id_nacionalidad order by c.apellido, c.nombre");
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
   public function insertChofer($dni, $nombre, $apellido, $nacionalidad)
{
    try {
        $query = "INSERT INTO choferes (dni, nombre, apellido, id_nacionalidad) 
                  VALUES (:dni, :nombre, :apellido, :nacionalidad)";
        $stmt = $this->db->prepare($query);

        $params = [
            'dni' => $dni,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'nacionalidad' => $nacionalidad
        ];

        $stmt->execute($params);
        $result = $this->db->lastInsertId();

        // Auditoría
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );


        // Return the inserted ID (string as returned by lastInsertId)
        return $result;

    } catch (PDOException $e) {
        if ($e->getCode() == '23000' && $e->errorInfo[1] == 1062) {
            writeLog("❌ Duplicado: intento de insertar chofer con DNI {$dni} y nacionalidad {$nacionalidad}");
            return 0; // duplicado
        }
        writeLog("❌ Error inesperado: " . $e->getMessage());
        return -1; // error genérico
    }

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

    public function getChoferesServerSide($start, $length, $searchValue, $orderColumn, $orderDir)
    {
        $sql = "SELECT c.*,n.nacionalidad FROM choferes c join nacionalidades n on c.id_nacionalidad = n.id_nacionalidad";
        $params = [];

        // Si hay búsqueda
        if (!empty($searchValue)) {
            $sql .= " WHERE c.nombre LIKE :search 
                    OR c.apellido LIKE :search 
                    OR c.dni LIKE :search 
                    OR n.nacionalidad LIKE :search";
            $params[':search'] = "%$searchValue%";
        }

        // Orden
        $sql .= " ORDER BY $orderColumn $orderDir";

        // Paginación
        $sql .= " LIMIT :start, :length";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':start', (int) $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int) $length, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarChoferesFiltrados($searchValue)
    {
        $sql = "SELECT COUNT(*) as total FROM choferes c inner join nacionalidades n on c.id_nacionalidad = n.id_nacionalidad";
        $params = [];

        if (!empty($searchValue)) {
            $sql .= " WHERE c.nombre LIKE :search 
                    OR c.apellido LIKE :search 
                    OR c.dni LIKE :search 
                    OR n.nacionalidad LIKE :search";
            $params[':search'] = "%$searchValue%";
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function contarChoferes()
    {
        $sql = "SELECT COUNT(*) as total FROM choferes";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}

?>