<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auditoriaHelper.php';
require_once 'Database.php';

/*    Clase para manejar las operaciones sobre la tabla lugares en la base de datos.
*/
class LugarModel
{
    // Instancia de conexion la base de datos.
    private PDO $db;

    // Establece la conexion la base de datos.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /** 
     * Funcion que ejecuta una query para devolver todo el conjunto de lugares de la base
     * de datos en un array.
     * @return array $stmt un array con todos los valores cargados.
     * POST: Un array con todos los lugares almacenados en la base de datos.
    */
    public function getAllLugares(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM lugares where activo = 1");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    /** 
     * Funcion para obtener un lugar en especifico segun su id.
     * PRE: Recibe el id de lugar que se quiere obtener.
     * @param int $id_lugar es el id del lugar que se requiere buscar en la base de datos. 
     * @return array $stmt un array con los datos de la fila en ese id.
     * POST: Devolvera un array con todos los datos que tenga el lugar con ese
     * id en la base de datos.
    */
    public function getLugar($id_lugar): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM lugares WHERE id_lugar = :id_lugar");
        $stmt->execute(['id_lugar' => $id_lugar]);
        return $stmt->fetch();
    }

    /**
     * Funcion que ejecuta una query para modificar el nombre de un lugar segun el id recibido.
     * PRE: Recibe la id de lugar especificada y el nombre nuevo que sera guardado
     * en esa id.
     * @param int $id_lugar es el id del lugar que se quiere modificar.
     * @param string $nombre_lugar es el nombre nuevo que sera almacenado en la base de datos
     * segun el id.
     * @return bool $stmt un valor de tipo bool.
     * POST: Un valor booleano que devolvera true en caso de ejecucion exitosa o false
     * en caso contrario.
    */
    public function updateLugar($id_lugar, $nombre_lugar): bool
    {
        $query = "UPDATE lugares SET nombre = :nombre WHERE id_lugar = :id_lugar";
        $stmt = $this->db->prepare($query);
        
        $params = ['id_lugar' => $id_lugar,'nombre' => $nombre_lugar];

        auditoriaHelper::Log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        return $stmt->execute($params);
    }
    /** 
     * Funcion que ejecuta una query para insertar un nuevo lugar.
     * PRE: Recibe el nombre del lugar que se quiere insertar.
     * @param string $nombre_lugar es el nombre del lugar que se quiere insertar.
     * @return string $this Devuelve el id del nuevo elemento insertado.
     * POST: Un string con el id del nuevo lugar insertado.
    */
    public function insertLugar($nombre_lugar)
    {
        $query = "INSERT INTO lugares (nombre) VALUES (:nombre)";
        $stmt = $this->db->prepare($query);

        $params = ['nombre' => $nombre_lugar];
        $stmt->execute($params);

        auditoriaHelper::Log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        return $this->db->lastInsertId();
    }

    /** 
     * Funcion que ejecuta una query para eliminar un lugar de la base de datos.
     * PRE: ID del lugar.
     * @param int $id_lugar es el id del lugar.
     * @return bool $stmt es un valor bool verificando la ejecucion.
     * POST: un valor boleano que sera true en caso de ejecucion exitosa o false en caso
     * contrario.
    */
    public function deleteLugar($id_lugar): bool
    {
        $query = "DELETE from lugares WHERE id_lugar = :id_lugar";
        $stmt = $this->db->prepare($query);

        $params = ['id_lugar' => $id_lugar];
        $stmt->execute($params);

        auditoriaHelper::Log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        return $stmt->rowCount() > 0;
    }

    public function getPermisosByLugarId($id_lugar): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM permisos WHERE id_lugar = :id_lugar");
        $stmt->execute(['id_lugar' => $id_lugar]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desactivarLugar($id_lugar): bool
    {
        $query = "UPDATE lugares SET activo = 0 WHERE id_lugar = :id_lugar";
        $stmt = $this->db->prepare($query);

        $params = ['id_lugar' => $id_lugar];

        auditoriaHelper::Log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        return $stmt->execute($params);
    }

    public function getPermisosByLugar($id_lugar): array
    {
        $stmt = $this->db->prepare("SELECT p.* FROM permisos p
                                    WHERE p.id_lugar = :id_lugar");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_lugar' => $id_lugar]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

  