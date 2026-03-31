<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ .'/../helpers/auditoriaHelper.php';
require_once __DIR__ . '/../helpers/logHelper.php';
require_once 'Database.php';

/*
    Clase para manejar las operaciones sobre la tabla recorridos_permisos en la base de datos.
*/
class PermisosCallesModel
{
    // Instancia de conexion la base de datos.
    private PDO $db;

    // Establece la conexion la base de datos.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
        * Funcion para obtener todos los datos de la tabla permisos_calles en un array.
        * @return array $stmt un array con los datos de la tabla.
        * POST: Devuelve un array con todo el contenido de la tabla permisos_calles.
     */ 
    public function getAllPermisosCalles(): array
    {
        $stmt = $this->db->prepare("SELECT 
            pc.id_permiso_calle,
            pc.id_permiso,
            pc.id_calle,
            c.nombre AS calle
        FROM 
            permisos_calles pc
        JOIN 
            calles c ON c.id_calle = pc.id_calle;
        ");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }
    /**
         * Inserta un nuevo registro en la tabla `permisos_calles`.
         * PRE: Deben existir los IDs de permiso y calle en sus respectivas tablas.
         * @param int $id_permiso     ID del permiso nuevo asignado.
         * @param int $id_calle       ID de la calle nueva asignada.
         * @return string ID autogenerado del nuevo registro insertado (como cadena).
         * POST: Se crea un nuevo vínculo entre un permiso y una calle en la base de datos.
     */
    public function insertPermisosCalles($id_permiso, $id_calle)
    {
        $query = "INSERT INTO permisos_calles (id_permiso,id_calle) VALUES (:id_permiso,:id_calle)";
        $stmt = $this->db->prepare($query);
        $params = ['id_permiso' => $id_permiso,'id_calle' => $id_calle];
        $stmt->execute($params);
        $result = $this->db->lastInsertId();
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );

        if (!$result) {
            writeLog("❌ Error: No se pudo insertar la calle en el permiso "." en la base de datos. Query: ".$query."parametros: ".$params);
        }

        return $result;
    }

    public function getPermisosByCalle($id_calle): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM permisos_calles WHERE id_calle = :id_calle");
        $stmt->execute(['id_calle' => $id_calle]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCallesByPermiso($id_permiso): array 
    {
        $stmt = $this->db->prepare("SELECT pc.*, c.nombre
                                            FROM permisos_calles pc
                                            INNER JOIN calles c ON c.id_calle = pc.id_calle
                                            WHERE pc.id_permiso = :id_permiso");
        $stmt->execute(['id_permiso' => $id_permiso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

  