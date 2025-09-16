<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/logHelper.php';
require_once __DIR__ .'/../helpers/auditoriaHelper.php';
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
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario and activo = 1");
         // Asegurarse de que el nombre de usuario no sea nulo o vacío
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
        $query = "UPDATE usuarios SET usuario = :usuario, nombre = :nombre, apellido = :apellido, cargo = :cargo, sector = :sector, id_tipo_usuario = :id_tipo_usuario WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $params = ['id_usuario' => $id_usuario, 'usuario' => $usuario, 'nombre' => $nombre, 'apellido' => $apellido, 'cargo' => $cargo, 'sector' => $sector, 'id_tipo_usuario' => $id_tipo_usuario];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        
        if($stmt->execute($params)){
            return true;
        }else{
            writeLog("❌ Error: No se pudo actualizar el usuario con id ".$id_usuario." en la base de datos. Query: ".$query."parametros: ".json_encode($params));

            return false;
        }
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
        $query = "INSERT INTO usuarios (usuario, nombre, apellido, cargo, sector, contrasenia, id_tipo_usuario) VALUES (:usuario, :nombre, :apellido, :cargo, :sector, :contrasenia, :id_tipo_usuario)";
        $stmt = $this->db->prepare($query);
        $params = ['usuario' => $usuario, 'nombre' => $nombre, 'apellido' => $apellido, 'cargo' => $cargo, 'sector' => $sector, 'contrasenia' => $contrasenia, 'id_tipo_usuario' => $id_tipo_usuario];
        $stmt->execute($params);
        $result = $this->db->lastInsertId();
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        
        if (!$result) {
            writeLog("❌ Error: No se pudo insertar el usuario ".$usuario." en la base de datos. Query: ".$query."parametros: ".$params);
        }

        return $result;
    }
    
    /**
     * Desactiva un usuario de la base de datos por su ID.
     *
     * @param int $id_usuario ID del usuario a desactivar.
     * @return bool True si se desactivó al menos un registro, false en caso contrario.
     */
    public function deleteUsuario($id_usuario) : bool {
        $query = "UPDATE usuarios SET activo = 0 WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $params = ['id_usuario' => $id_usuario];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) {
            writeLog("❌ Error: No se pudo eliminar el usuario con id ".$id_usuario." en la base de datos. Query: ".$query."parametros: ".json_encode($params));
        }

        return $stmt->rowCount() > 0;
    }

    /**
     * Activa un usuario de la base de datos por su ID.
     *
     * @param int $id_usuario ID del usuario a activar.
     * @return bool True si se activó el usuario, false en caso contrario.
     */
    public function activateUsuario($id_usuario) : bool {
        $query = "UPDATE usuarios SET activo = 1 WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $params = ['id_usuario' => $id_usuario];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) {
            writeLog("❌ Error: No se pudo activar el usuario con id ".$id_usuario." en la base de datos. Query: ".$query."parametros: ".json_encode($params));
        }

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
        $query = "UPDATE usuarios SET contrasenia = :contrasenia WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $params = ['id_usuario' => $id_usuario, 'contrasenia'=> $password];
        
        auditoriaHelper::log(
            $_SESSION['usuario_id'],
            $query,
            $params
        );
        // Ejecuta la consulta pasando los valores
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) {
            writeLog("❌ Error: No se pudo actualizar la contraseña del usuario id".$id_usuario." en la base de datos. Query: ".$query."parametros: ".json_encode($params));
        }

        return $stmt->rowCount() > 0;
    }

    /**
     * Obtiene un usuario basado en su id .
     *
     * @param int $id $id_usuario ID del usuario.
     * @return array $stmt un arreglo que va a almacenar los datos de ese usuario.
     */
    public function getUsuarioById($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUsuariosServerSide($start, $length, $searchValue, $orderColumn, $orderDir)
    {
        $sql = "SELECT 
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
            tipos_usuarios tu ON u.id_tipo_usuario = tu.id_tipo_usuario";
        $params = [];

        // Si hay búsqueda
        if (!empty($searchValue)) {
            $sql .= " WHERE u.nombre LIKE :search 
                    OR u.apellido LIKE :search 
                    OR u.sector LIKE :search 
                    OR u.usuario LIKE :search 
                    OR u.cargo LIKE :search
                    OR tu.tipo_usuario LIKE :search ";
            $params[':search'] = "%$searchValue%";
        }

        // Orden
        $sql .= " ORDER BY $orderColumn $orderDir";

        // Paginación
        $sql .= " LIMIT :start, :length";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->bindValue(':start', (int) $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', (int) $length, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function contarUsuariosFiltrados($searchValue)
    {
        $sql = "SELECT COUNT(*) as total FROM usuarios u 
            inner JOIN tipos_usuarios tu ON u.id_tipo_usuario = tu.id_tipo_usuario;";
        $params = [];

        if (!empty($searchValue)) {
            $sql .= " WHERE u.nombre LIKE :search 
                    OR u.apellido LIKE :search 
                    OR u.sector LIKE :search 
                    OR u.usuario LIKE :search 
                    OR u.cargo LIKE :search
                    OR tu.tipo_usuario LIKE :search ";
            $params[':search'] = "%$searchValue%";
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    public function contarUsuarios()
    {
        $sql = "SELECT COUNT(*) as total FROM usuarios";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

} 
?>