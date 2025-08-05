<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Class UsuariosModel
 *
 * Modelo para gestionar operaciones CRUD sobre la tabla 'usuarios'.
 */
class RememberTokensModel {
    /**
     * Instancia de la conexión PDO a la base de datos.
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase RememberTokensModel.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los tokens de "Recuérdame" almacenados en la base de datos.
     *
     * @return array Arreglo asociativo con todos los tokens.
     */
    public function getAllTokens() : array {
        $stmt= $this->db->prepare("SELECT * FROM remember_tokens");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Almacena un token de "Recuérdame" en la base de datos.
     *
     * @param int $id_usuario ID del usuario al que pertenece el token.
     * @param string $token Token generado para el usuario.
     * @param int $expiry Tiempo de expiración del token en formato timestamp.
     * @return bool True si se almacenó correctamente, false en caso contrario.
     */
    public function insertRememberMeToken($id_usuario, $token, int $expiry) {
        $stmt = $this->db->prepare("INSERT INTO remember_tokens (id_usuario, token, fecha_expiracion) VALUES (:id_usuario, :token, :fecha_expiracion)");
        $stmt->execute(['id_usuario' => $id_usuario, 'token' => $token, 'fecha_expiracion' => date('Y-m-d H:i:s', $expiry)]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Valida un token de "Recuérdame" para un usuario específico.
     *
     * @param int $id_usuario ID del usuario al que pertenece el token.
     * @param string $token Token a validar.
     * @return array|false Arreglo asociativo con el ID del usuario si es válido, false en caso contrario.
     */
    public function validateRememberMeToken($id_usuario, $token) {
        $stmt = $this->db->prepare("SELECT u.*
            FROM remember_tokens rt
            INNER JOIN usuarios u ON u.id_usuario = rt.id_usuario
            WHERE rt.id_usuario = :id_usuario
            AND rt.token = :token
            AND rt.fecha_expiracion > NOW()
            LIMIT 1
        ");    
        $stmt->execute([
            'id_usuario' => $id_usuario,
            'token' => $token
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Devuelve los datos completos del usuario
    }


    /**
     * Elimina un token de "Recuérdame" para un usuario específico.
     *
     * @param int $id_usuario ID del usuario al que pertenece el token.
     * @return bool True si se eliminó al menos un registro, false en caso contrario.
     */
    public function deleteRememberMeToken($id_usuario, $token) {
        $stmt = $this->db->prepare("DELETE FROM remember_tokens WHERE id_usuario = :id_usuario AND token = :token");
        $stmt->execute(['id_usuario' => $id_usuario, 'token' => $token]);
        return $stmt->rowCount() > 0;
    }
}