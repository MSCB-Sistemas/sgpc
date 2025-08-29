<?php
require_once __DIR__ . '/../models/Database.php';

class auditoriaHelper {
    public static function log($id_usuario, $query, $param): bool {
        try {
            $db = Database::connect();

            $stmt = $db->prepare("INSERT INTO auditoria (id_usuario, fecha_creacion, query, parametros) 
                        VALUES (:id_usuario, :fecha_creacion, :query, :parametros)"
                        );
            
            return $stmt->execute([
                            ':id_usuario' => $id_usuario,
                            ':fecha_creacion' => date('Y-m-d H:i:s'),
                            ':query' => $query,
                            ':parametros' => json_encode($param, JSON_UNESCAPED_UNICODE)
                        ]);

        } catch (Exception $e) {
            error_log('Error al registrar auditoria: ' . $e->getMessage());
            return false;
        }
    }
}
?>