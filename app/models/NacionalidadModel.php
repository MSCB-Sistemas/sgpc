<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

class NacionalidadModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllNacionalidades(): array
    {
        $stmt = $this->db->query("SELECT * FROM nacionalidades");
        return $stmt->fetchAll();
    }

    public function getNacionalidad($id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM nacionalidades where id_nacionalidad = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function insertNacionalidad($nacionalidad): bool|string
    {
        $stmt = $this->db->prepare("INSERT INTO nacionalidades (nacionalidad) VALUES (:nacionalidad)");
        $stmt->execute(['nacionalidad' => $nacionalidad]);
        return $this->db->lastInsertId();
    }


    public function UpdateNacionalidad($id, $nuevaNacionalidad): bool
    {
        $stmt = $this->db->prepare("UPDATE nacionalidades set nacionalidad = :nuevaNacionalidad where id_nacionalidad = :id");
        $stmt->execute([
            'id' => $id,
            'nuevaNacionalidad' => $nuevaNacionalidad
        ]);
        return $stmt->rowCount() > 0;
    }
}
  