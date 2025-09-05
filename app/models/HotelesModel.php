<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/logHelper.php';
require_once __DIR__ . '/../helpers/auditoriaHelper.php';
require_once 'Database.php';

/**
 * Class HotelesModel
 *
 * Modelo para gestionar operaciones CRUD sobre la tabla 'hoteles'.
 */
class HotelesModel {
    /**
     * Instancia de la conexión PDO a la base de datos.
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase HotelesModel.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los hoteles de la base de datos.
     *
     * @return array Arreglo asociativo con todos los hoteles.
     */
    public function getAllHoteles(): array {
        $stmt = $this->db->prepare("SELECT * FROM hoteles where activo = 1");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la información de un hotel específico por su ID.
     *
     * @param int $id_hotel ID del hotel a consultar.
     * @return array|false Arreglo asociativo con los datos del hotel o false si no existe.
     */
    public function getHotel($id_hotel) : array {
        $stmt = $this->db->prepare("SELECT * FROM hoteles WHERE id_hotel = :id_hotel");
        $stmt->execute(['id_hotel' => $id_hotel]);
        return $stmt->fetch();
    }

    /**
     * Actualiza los datos de un hotel existente.
     *
     * @param int $id_hotel ID del hotel a actualizar.
     * @param string $nombre_hotel Nuevo nombre del hotel.
     * @param string $direccion Nueva dirección del hotel.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updateHotel($id_hotel, $nombre_hotel, $direccion) : bool {

        $query = "UPDATE hoteles SET nombre = :nombre, direccion = :direccion WHERE id_hotel = :id_hotel";
        $stmt = $this->db->prepare($query);

        $params = ['id_hotel' => $id_hotel, 'nombre' => $nombre_hotel, 'direccion' => $direccion];

        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        
        if($stmt->execute($params)){
            return true;
        }else{
            writeLog("❌ Error: No se pudo actualizar el hotel con id ".$id_hotel." en la base de datos. Query: ".$query."parametros: ".json_encode($params));

            return false;
        }
    }

    /**
     * Inserta un nuevo hotel en la base de datos.
     *
     * @param string $nombre_hotel Nombre del hotel.
     * @param string $direccion Dirección del hotel.
     * @return int|string ID del hotel insertado.
     */
    public function insertHotel($nombre_hotel, $direccion) {

        $query = "INSERT INTO hoteles (nombre, direccion) VALUES (:nombre, :direccion)";
        $stmt = $this->db->prepare($query);

        $params = ['nombre' => $nombre_hotel, 'direccion' => $direccion];
        $stmt->execute($params);

        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        $stmt->execute($params);
        return $this->db->lastInsertId();
    }

    /**
     * Elimina un hotel de la base de datos por su ID.
     *
     * @param int $id_hotel ID del hotel a eliminar.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteHotel($id_hotel) : bool {

        $query = "DELETE FROM hoteles WHERE id_hotel = :id_hotel";
        $stmt = $this->db->prepare($query);

        $params = ['id_hotel' => $id_hotel];
        $stmt->execute($params);

        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        if ($stmt->rowCount() === 0) {
            writeLog("❌ Error: No se pudo eliminar el hotel con id ".$id_hotel." en la base de datos. Query: ".$query."parametros: ".json_encode($params));
        }

        return $stmt->rowCount() > 0;
    }

    public function getReservasByHotel($id_hotel): array
    {
        $stmt = $this->db->prepare("SELECT * FROM reservas_puntos WHERE id_hotel = :id_hotel");
        $stmt->execute(['id_hotel' => $id_hotel]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desactivarHotel($id_hotel): bool
    {
        $query = "UPDATE hoteles SET activo = 0 WHERE id_hotel = :id_hotel";
        $stmt = $this->db->prepare($query);

        $params = ['id_hotel' => $id_hotel];

        auditoriaHelper::Log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        if($stmt->execute($params)){
            return true;
        }else{
            writeLog("❌ Error: No se pudo desactivar el hotel con id ".$id_hotel." en la base de datos. Query: ".$query."parametros: ".json_encode($params));

            return false;
        }
    }
}

?>