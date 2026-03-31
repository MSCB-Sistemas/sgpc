<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/*
    Clase para manejar las operaciones sobre la tabla tipos usuarios en la base de datos.
*/
class TiposUsuariosModel
{
    // Instancia de conexion la base de datos.
    private PDO $db;

    // Establece la conexion la base de datos.
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los registros de la tabla `tipos_usuarios`.
     *
     * PRE: La tabla `tipos_usuarios` debe existir.
     *
     * @return array  Arreglo asociativo con todos los tipos de usuarios.
     *
     * POST: Devuelve un listado completo de registros de la tabla.
     */
    public function getAllTiposUsuarios(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tipos_usuarios");
        // Ejecución de la consulta
        $stmt->execute(); 
        // Devuelve el resultado como un arreglo asociativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);  
    }

    /**
     * Obtiene un tipo de usuario por su ID.
     *
     * PRE: Debe existir un registro con el ID proporcionado.
     *
     * @param int $id_tipo_usuario  ID del tipo de usuario.
     * @return array                Arreglo asociativo con los datos del tipo de usuario, o false si no existe.
     *
     * POST: Devuelve la información del tipo de usuario correspondiente al ID.
     */ 
    public function getTipoUsuario($id_tipo_usuario): array 
    {
        $stmt = $this->db->prepare("SELECT * FROM tipos_usuarios WHERE id_tipo_usuario = :id_tipo_usuario");
        $stmt->execute(['id_tipo_usuario' => $id_tipo_usuario]);
        return $stmt->fetch();
    }

    /**
     * Actualiza el nombre del tipo de usuario por su ID.
     *
     * PRE: Debe existir un tipo de usuario con ese ID.
     *
     * @param int $id_tipo_usuario  ID del tipo de usuario a modificar.
     * @param string $tipo_usuario  Nuevo nombre del tipo de usuario.
     * @return bool                 True si la actualización afectó filas, false si no.
     *
     * POST: Se actualiza el campo `tipo_usuario` en el registro correspondiente.
     */
    public function updateTipoUsuario($id_tipo_usuario, $tipo_usuario): bool
    {
        $stmt = $this->db->prepare("UPDATE tipos_usuarios SET tipo_usuario = :tipo_usuario
        WHERE id_tipo_usuario = :id_tipo_usuario");
        // Ejecuta la consulta pasando los valores
        
        return $stmt->execute(['id_tipo_usuario' => $id_tipo_usuario,'tipo_usuario' => $tipo_usuario ]);
    }
 
    /**
     * Inserta un nuevo tipo de usuario en la base de datos.
     *
     * PRE: El valor de tipo_usuario no debe ser nulo o vacío.
     *
     * @param string $tipo_usuario  Nombre del nuevo tipo de usuario.
     * @return string               ID del nuevo registro insertado (como cadena).
     *
     * POST: Se agrega un nuevo registro a la tabla `tipos_usuarios`.
     */
    public function insertTipoUsuario($tipo_usuario)
    {
        $stmt = $this->db->prepare("INSERT INTO tipos_usuarios (tipo_usuario) VALUES (:tipo_usuario)");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['tipo_usuario' => $tipo_usuario]);
        return $this->db->lastInsertId();
    }

    /**
     * Elimina un tipo de usuario de la base de datos por su ID.
     *
     * PRE: Debe existir un registro con ese ID en la tabla `tipos_usuarios`.
     *
     * @param int $id_tipo_usuario  ID del tipo de usuario a eliminar.
     * @return bool                 True si se eliminó al menos una fila, false en caso contrario.
     *
     * POST: El registro correspondiente al ID es eliminado de la base de datos.
     */
    public function deleteTipoUsuario($id_tipo_usuario): bool
    {
        $stmt = $this->db->prepare("DELETE from tipos_usuarios WHERE id_tipo_usuario = :id_tipo_usuarios");
        // Ejecuta la consulta pasando los valores
        $stmt->execute(['id_tipo_usuario' => $id_tipo_usuario]);
        return $stmt->rowCount() > 0;
    }
}

  