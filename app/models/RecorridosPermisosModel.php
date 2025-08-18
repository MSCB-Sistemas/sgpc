<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/*
    Clase para manejar las operaciones sobre la tabla recorridos_permisos en la base de datos.
*/
class RecorridosPermisosModel
{
    // Instancia de conexion la base de datos.
    private PDO $db;

    // Establece la conexion la base de datos.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
        * Funcion para obtener todos los datos de la tabla recorridospermisos en un array.
        * @return array $stmt un array con los datos de la tabla.
        * POST: Devuelve un array con todo el contenido de la tabla recorridospermisos.
     */ 
    public function getAllRecorridosPermisos(): array
    {
        $stmt = $this->db->prepare("SELECT 
            rp.id_recorrido_permiso,
            rp.id_permiso,
            r.nombre AS recorrido
        FROM 
            recorridos_permisos rp
        JOIN 
            recorridos r ON rp.id_recorrido = r.id_recorrido;
        ");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    /**
        * Funcion para obtener los datos de una fila de la tabla recorridos_permisos referenciado por id.
        * PRE: Recibe el id de una fila de la tabla recorrido_permisos.
        * @param int $id_recorrido_permiso es el valor id de la fila que va a ser seleccionada desde la base de datos.
        * @return array $stmt devuelve un array con el contenido de la fila.
        * POST: Devuelve todo el contenido de la fila que se indico por id en un array.
     */ 
    public function getRecorridoPermiso($id_recorrido_permiso): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM recorridos WHERE id_recorrido_permiso = :id_recorrido_permiso");
        $stmt->execute(['id_recorrido_permiso' => $id_recorrido_permiso]);
        return $stmt->fetch();
    }

    /**
        * 
        * PRE: Funcion para modificar un recorrido_permiso ya existente en la tabla de la base de datos.
        * @param int $id_recorrido_permiso es el id de la fila en la tabla recorridos_permisos.
        * @param int $id_permiso es el id del permiso asignado que va a ser modificado.
        * @param int $id_recorrido es el id del recorrido asignado que va a ser modificado.
        * @return bool $stmt es un valor bool que indica el estado de ejecucion.
        * POST: Devuelve el estado de ejecucion con un true en caso de ser exitoso, o false en caso de lo contrario.
     */ 
    public function updateRecorrido($id_recorrido_permiso, $id_permiso, $id_recorrido): bool
    {
        $stmt = $this->db->prepare("UPDATE recorridos SET id_recorrido = :id_recorrido, id_permiso =
        :id_permiso WHERE id_recorrido_permiso = :id_recorrido_permiso");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_recorrido_permiso' => $id_recorrido_permiso,'id_permiso' => $id_permiso, 
        'id_recorrido' => $id_recorrido]);
        // Verifica si la actualización fue exitosa (si se afectaron filas)
        return $stmt->rowCount() > 0;
    }
 
    /**
         * Inserta un nuevo registro en la tabla `recorridos_permisos`.
         * PRE: Deben existir los IDs de permiso y recorrido en sus respectivas tablas.
         * @param int $id_permiso     ID del permiso nuevo asignado.
         * @param int $id_recorrido   ID del recorrido nuevo asignado.
         * @return string ID autogenerado del nuevo registro insertado (como cadena).
         * POST: Se crea un nuevo vínculo entre un permiso y un recorrido en la base de datos.
     */
    public function insertRecorrido($id_permiso, $id_recorrido)
    {
        $stmt = $this->db->prepare("INSERT INTO recorridos_permisos (id_permiso,id_recorrido) VALUES (:id_permiso,:id_recorrido)");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_permiso' => $id_permiso,'id_recorrido' => $id_recorrido]);
        return $this->db->lastInsertId();
    }

    /**
         * Elimina un registro de la tabla `recorridos_permisos` por su ID.
         * PRE: Debe existir un registro con el ID de recorrido_permiso especificado.
         * @param int $id_recorrido_permiso   ID del vínculo permiso-recorrido a eliminar.
         * @return bool True si se eliminó al menos un registro, false en caso contrario.
         * POST: El vínculo correspondiente se elimina de la base de datos si existe.
     */
    public function deleteRecorrido($id_recorrido_permiso): bool
    {
        $stmt = $this->db->prepare("DELETE from recorridos_permisos WHERE id_recorrido_permiso = :id_recorrido_permiso");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_recorrido_permiso' => $id_recorrido_permiso]);
        return $stmt->rowCount() > 0;
    }

    public function getPermisosByRecorrido($id_recorrido): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM recorridos_permisos WHERE id_recorrido = :id_recorrido");
        $stmt->execute(['id_recorrido' => $id_recorrido]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

  