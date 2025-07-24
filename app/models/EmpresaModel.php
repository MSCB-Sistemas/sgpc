<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/*
    Clase para manejar las operaciones sobre la tabla empresa en la base de datos.
*/
class EmpresaModel
{
    // Instancia de conexion la base de datos.
    private PDO $db;

    // Establece la conexion la base de datos.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
        * Funcion para obtener todos los datos de la tabla empresa de la base de datos.
        * @return $stmt un array de la tabla empresas.
        * POST: Devuelve un array con todos los datos almacenados en la tabla empresa.
    */
    public function getAllEmpresas(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM empresas");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  

    }

    /**
        * Funcion para obtener los datos de una empresa por numero de id.
        * PRE: Recibe el id para especificar de que fila se extraen los datos.
        * @param $id_empresa un numero entero indicando el id.
        * @return $stmt un array con los datos de la fila.
        * POST: Devuelve un array con todos los datos de la fila del id que se recibio.
    */
    public function getEmpresa($id_empresa): array
    {
        // Prepara consulta
        $stmt = $this->db->prepare("SELECT * FROM empresas WHERE id_empresa = :id_empresa");
        // Ejecucion de consulta
        $stmt->execute(['id_empresa' => $id_empresa]);
        // Devuelve el array
        return $stmt->fetch();
    }

    /** 
        * Funcion para modificar el nombre de una empresa referenciandose por su id. 
        * PRE: Se ingresa el id de la empresa y el nombre nuevo que remplazara al antiguo.
        * @param int $id_empresa es el id de la empresa a modificar. 
        * @param string $nombre_empresa es la cadena donde almacena el nuevo nombre.
        * @return bool $stmt valor booleano indicando la ejecucion.
        * POST: Devuelve un valor bool true si la ejecucion fue exitosa en caso contrario false.
    */
    public function updateEmpresa($id_empresa, $nombre_empresa): bool 
    {
        $stmt = $this->db->prepare("UPDATE empresas SET nombre = :nombre 
        WHERE id_empresa = :id_empresa");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_empresa' => $id_empresa,'nombre' => $nombre_empresa]);
        // Verifica si la actualización fue exitosa (si se afectaron filas)
        return $stmt->rowCount() > 0;
    }

    /** 
        * Funcion para almacenar una nueva empresa en la base de datos.
        * PRE: Una cadena de texto con el nombre.
        * @param string $nombre_empresa es el nombre de la nueva empresa que se almacenara. 
        * @return string $this almacena el id de la nueva empresa almacenada.
        * POST: Devuelve el id de la nueva impresa almacenada.
    */
    public function insertEmpresa($nombre_empresa)
    {
        $stmt = $this->db->prepare("INSERT INTO empresas (nombre) VALUES (:nombre)");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['nombre' => $nombre_empresa]);
        return $this->db->lastInsertId();
    }

    /** 
        * Funcion para eliminar una empresa de la base de datos.
        * PRE: Se recibe el id de la empresa a eliminar de la base de datos.
        * @param int $id_empresa id para referencias a la empresa en la base de datos.
        * @return bool $stmt valor bool indicando ejecucion. 
        * POST: Devuelve true en caso de que la ejecucion sea exitosa, en caso contrario false.
    */
    public function deleteEmpresa($id_empresa): bool
    {
        $stmt = $this->db->prepare("DELETE from empresas WHERE id_empresa = :id_empresa");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_empresa' => $id_empresa]);
        return $stmt->rowCount() > 0;
    }
}