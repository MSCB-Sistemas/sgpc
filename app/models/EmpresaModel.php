<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auditoriaHelper.php';
require_once 'Database.php';

/*
    Clase para manejar las operaciones sobre la tabla empresa en la base de datos.
*/
class EmpresaModel
{
    private PDO $db;

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
        $stmt = $this->db->prepare("SELECT * FROM empresas where activo = 1");
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
        $query = "UPDATE empresas SET nombre = :nombre WHERE id_empresa = :id_empresa";
        $stmt = $this->db->prepare($query);

        $params = ['id_empresa' => $id_empresa,'nombre' => $nombre_empresa];

        auditoriaHelper::log(
            $_SESSION['id_usuario'],
            $query,
            $params
        );

        return $stmt->execute($params);
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
        $query = "INSERT INTO empresas (nombre) VALUES (:nombre)";
        $stmt = $this->db->prepare($query);

        $params = ['nombre' => $nombre_empresa];
        $stmt->execute($params);

        auditoriaHelper::Log(
            $_SESSION['id_usuario'],
            $query,
            $params
        ); 

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
        $query = "DELETE from empresas WHERE id_empresa = :id_empresa";
        $stmt = $this->db->prepare($query);

        $params = ['id_empresa' => $id_empresa];
        $stmt->execute($params);

        auditoriaHelper::Log(
            $_SESSION['id_usuario'],
            $query,
            $params
        );

        return $stmt->rowCount() > 0;
    }

    public function getPermisosByEmpresa($id_empresa): array
    {
        $stmt = $this->db->prepare("SELECT p.* FROM permisos p
                                    INNER JOIN servicios s ON p.id_servicio = s.id_servicio
                                    WHERE s.id_empresa = :id_empresa");
        $stmt->execute(['id_empresa' => $id_empresa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function desactivarEmpresa($id_empresa): bool
    {
        $query = "UPDATE empresas SET activo = 0 WHERE id_empresa = :id_empresa";
        $stmt = $this->db->prepare($query);

        $params = ['id_empresa' => $id_empresa];

        auditoriaHelper::Log(
            $_SESSION['id_usuario'],
            $query,
            $params
        );

        return $stmt->execute($params);
    }
}