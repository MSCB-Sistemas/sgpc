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
    public function getAllPermisos(): array
    {
        $stmt = $this->db->query("SELECT
            p.id_permiso,
            p.tipo,
            p.fecha_reserva,
            p.fecha_emision,
            p.es_arribo,
            p.observacion,

            -- Datos del chofer
            c.dni AS chofer_dni,
            c.nombre AS chofer_nombre,
            c.apellido AS chofer_apellido,
            n.nacionalidad AS chofer_nacionalidad,

            -- Datos del usuario
            u.nombre AS usuario_nombre,
            u.apellido AS usuario_apellido,
            u.cargo AS usuario_cargo,

            -- Datos del servicio
            s.interno AS servicio_interno,
            s.dominio AS servicio_dominio,
            e.nombre AS empresa_nombre

        FROM permisos p
        JOIN choferes c ON p.id_chofer = c.id_chofer
        JOIN nacionalidades n ON c.id_nacionalidad = n.id_nacionalidad
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        JOIN servicios s ON p.id_servicio = s.id_servicio
        JOIN empresas e ON s.id_empresa = e.id_empresa;
        ");
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
     * @param bool|int $es_arribo Indicador si es un permiso de arribo (1 o 0).
     * @param string|null $observacion Observaciones adicionales (puede ser null).
     * @return bool|string ID del nuevo registro insertado o false en caso de error.
     */
    public function insertPermiso($id_chofer, $id_usuario, $id_servicio, $tipo, $fecha_reserva, $fecha_emision, $es_arribo, $observacion): bool|string
    {
        $stmt = $this->db->prepare("INSERT INTO sgpc.permisos
            (id_chofer, id_usuario, id_servicio, tipo, fecha_reserva, fecha_emision, es_arribo, observacion)
            VALUES (:id_chofer, :id_usuario, :id_servicio, :tipo, :fecha_reserva, :fecha_emision, :es_arribo, :observacion)");
        $stmt->execute([
            'id_chofer' => $id_chofer,
            'id_usuario' => $id_usuario,
            'id_servicio' => $id_servicio,
            'tipo' => $tipo,
            'fecha_reserva' => $fecha_reserva,
            'fecha_emision' => $fecha_emision,
            'es_arribo' => $es_arribo,
            'observacion' => $observacion
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
     * @param bool|int $es_arribo Indicador si es un permiso de arribo (1 o 0).
     * @param string|null $observacion Observaciones adicionales.
     * @param bool|int $activo Estado del permiso (1 para activo, 0 para inactivo).
     * @return bool True si se actualizó al menos un registro, false en caso contrario.
     */
    public function updatePermiso($id, $id_chofer, $id_usuario, $id_servicio, $tipo, $fecha_reserva, $fecha_emision, $es_arribo, $observacion, $activo): bool|string
    {
        $stmt = $this->db->prepare("UPDATE sgpc.permisos
            SET id_chofer = :id_chofer, id_usuario = :id_usuario, id_servicio = :id_servicio, tipo = :tipo,
                fecha_reserva = :fecha_reserva, fecha_emision = :fecha_emision, es_arribo = :es_arribo,
                observacion = :observacion, activo = :activo
            WHERE id_permiso = :id");
        $stmt->execute([
            'id' => $id,
            'id_chofer' => $id_chofer,
            'id_usuario' => $id_usuario,
            'id_servicio' => $id_servicio,
            'tipo' => $tipo,
            'fecha_reserva' => $fecha_reserva,
            'fecha_emision' => $fecha_emision,
            'es_arribo' => $es_arribo,
            'observacion' => $observacion,
            'activo' => $activo
        ]);
        return $stmt->rowCount() > 0;
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

    /**
     * Obtiene la cantidad de permisos por tipo (charter, línea, otros).
     *
     * @return array Arreglo asociativo con la cantidad de permisos por tipo.
     */
    public function getPromediosPermisos($fecha_inicio = null, $fecha_fin = null): array {
        // Fechas por defecto: último mes
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-1 month', strtotime($fecha_fin)));
        }

        $sql = "
            SELECT 
                COUNT(CASE WHEN tipo='linea' AND es_arribo=1 THEN 1 END) AS cantidad_linea_arribos,
                COUNT(CASE WHEN tipo='linea' AND es_arribo=0 THEN 1 END) AS cantidad_linea_salidas,
                COUNT(CASE WHEN tipo='charter' AND es_arribo=1 THEN 1 END) AS cantidad_charter_arribos,
                COUNT(CASE WHEN tipo='charter' AND es_arribo=0 THEN 1 END) AS cantidad_charter_salidas,
                COUNT(CASE WHEN tipo='otros' AND es_arribo=1 THEN 1 END) AS cantidad_otros_arribos,
                COUNT(CASE WHEN tipo='otros' AND es_arribo=0 THEN 1 END) AS cantidad_otros_salidas,
                COUNT(*) AS cantidad_total
            FROM permisos
            WHERE activo = 1
            AND DATE(fecha_reserva) BETWEEN :fecha_inicio AND :fecha_fin
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




    /**
     * Obtiene la cantidad de permisos agrupados por tipo.
     *
     * @return array Arreglo asociativo con la cantidad de permisos por tipo.
     */
    public function getCantidadPorTipo($fecha_inicio = null, $fecha_fin = null): array {
    // Si no se pasa fecha, por defecto el último mes
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-1 month', strtotime($fecha_fin)));
        }

        $stmt = $this->db->prepare("
            SELECT tipo, COUNT(*) AS cantidad 
            FROM permisos 
            WHERE activo = 1
            AND DATE(fecha_reserva) BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY tipo
            ORDER BY cantidad DESC
            LIMIT 1
        ");

        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // solo un registro
    }




    /**
     * Obtiene la empresa más frecuente en los permisos.
     * @return array
     */
    public function getEmpresasMasFrecuentes($fecha_inicio = null, $fecha_fin = null, $limit = 5): array {
        // Si no se pasa fecha, por defecto el último mes
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-1 month', strtotime($fecha_fin)));
        }

        $stmt = $this->db->prepare("
            SELECT e.nombre AS nombre_empresa, COUNT(*) AS cantidad
            FROM permisos p
            JOIN servicios s ON p.id_servicio = s.id_servicio
            JOIN empresas e ON s.id_empresa = e.id_empresa
            WHERE p.activo = 1
            AND DATE(p.fecha_reserva) BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY e.nombre
            ORDER BY cantidad DESC
            LIMIT :limit
        ");

        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Obtiene los hoteles más utilizados en las reservas de puntos.
     * Se limita a los 5 hoteles más frecuentes.
     * @return array
     */
    public function getHotelesMasUsados($fecha_inicio = null, $fecha_fin = null, $limit = 5): array {
        // Si no se pasa fecha, por defecto el último mes
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-1 month', strtotime($fecha_fin)));
        }

            $limit = (int)$limit;
            $stmt = $this->db->prepare("
                SELECT h.nombre AS nombre_hotel, COUNT(*) AS cantidad
                FROM reservas_puntos rp
                JOIN hoteles h ON rp.id_hotel = h.id_hotel
                WHERE rp.id_hotel IS NOT NULL
                AND DATE(rp.fecha_horario) BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY h.nombre
                ORDER BY cantidad DESC
                LIMIT $limit
            ");




    $stmt->bindValue(':fecha_inicio', $fecha_inicio);
    $stmt->bindValue(':fecha_fin', $fecha_fin);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }



    /**
     * Obtiene los puntos de ingreso más utilizados en las reservas de puntos.
     * Se limita a los 5 puntos más frecuentes.
     * @return array
     */
    public function getReservasDesdeHoy(): array {
        $stmt = $this->db->prepare("
            SELECT 
                pd.nombre AS punto,
                c.nombre AS calle,
                rp.fecha_horario
            FROM reservas_puntos rp
            JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion
            JOIN calles c ON pd.id_calle = c.id_calle
            WHERE rp.fecha_horario >= CURRENT_DATE
            ORDER BY rp.fecha_horario ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los puntos de detención más utilizados en las reservas de puntos.
     * Se limita a los 5 puntos más frecuentes.
     * @return array
     */
    public function getPuntosMasUsados(?string $fecha_inicio = null, ?string $fecha_fin = null): array {
    // Si no se pasan fechas, usar un rango por defecto, por ejemplo el último mes
        if (!$fecha_inicio || !$fecha_fin) {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-1 month', strtotime($fecha_fin)));
        }

        $stmt = $this->db->prepare("
            SELECT pd.nombre AS nombre_punto, COUNT(*) AS cantidad
            FROM reservas_puntos rp
            JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion
            WHERE rp.fecha_horario BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY pd.nombre
            ORDER BY cantidad DESC
            LIMIT 5
        ");

        $stmt->execute([
            'fecha_inicio' => $fecha_inicio . ' 00:00:00',
            'fecha_fin' => $fecha_fin . ' 23:59:59'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
