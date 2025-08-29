<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ .'/../helpers/auditoriaHelper.php';
require_once 'Database.php';

/*
    Clase para manejar las operaciones sobre la tabla recorridos en la base de datos.
*/
class RecorridoModel
{
    // Instancia de conexion la base de datos.
    private PDO $db;

    // Establece la conexion la base de datos.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Funcion para obtener todos los datos de la tabla recorridos de aquellos que estén activos.
     * PRE: La base de datos debe estar disponible y la tabla 'recorridos' debe existir.
     * @return array Arreglo asociativo con todos los registros de la tabla recorridos.
     * POST: Retorna todos los registros existentes en la tabla recorridos.
     */
    public function getAllRecorridos(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM recorridos where activo = 1");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    /**
     * Funcion para obtener un recorrido por el ID.
     * PRE: Debe existir un recorrido con el ID proporcionado.
     * @param int $id_recorrido ID del recorrido a obtener.
     * @return array Arreglo asociativo con los datos del recorrido, o false si no se encuentra.
     * POST: Se obtiene y retorna la información del recorrido correspondiente al ID.
     */
    public function getRecorrido($id_recorrido): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM recorridos WHERE id_recorrido = :id_recorrido");
        $stmt->execute(['id_recorrido' => $id_recorrido]);
        return $stmt->fetch();
    }

    /**
     * Funcion para modificar un recorrido de la tabla recorrdios, actualizando su nombre.
     * PRE: Debe existir un recorrido con el ID especificado.
     * @param int $id_recorrido ID del recorrido a actualizar.
     * @param string $nombre_recorrido Nuevo nombre para el recorrido.
     * @return bool True si se actualizó al menos un registro, False en caso contrario.
     * POST: El nombre del recorrido con el ID dado será actualizado si existe.
     */
    public function updateRecorrido($id_recorrido, $nombre_recorrido): bool
    {
        $query = "UPDATE recorridos SET nombre = :nombre WHERE id_recorrido = :id_recorrido";
        $stmt = $this->db->prepare($query);
        $params = ['id_recorrido' => $id_recorrido,'nombre' => $nombre_recorrido ];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        return $stmt->execute($params);
    }
 
    /**
     * Funcion para almacenar un nuevo recorrido en la tabla recorridos.
     * PRE: El nombre del recorrido debe ser una cadena válida.
     * @param string $nombre_recorrido Nombre del nuevo recorrido a insertar.
     * @return int ID del nuevo recorrido insertado.
     * POST: Se inserta un nuevo recorrido en la base de datos y se retorna su ID.
     */
    public function insertRecorrido($nombre_recorrido)
    {
        $query = "INSERT INTO recorridos (nombre) VALUES (:nombre)";
        $stmt = $this->db->prepare($query);
        $params = ['nombre' => $nombre_recorrido];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        $stmt->execute($params);
        return $this->db->lastInsertId();
    }

    /**
     * Funcion para eliminar un recorrido de tabla recorridos.
     * PRE: Debe existir un recorrido con el ID proporcionado.
     * @param int $id_recorrido ID del recorrido a eliminar.
     * @return bool True si se eliminó al menos un registro, False en caso contrario.
     * POST: El recorrido con el ID especificado será eliminado de la base de datos.
     */
    public function deleteRecorrido($id_recorrido): bool
    {
        $query = "DELETE from recorridos WHERE id_recorrido = :id_recorrido";
        $stmt = $this->db->prepare($query);
        $params = ['id_recorrido' => $id_recorrido];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function desactivarRecorrido($id_recorrido): bool
    {
        $query = "UPDATE recorridos SET activo = 0 WHERE id_recorrido = :id_recorrido";
        $stmt = $this->db->prepare($query);
        $params = ['id_recorrido' => $id_recorrido];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        return $stmt->execute($params);
    }
}