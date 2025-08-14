<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Modelo para obtener analíticas y métricas del sistema.
 * Contiene métodos para calcular promedios, totales y estadísticas de permisos.
 */
class EstadisticasModel 
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Genera fechas por defecto (últimos 30 días) si no se proporcionan
     */
    private function establecerFechasPorDefecto(&$fecha_inicio, &$fecha_fin)
    {
        if (!$fecha_fin) {
            $fecha_fin = date('Y-m-d');
        }

        if (!$fecha_inicio) {
            $fecha_inicio = date('Y-m-d', strtotime('-30 days', strtotime($fecha_fin)));
        }
    }

    /**
     * Promedio de permisos emitidos por día
     */
    public function getPromedioPermisosPorDia($fecha_inicio = null, $fecha_fin = null): float
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT AVG(cantidad) AS promedio_diario
            FROM (
                SELECT DATE(fecha_emision) AS dia, COUNT(*) AS cantidad
                FROM permisos
                WHERE activo = 1 AND DATE(fecha_emision) BETWEEN :inicio AND :fin
                GROUP BY DATE(fecha_emision)
            ) AS sub
        ");
        $stmt->bindValue(':inicio', $fecha_inicio);
        $stmt->bindValue(':fin', $fecha_fin);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($resultado['promedio_diario'])) {
            return 0;
        }
        return (float)($resultado['promedio_diario']);
    }

    /**
     * Empresa con más permisos diarios en promedio
     */
    public function getEmpresaConMasPermisos($fecha_inicio = null, $fecha_fin = null): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.nombre, 
                COUNT(p.id_permiso) AS total,
                COUNT(p.id_permiso) / COUNT(DISTINCT DATE(p.fecha_reserva)) AS promedio_diario
            FROM permisos p
            JOIN servicios s ON p.id_servicio = s.id_servicio
            JOIN empresas e ON s.id_empresa = e.id_empresa
            WHERE p.activo = 1 
            AND DATE(p.fecha_reserva) BETWEEN :inicio AND :fin
            GROUP BY e.id_empresa
            ORDER BY promedio_diario DESC
            LIMIT 1
        ");
        $stmt->bindValue(':inicio', $fecha_inicio);
        $stmt->bindValue(':fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    /**
     * Promedio de permisos por tipo
     */
    public function getPromedioPermisosPorTipo($fecha_inicio = null, $fecha_fin = null): array
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT tipo, COUNT(*) / COUNT(DISTINCT DATE(fecha_reserva)) AS promedio_diario
            FROM permisos
            WHERE activo = 1 AND DATE(fecha_reserva) BETWEEN :inicio AND :fin
            GROUP BY tipo
        ");
        $stmt->bindValue(':inicio', $fecha_inicio);
        $stmt->bindValue(':fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recorrido más utilizado
     */
    public function getRecorridoMasUtilizado($fecha_inicio = null, $fecha_fin = null): ?array
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT r.nombre, COUNT(*) AS cantidad
            FROM recorridos_permisos rp
            JOIN recorridos r ON r.id_recorrido = rp.id_recorrido
            JOIN permisos p ON p.id_permiso = rp.id_permiso
            WHERE p.activo = 1 AND DATE(p.fecha_reserva) BETWEEN :inicio AND :fin
            GROUP BY r.id_recorrido
            ORDER BY cantidad DESC
            LIMIT 1
        ");
        $stmt->bindValue(':inicio', $fecha_inicio);
        $stmt->bindValue(':fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Punto de detención más utilizado
     */
    public function getPuntoMasUtilizado($fecha_inicio = null, $fecha_fin = null): ?array
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT pd.nombre, COUNT(*) AS cantidad
            FROM reservas_puntos rp
            JOIN puntos_detencion pd ON pd.id_punto_detencion = rp.id_punto_detencion
            JOIN permisos p ON p.id_permiso = rp.id_permiso
            WHERE p.activo = 1 AND DATE(p.fecha_reserva) BETWEEN :inicio AND :fin
            GROUP BY pd.id_punto_detencion
            ORDER BY cantidad DESC
            LIMIT 1
        ");
        $stmt->bindValue(':inicio', $fecha_inicio);
        $stmt->bindValue(':fin', $fecha_fin);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    /**
     * Obtiene un listado de permisos filtrados por fecha, DNI, tipo, orden y paginación.
     *
     * @param string|null $fecha_inicio Fecha inicial para filtrar los permisos.
     * @param string|null $fecha_fin Fecha final para filtrar los permisos.
     * @param string|null $dni DNI del chofer (si se busca por chofer).
     * @param string|null $tipo Tipo de servicio (línea, charter, etc.).
     * @param string $sort_col Columna por la que se ordenan los resultados.
     * @param string $sort_dir Dirección de ordenamiento (ASC o DESC).
     * @param int|null $limit Límite de resultados por página (para paginación).
     * @param int|null $offset Desplazamiento de la paginación.
     * @return array Arreglo asociativo con los permisos encontrados.
     */
    public function getPermisosFiltrados(
        $fecha_inicio = null,
        $fecha_fin = null,
        $dni = null,
        $tipo = null,
        $sort_col = 'fecha',
        $sort_dir = 'ASC',
        $limit = null,
        $offset = null
    ): array {
        // Columnas válidas para ordenar
        $columnasValidas = ['empresa', 'fecha', 'lugar', 'tipo_movimiento', 'cantidad'];
        $sort_col = in_array($sort_col, $columnasValidas) ? $sort_col : 'fecha';
        $sort_dir = strtoupper($sort_dir) === 'DESC' ? 'DESC' : 'ASC';

        // Consulta base con joins necesarios
        $sql = "
            SELECT
                e.nombre AS empresa,
                p.fecha_emision AS fecha,
                l.nombre AS lugar,
                p.arribo_salida AS tipo_movimiento,
                p.pasajeros AS cantidad
            FROM permisos p
            INNER JOIN servicios s ON p.id_servicio = s.id_servicio
            INNER JOIN empresas e ON s.id_empresa = e.id_empresa
            INNER JOIN lugares l ON p.id_lugar = l.id_lugar
            LEFT JOIN choferes c ON p.id_chofer = c.id_chofer
            WHERE p.activo = 1
        ";

        // Parametros de filtrado
        $params = [];

        if (!empty($fecha_inicio)) {
            $sql .= " AND DATE(p.fecha_emision) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fecha_inicio;
        }

        if (!empty($fecha_fin)) {
            $sql .= " AND DATE(p.fecha_emision) <= :fecha_fin";
            $params[':fecha_fin'] = $fecha_fin;
        }

        if (!empty($dni)) {
            $sql .= " AND c.dni = :dni";
            $params[':dni'] = $dni;
        }

        if (!empty($tipo)) {
            $sql .= " AND p.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        // Ordenamiento
        $sql .= " ORDER BY $sort_col $sort_dir";

        // Paginación (si se usa)
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        // Preparar consulta
        $stmt = $this->db->prepare($sql);

        // Asignar parámetros dinámicamente
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        // Asignar paginación si aplica
        if ($limit !== null && $offset !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        // Ejecutar y devolver resultados
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna la cantidad total de permisos que coinciden con los filtros aplicados.
     * Esta función se utiliza para calcular la cantidad de páginas en la paginación.
     *
     * @param string|null $fecha_inicio Fecha inicial de filtrado.
     * @param string|null $fecha_fin Fecha final de filtrado.
     * @param string|null $dni DNI del chofer si se filtra por chofer.
     * @param string|null $tipo Tipo de permiso (línea, charter, etc.).
     * @return int Número total de resultados que coinciden con los filtros.
     */
    public function getCantidadPermisosFiltrados($fecha_inicio = null, $fecha_fin = null, $dni = null, $tipo = null): int
    {
        // Consulta base con JOIN a choferes
        $sql = "
            SELECT COUNT(*)
            FROM permisos p
            LEFT JOIN choferes c ON p.id_chofer = c.id_chofer
            WHERE p.activo = 1
        ";

        $params = [];

        if (!empty($fecha_inicio)) {
            $sql .= " AND DATE(p.fecha_emision) >= :fecha_inicio";
            $params[':fecha_inicio'] = $fecha_inicio;
        }

        if (!empty($fecha_fin)) {
            $sql .= " AND DATE(p.fecha_emision) <= :fecha_fin";
            $params[':fecha_fin'] = $fecha_fin;
        }

        if (!empty($dni)) {
            $sql .= " AND c.dni = :dni";
            $params[':dni'] = $dni;
        }

        if (!empty($tipo)) {
            $sql .= " AND p.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }

        // Ejecutar consulta y retornar el número
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
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
                COUNT(CASE WHEN tipo='linea' AND arribo_salida='arribo' THEN 1 END) AS cantidad_linea_arribos,
                COUNT(CASE WHEN tipo='linea' AND arribo_salida='salida' THEN 1 END) AS cantidad_linea_salidas,
                COUNT(CASE WHEN tipo='charter' AND arribo_salida='arribo' THEN 1 END) AS cantidad_charter_arribos,
                COUNT(CASE WHEN tipo='charter' AND arribo_salida='salida' THEN 1 END) AS cantidad_charter_salidas,
                COUNT(CASE WHEN tipo='otros' AND arribo_salida='arribo' THEN 1 END) AS cantidad_otros_arribos,
                COUNT(CASE WHEN tipo='otros' AND arribo_salida='salida' THEN 1 END) AS cantidad_otros_salidas,
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
    public function getCantidadPorTipo($fecha_inicio = null, $fecha_fin = null): array|bool {
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
