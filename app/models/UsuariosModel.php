<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Class UsuariosModel
 *
 * Modelo para gestionar operaciones CRUD sobre la tabla 'usuarios'.
 */
class UsuariosModel {
    /**
     * Instancia de la conexión PDO a la base de datos.
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase UsuariosModel.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct() {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los usuarios de la base de datos.
     *
     * @return array Arreglo asociativo con todos los usuarios.
     */
    public function getAllUsuarios(): array {
        $stmt = $this->db->prepare("SELECT 
            u.id_usuario,
            u.usuario,
            u.nombre,
            u.apellido,
            u.cargo,
            u.sector,
            tu.tipo_usuario,
            u.activo
        FROM 
            usuarios u
        JOIN 
            tipos_usuarios tu ON u.id_tipo_usuario = tu.id_tipo_usuario;
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        
    /**
     * Obtiene la información de un usuario específico por su ID.
     *
     * @param int $id_usuario ID del usuario a consultar.
     * @return array|false Arreglo asociativo con los datos del usuario o false si no existe.
     */
    public function getUsuario($id_usuario) : array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => $id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsuarioByNombreUsuario($nombre_usuario) : array|bool {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $stmt->execute(['usuario' => $nombre_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de un usuario existente.
     *
     * @param int $id_usuario ID del usuario a actualizar.
     * @param string $usuario Nuevo nombre de usuario.
     * @param string $nombre Nuevo nombre.
     * @param string $apellido Nuevo apellido.
     * @param string $cargo Nuevo cargo.
     * @param string $sector Nuevo sector.
     * @param string $contrasenia Nueva contraseña.
     * @param int $id_tipo_usuario Nuevo tipo de usuario.
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updateUsuario($id_usuario, $usuario, $nombre, $apellido, $cargo, $sector, $id_tipo_usuario) : bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET usuario = :usuario, nombre = :nombre, apellido = :apellido, cargo = :cargo, sector = :sector, id_tipo_usuario = :id_tipo_usuario WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => $id_usuario, 'usuario' => $usuario, 'nombre' => $nombre, 'apellido' => $apellido, 'cargo' => $cargo, 'sector' => $sector, 'id_tipo_usuario' => $id_tipo_usuario]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Inserta un nuevo usuario en la base de datos.
     *
     * @param string $usuario Nombre de usuario.
     * @param string $nombre Nombre.
     * @param string $apellido Apellido.
     * @param string $cargo Cargo.
     * @param string $sector Sector.
     * @param string $contrasenia Contraseña.
     * @param int $id_tipo_usuario Tipo de usuario.
     * @return int|string ID del usuario insertado.
     */
    public function insertUsuario($usuario, $nombre, $apellido, $cargo, $sector, $contrasenia, $id_tipo_usuario) {
        $stmt = $this->db->prepare("INSERT INTO usuarios (usuario, nombre, apellido, cargo, sector, contrasenia, id_tipo_usuario) VALUES (:usuario, :nombre, :apellido, :cargo, :sector, :contrasenia, :id_tipo_usuario)");
        $stmt->execute(['usuario' => $usuario, 'nombre' => $nombre, 'apellido' => $apellido, 'cargo' => $cargo, 'sector' => $sector, 'contrasenia' => $contrasenia, 'id_tipo_usuario' => $id_tipo_usuario]);
        return $this->db->lastInsertId();
    }

    /**
     * Desactiva un usuario de la base de datos por su ID.
     *
     * @param int $id_usuario ID del usuario a desactivar.
     * @return bool True si se desactivó al menos un registro, false en caso contrario.
     */
    public function deleteUsuario($id_usuario) : bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => $id_usuario]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Activa un usuario de la base de datos por su ID.
     *
     * @param int $id_usuario ID del usuario a activar.
     * @return bool True si se activó el usuario, false en caso contrario.
     */
    public function activateUsuario($id_usuario) : bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET activo = 1 WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => $id_usuario]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Actualiza la contraseña de un usuario.
     *
     * @param  mixed $id_usuario ID del usuario cuya contraseña se actualizará.
     * @param  mixed $password  Nueva contraseña del usuario.
     * @return bool True si se actualizó la contraseña, false en caso contrario.
     */
    public function updatePassword($id_usuario, $password) : bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET contrasenia = :contrasenia WHERE id_usuario = :id_usuario");
        $stmt->execute(['id_usuario' => $id_usuario, 'contrasenia'=> $password]);
        return $stmt->rowCount() > 0;
    }

    public function getUsuarioById($id)
{
    // Ejemplo usando PDO
    $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
} 
?>