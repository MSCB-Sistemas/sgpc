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
        $stmt = $this->db->query("SELECT * FROM calles");
        return $stmt->fetchAll();
    }
}
  