<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Modelo para manejar las operaciones CRUD sobre la tabla `permisos`.
 *
 * Esta clase permite gestionar los permisos registrados en el sistema,
 * incluyendo su creación, edición, consulta y desactivación lógica.
 */
class PermisoModel
{
    /**
     * Instancia de conexión PDO a la base de datos.
     *
     * @var PDO
     */
    private PDO $db;

    /**
     * Constructor de la clase.
     *
     * Establece la conexión con la base de datos utilizando la clase `Database`.
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Obtiene todos los permisos registrados.
     *
     * @return array Arreglo asociativo con todos los registros de la tabla `permisos`.
     */
    public function getAllPermisos($activos): array
    {
        $sql = "SELECT
            p.id_permiso,
            p.tipo,
            p.fecha_reserva,
            p.fecha_emision,
            p.arribo_salida,
            p.observacion,
            p.pasajeros,
            l.nombre as lugar,

            -- Datos del chofer
            CONCAT(c.dni,' - ',c.nombre,' ',c.apellido) AS chofer,

            -- Datos del usuario
            CONCAT(u.nombre,' ',u.apellido) AS usuario,

            -- Datos del servicio
            s.interno AS servicio_interno,
            s.dominio AS servicio_dominio,
            e.nombre AS empresa_nombre

        FROM permisos p
        JOIN choferes c ON p.id_chofer = c.id_chofer
        JOIN nacionalidades n ON c.id_nacionalidad = n.id_nacionalidad
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        JOIN servicios s ON p.id_servicio = s.id_servicio
        JOIN empresas e ON s.id_empresa = e.id_empresa
        JOIN lugares l ON p.id_lugar = l.id_lugar;
        ";

        if ($activos === true) {
            $sql .= " WHERE p.activo = 1";
        }

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene un permiso específico por su ID.
     *
     * @param int|string $id ID del permiso.
     * @return array Arreglo asociativo con los datos del permiso.
     */
    public function getPermiso($id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM permisos WHERE id_permiso = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getPermisoPdf($id): array
    {
        $stmt = $this->db->prepare("SELECT 
            p.tipo ,
            p.arribo_salida ,
            DATE(p.fecha_reserva) as fecha_reserva ,
            e.nombre as empresa ,
            s.dominio ,
            s.interno ,
            p.pasajeros ,
            p.observacion ,
            rp.id_recorrido ,
            c.nombre as nombre_chofer,
            c.apellido as apellido_chofer,
            c.dni as dni_chofer,
            u.nombre as usuario_nombre,
            u.apellido as usuario_apellido,
            u.cargo as usuario_cargo,
            u.sector as usuario_sector
            from permisos p
            inner join choferes c on p.id_chofer = c.id_chofer
            inner join usuarios u on p.id_usuario = u.id_usuario
            inner join servicios s on p.id_servicio = s.id_servicio 
            inner join empresas e on s.id_empresa = e.id_empresa 
            inner join recorridos_permisos rp on p.id_permiso = rp.id_permiso
            where p.id_permiso = :id;
            ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getPermisosByChofer($id_chofer): array
    {
        $stmt = $this->db->prepare("SELECT * FROM permisos WHERE id_chofer = :id");
        $stmt->execute(['id' => $id_chofer]);
        return $stmt->fetchAll();
    }

    /**
     * Inserta un nuevo permiso en la base de datos.
     *
     * @param int|string $id_chofer ID del chofer asignado.
     * @param int|string $id_usuario ID del usuario que genera el permiso.
     * @param int|string $id_servicio ID del servicio asociado.
     * @param string $tipo Tipo de permiso ("salida" o "arribo").
     * @param string $fecha_reserva Fecha de reserva del permiso (formato 'YYYY-MM-DD HH:MM:SS').
     * @param string $fecha_emision Fecha de emisión del permiso (formato 'YYYY-MM-DD HH:MM:SS').
     * @param bool|int $arribo_salida Indicador si es un permiso de arribo (1 o 0).
     * @param string|null $observacion Observaciones adicionales (puede ser null).
     * @return bool|string ID del nuevo registro insertado o false en caso de error.
     */
    public function insertPermiso($id_chofer, $id_usuario, $id_servicio, $tipo, $fecha_reserva, $fecha_emision, $arribo_salida, $observacion,$pasajeros,$id_lugar): bool|string
    {
        $stmt = $this->db->prepare("INSERT INTO sgpc.permisos
            (id_chofer, id_usuario, id_servicio, tipo, fecha_reserva, fecha_emision, arribo_salida, observacion, pasajeros, id_lugar)
            VALUES (:id_chofer, :id_usuario, :id_servicio, :tipo, :fecha_reserva, :fecha_emision, :arribo_salida, :observacion, :pasajeros, :id_lugar)");
        $stmt->execute([
            'id_chofer' => $id_chofer,
            'id_usuario' => $id_usuario,
            'id_servicio' => $id_servicio,
            'tipo' => $tipo,
            'fecha_reserva' => $fecha_reserva,
            'fecha_emision' => $fecha_emision,
            'arribo_salida' => $arribo_salida,
            'observacion' => $observacion,
            'pasajeros'=> $pasajeros,
            'id_lugar'=> $id_lugar
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Actualiza los datos de un permiso existente.
     *
     * @param int|string $id ID del permiso a actualizar.
     * @param int|string $id_chofer ID del chofer.
     * @param int|string $id_usuario ID del usuario.
     * @param int|string $id_servicio ID del servicio.
     * @param string $tipo Tipo de permiso ("salida" o "arribo").
     * @param string $fecha_reserva Fecha de reserva (formato 'YYYY-MM-DD HH:MM:SS').
     * @param string $fecha_emision Fecha de emisión (formato 'YYYY-MM-DD HH:MM:SS').
     * @param bool|int $arribo_salida Indicador si es un permiso de arribo (1 o 0).
     * @param string|null $observacion Observaciones adicionales.
     * @param bool|int $activo Estado del permiso (1 para activo, 0 para inactivo).
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updatePermiso($id, $id_chofer, $id_usuario, $id_servicio, $tipo, $fecha_reserva, $fecha_emision, $arribo_salida, $observacion, $activo): bool|string
    {
        $stmt = $this->db->prepare("UPDATE sgpc.permisos
            SET id_chofer = :id_chofer, id_usuario = :id_usuario, id_servicio = :id_servicio, tipo = :tipo,
                fecha_reserva = :fecha_reserva, fecha_emision = :fecha_emision, arribo_salida = :arribo_salida,
                observacion = :observacion, activo = :activo
            WHERE id_permiso = :id");
        
        return $stmt->execute([
            'id' => $id,
            'id_chofer' => $id_chofer,
            'id_usuario' => $id_usuario,
            'id_servicio' => $id_servicio,
            'tipo' => $tipo,
            'fecha_reserva' => $fecha_reserva,
            'fecha_emision' => $fecha_emision,
            'arribo_salida' => $arribo_salida,
            'observacion' => $observacion,
            'activo' => $activo
        ]);
    }

    /**
     * Desactiva lógicamente un permiso en lugar de eliminarlo físicamente.
     *
     * @param int|string $id ID del permiso a desactivar.
     * @return bool True si se modificó al menos un registro, false en caso contrario.
     */
    public function deletePermiso($id): bool
    {
        $stmt = $this->db->prepare("UPDATE permisos SET activo = 0 WHERE id_permiso = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function getPermisosByServicio($id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM permisos WHERE id_servicio = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

}
