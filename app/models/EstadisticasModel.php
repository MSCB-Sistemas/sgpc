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
     * Empresa con más permisos diarios en promedio
     */
    public function getEmpresaConMasPermisos($fecha_inicio = null, $fecha_fin = null): array
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

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
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            return $resultado;
        } 
        return [];

    }


    /**
     * Promedio de permisos rango de fechas
     */
    public function getPromedioPermisos($fecha_inicio = null, $fecha_fin = null): string|null
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT tipo, COUNT(*) / COUNT(DISTINCT DATE(fecha_reserva)) AS promedio_diario
            FROM permisos
            WHERE activo = 1 AND DATE(fecha_reserva) BETWEEN :inicio AND :fin
        ");
        $stmt->bindValue(':inicio', $fecha_inicio);
        $stmt->bindValue(':fin', $fecha_fin);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            return $resultado['promedio_diario'];
        }
        return '';
        
    }

    /**
     * Promedio de permisos por día rango de fechas
     */
    public function getPermisosPorDia($fecha_inicio = null, $fecha_fin = null): array
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT DATE(fecha_emision) AS dia, COUNT(*) AS total
            FROM permisos
            WHERE activo = 1 
            AND DATE(fecha_emision) BETWEEN :inicio AND :fin
            GROUP BY DATE(fecha_emision)
            ORDER BY dia;
        ");
        $stmt->bindValue(':inicio', $fecha_inicio);
        $stmt->bindValue(':fin', $fecha_fin);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Recorrido más utilizado
     */
    public function getRecorridoMasUtilizado($fecha_inicio = null, $fecha_fin = null): array
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
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado) {
            return $resultado;
        } 
        return [];
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
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);
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
     * Funcion para obtener los datos de las estadisticas
     * @param mixed $fecha_inicio
     * @param mixed $fecha_fin
     * @param mixed $dni
     * @param mixed $tipo
     * @param mixed $offset
     * @param mixed $limite_por_pagina
     * @return array
     */
    public function getPermisosFiltradosChofer($fecha_inicio = null, $fecha_fin = null, $dni = null, $tipo = null, $offset = 0, $limite_por_pagina = null): array
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);
        $sql = "
            SELECT 
                p.*, 
                CONCAT(c.apellido,' ',c.nombre) AS chofer_completo,
                e.nombre AS empresa, 
                l.nombre AS lugar
            FROM permisos p
            LEFT JOIN choferes c ON p.id_chofer = c.id_chofer
            LEFT JOIN servicios s ON p.id_servicio = s.id_servicio
            LEFT JOIN empresas e ON s.id_empresa = e.id_empresa
            LEFT JOIN lugares l  ON p.id_lugar = l.id_lugar
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

        if (!empty($limite_por_pagina)) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if (!empty($limite_por_pagina)) {
            $stmt->bindValue(':limit', (int)$limite_por_pagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtiene los hoteles más utilizados en las reservas de puntos.
     * Se limita a los 5 hoteles más frecuentes.
     * @return array
     */
    public function getHotelesMasUsados($fecha_inicio = null, $fecha_fin = null, $limit = 5): array {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

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
                        s.dominio AS dominio,
                        c.nombre AS calle,
                        e.nombre AS empresa,
                        rp.fecha_horario,
                        p.tipo AS tipo
                    FROM reservas_puntos rp
                    JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion
                    JOIN calles c ON pd.id_calle = c.id_calle
                    JOIN permisos p ON rp.id_permiso = p.id_permiso
                    JOIN servicios s ON p.id_servicio = s.id_servicio
                    JOIN empresas e ON s.id_empresa = e.id_empresa
                    WHERE rp.fecha_horario >= CURRENT_DATE
                    ORDER BY rp.fecha_horario ASC;
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los puntos de detención más utilizados en las reservas de puntos.
     * Se limita a los 5 puntos más frecuentes.
     * @return array
     */
    public function getPuntosMasUsados( $fecha_inicio = null, $fecha_fin = null): array {

        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT pd.nombre AS nombre, COUNT(*) AS cantidad
            FROM reservas_puntos rp
            JOIN puntos_detencion pd ON rp.id_punto_detencion = pd.id_punto_detencion
            WHERE rp.fecha_horario BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY pd.nombre
            ORDER BY cantidad DESC
            LIMIT 1
        ");

        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Funcion para obtener el servicio mas usado
     */
    public function getServicioMasUsado($fecha_inicio = null, $fecha_fin = null): array {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT tipo, COUNT(*) AS cantidad
            FROM permisos p
            WHERE fecha_emision BETWEEN :fecha_inicio AND :fecha_fin
            GROUP BY tipo
            ORDER BY cantidad DESC
            LIMIT 1
        ");

        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLugarMasUsado($tipo_movimiento, $fecha_inicio = null, $fecha_fin = null): array {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);
        $stmt = $this->db->prepare("
            SELECT l.nombre as nombre_lugar, COUNT(*) AS cantidad
            FROM permisos p
            JOIN lugares l ON p.id_lugar = l.id_lugar
            WHERE p.fecha_emision BETWEEN :fecha_inicio AND :fecha_fin
            AND p.arribo_salida = :tipo_movimiento
            GROUP BY l.nombre
            ORDER BY cantidad DESC
            LIMIT 1
        ");

        $stmt->bindValue(':fecha_inicio', $fecha_inicio);
        $stmt->bindValue(':fecha_fin', $fecha_fin);
        $stmt->bindValue(':tipo_movimiento', $tipo_movimiento);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
}
