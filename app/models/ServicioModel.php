<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/logHelper.php';
require_once __DIR__ .'/../helpers/auditoriaHelper.php';
require_once 'Database.php';

/**
 * Modelo para manejar las operaciones CRUD sobre la tabla `servicios`.
 *
 * Esta clase proporciona métodos para interactuar con la tabla `servicios` de la base de datos.
 * Permite obtener, insertar, actualizar y eliminar servicios asociados a una empresa.
 */
class ServicioModel
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
     * Obtiene todos los servicios registrados en la base de datos.
     *
     * @return array Arreglo asociativo con todos los registros de servicios.
     */
    public function getAllServicios(): array
    {
        $stmt = $this->db->prepare("SELECT s.*, e.nombre as nombre_empresa 
                                    FROM servicios s 
                                    JOIN empresas e ON s.id_empresa = e.id_empresa");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un servicio específico por su ID.
     *
     * @param int|string $id ID del servicio a consultar.
     * @return array Arreglo asociativo con los datos del servicio.
     */
    public function getServicio($id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM servicios WHERE id_servicio = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getServicioByEmpresa($id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM servicios WHERE id_empresa = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }
    /**
     * Inserta un nuevo servicio en la base de datos.
     *
     * @param int|string $id_empresa ID de la empresa asociada.
     * @param string $interno Nombre o identificador interno del servicio.
     * @param string $dominio Dominio asignado al servicio.
     * @return bool|string ID del nuevo registro insertado, o false en caso de error.
     */
    public function insertServicio($id_empresa, $interno, $dominio): bool|string
    {
        $query = "INSERT INTO servicios (id_empresa, interno, dominio) VALUES (:id_empresa, :interno, :dominio)";
        $stmt = $this->db->prepare($query);
        $params = [
            'id_empresa' => $id_empresa,
            'interno' => $interno,
            'dominio' => $dominio
        ];
        $stmt->execute($params);
        $result = $this->db->lastInsertId();
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        if (!$result) {
            writeLog("❌ Error: No se pudo insertar el servicio "." en la base de datos. Query: ".$query."parametros: ".$params);
        }

        return $result;
    }

    /**
     * Actualiza un servicio existente en la base de datos.
     *
     * @param int|string $id ID del servicio a actualizar.
     * @param int|string $id_empresa ID de la empresa asociada.
     * @param string $interno Nombre o identificador interno del servicio.
     * @param string $dominio Dominio actualizado del servicio.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updateServicio($id, $id_empresa, $interno, $dominio): bool|string
    {
        $query = $this->db->prepare(
            "UPDATE servicios 
             SET id_empresa = :id_empresa, interno = :interno, dominio = :dominio 
             WHERE id_servicio = :id"
        );
        
        $stmt = $this->db->prepare($query);
        $params = [
            'id' => $id,
            'id_empresa' => $id_empresa,
            'interno' => $interno,
            'dominio' => $dominio
        ];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        
        if($stmt->execute($params)){
            return true;
        }else{
            writeLog("❌ Error: No se pudo actualizar el servicio con id ".$id." en la base de datos. Query: ".$query."parametros: ".json_encode($params));

            return false;
        }
    }

    /**
     * Elimina un servicio de la base de datos.
     *
     * @param int|string $id ID del servicio a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteServicio($id): bool
    {
        $query = "DELETE FROM servicios WHERE id_servicio = :id";
        
        $stmt = $this->db->prepare($query);
        $params = ['id' => $id];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) {
            writeLog("❌ Error: No se pudo eliminar el servicio con id ".$id." en la base de datos. Query: ".$query."parametros: ".json_encode($params));
        }

        return $stmt->rowCount() > 0;
    }

    public function getServiciosServerSide($start, $length, $searchValue, $orderColumn, $orderDir)
    {
        $sql = "SELECT s.*, e.nombre as nombre_empresa 
                FROM servicios s 
                JOIN empresas e ON s.id_empresa = e.id_empresa";
        $params = [];
        // Si hay búsqueda
        if (!empty($searchValue)) {
            $sql .= " WHERE e.nombre LIKE :search 
                    OR s.interno LIKE :search 
                    OR s.dominio LIKE :search";
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
    
    public function contarServiciosFiltrados($searchValue)
    {
        $sql = "SELECT COUNT(*) as total FROM servicios s 
                JOIN empresas e ON s.id_empresa = e.id_empresa";
        $params = [];

        if (!empty($searchValue)) {
            $sql .= " WHERE e.nombre LIKE :search 
                    OR s.interno LIKE :search 
                    OR s.dominio LIKE :search";
            $params[':search'] = "%$searchValue%";
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function contarServicios()
    {
        $sql = "SELECT COUNT(*) as total FROM servicios";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
