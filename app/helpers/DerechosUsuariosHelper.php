<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Database.php';

class DerechosUsuariosHelper {

    public static function getDerechos($id_tipo_usuario) : array {
        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT d.descripcion FROM tipos_usuarios_derechos tud
                JOIN derechos d on d.id_derecho = tud.id_derecho
                WHERE id_tipo_usuario = :id_tipo_usuario;");
        $stmt->execute(['id_tipo_usuario' => $id_tipo_usuario]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN,0);
    }
}

?>