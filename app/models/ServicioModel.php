<?php
require_once __DIR__ . '/../config/config.php';
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
        
        return $stmt->execute($params);
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
        return $stmt->rowCount() > 0;
    }
}
