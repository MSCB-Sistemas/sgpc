<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Modelo para obtener analíticas y métricas del sistema.
 * Contiene métodos para calcular promedios, totales y estadísticas de permisos.
 */
class AnaliticasModel 
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
        return (float)($resultado['promedio_diario'] ?? 0);
    }

    /**
     * Empresa con más permisos diarios en promedio
     */
    public function getEmpresaConMasPermisos($fecha_inicio = null, $fecha_fin = null): ?array
    {
        $this->establecerFechasPorDefecto($fecha_inicio, $fecha_fin);

        $stmt = $this->db->prepare("
            SELECT e.nombre, COUNT(p.id_permiso) / COUNT(DISTINCT DATE(p.fecha_reserva)) AS promedio_diario
            FROM permisos p
            JOIN servicios s ON p.id_servicio = s.id_servicio
            JOIN empresas e ON s.id_empresa = e.id_empresa
            WHERE p.activo = 1 AND DATE(p.fecha_reserva) BETWEEN :inicio AND :fin
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
     * Obtener movimientos por empresa
     */
    public function getMovimientosPorEmpresa($fecha_inicio, $fecha_fin): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.nombre AS empresa,
                DATE(p.fecha_reserva) AS fecha,
                pd.nombre AS lugar,
                p.arribo_salida,
                SUM(p.pasajeros) AS cantidad
            FROM permisos p
            JOIN servicios s ON s.id_servicio = p.id_servicio
            JOIN empresas e ON e.id_empresa = s.id_empresa
            JOIN reservas_puntos rp ON rp.id_permiso = p.id_permiso
            JOIN puntos_detencion pd ON pd.id_punto_detencion = rp.id_punto_detencion
            WHERE p.fecha_reserva BETWEEN :fecha_inicio AND :fecha_fin
            AND p.activo = 1
            GROUP BY 
                e.id_empresa, 
                DATE(p.fecha_reserva), 
                pd.id_punto_detencion, 
                p.arribo_salida
            ORDER BY fecha DESC;

        ");
        $fecha_inicio = $_GET['fecha_inicio'] ?? '2025-01-01';
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    }

}
