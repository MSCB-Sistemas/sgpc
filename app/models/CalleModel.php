<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

class CalleModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllCalles(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM calles");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    public function getCalle($id_calle): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM calles WHERE id_calle = :id_calle");
        $stmt->execute(['id_calle' => $id_calle]);
        return $stmt->fetch();
    }

    public function updateCalle($id_calle, $nombre_calle): bool
    {
        $stmt = $this->db->prepare("UPDATE calles SET nombre = :nombre 
        WHERE id_calle = :id_calle");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_calle' => $id_calle,'nombre' => $nombre_calle ]);
        // Verifica si la actualización fue exitosa (si se afectaron filas)
        return $stmt->rowCount() > 0;
    }

    public function insertCalle($nombre_calle)
    {
        $stmt = $this->db->prepare("INSERT INTO calles (nombre) VALUES (:nombre)");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['nombre' => $nombre_calle]);
        return $this->db->lastInsertId();
    }

}

  