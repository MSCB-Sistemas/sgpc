<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/*
    Clase para manejar las operaciones sobre la tabla calles en la base de datos.
*/
class CalleModel
{
    // Instancia de conexion la base de datos.
    private PDO $db;

    // Establece la conexion la base de datos.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /** 
        * Funcion que ejecuta una query para devolver todo el conjunto de calles de la base
        * de datos en un array.
        * @return array $stmt un array con todos los valores cargados.
        * POST: Un array con todas las calles almacenadas en la base de datos.
    */
    public function getAllCalles(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM calles");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    /** 
        * Funcion para obtener en una calle en especifico segun su id.
        * PRE: Recibe el id de calle que se quiere obtener.
        * @param int $id_calle es el id de la calle que se requiere buscar en la base de datos.
        * @return array $stmt un array con los datos de la fila en ese id.
        * POST: Devolvera un array con todos los datos que tenga la calle con ese
        * id en la base de datos.
    */
    public function getCalle($id_calle): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM calles WHERE id_calle = :id_calle");
        $stmt->execute(['id_calle' => $id_calle]);
        return $stmt->fetch();
    }

    /** 
        * Funcion que ejecuta una query para modificar el nombre de un calle segun el id recibido.
        * PRE: Recibe la id de la calle especificada y el nombre nuevo que sera guardado
        * en esa id.
        * @param int $id_calle es el id de la calle que se quiere modificar.
        * @param string $nombre_calle es el nombre nuevo que sera almacenado en la base de datos
        * segun el id.
        * @return bool $stmt un valor de tipo bool.
        * POST: Un valor booleano que devolvera true en caso de ejecucion exitosa o false
        * en caso contrario.
    */
    public function updateCalle($id_calle, $nombre_calle): bool
    {
        $stmt = $this->db->prepare("UPDATE calles SET nombre = :nombre 
        WHERE id_calle = :id_calle");
        // Ejecuta la consulta pasando los valores
        return $stmt->execute(['id_calle' => $id_calle,'nombre' => $nombre_calle ]);
    }
    /** 
        * Funcion que ejecuta una query para insertar una nueva calle en la base de datos.
        * PRE: Recibe el nombre de la nueva calle a ser almacenada en la base de datos.
        * @param int $nombre_calle el nombre de la calle nueva.
        * @return string $this Devuelve el id del nuevo elemento insertado.
        * POST: Devuelve el id autoincremental del elemenento que se almaceno en la ejecucion.
    */
    public function insertCalle($nombre_calle)
    {
        $stmt = $this->db->prepare("INSERT INTO calles (nombre) VALUES (:nombre)");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['nombre' => $nombre_calle]);
        return $this->db->lastInsertId();
    }

    /** 
        * Funcion para ejecutar una query que elimine una calle por referencia de id.
        * PRE: ID de la calle.
        * @param int $id_calle es la id de la calle.
        * @return bool $stmt es un valor bool verificando la ejecucion.
        * POST: un valor boleano que sera true en caso de ejecucion exitosa o false en caso
        * contrario.
    */
    public function deleteCalle($id_calle): bool
    {
        $stmt = $this->db->prepare("DELETE from calles WHERE id_calle = :id_calle");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_calle' => $id_calle]);
        return $stmt->rowCount() > 0;
    }
}

  