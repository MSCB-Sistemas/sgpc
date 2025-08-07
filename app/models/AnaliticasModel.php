<?php
require_once __DIR__ . '/../config/config.php';
require_once 'Database.php';

/**
 * Modelo para obtener analíticas y métricas del sistema.
 * Contiene métodos para calcular promedios, totales y estadísticas de permisos.
 * */
class AnaliticaModel 
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    /**
     * Promedio de permisos emitidos por día
     * @return float
     */
    public function getPromedioPermisosPorDia(): float
    {
        $stmt = $this->db->prepare("
            SELECT AVG(cantidad) AS promedio_diario
            FROM (
                SELECT DATE(fecha_emision) AS dia, COUNT(*) AS cantidad
                FROM permisos
                WHERE activo = 1
                GROUP BY DATE(fecha_emision)
            ) AS sub
        ");
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float)($resultado['promedio_diario'] ?? 0);
    }

    /**
     * Operación para obtener la empresa con más permisos en promedio por día
     * @return array|null
     */
    public function getEmpresaConMasPermisos(): ?array
    {
        $stmt = $this->db->prepare("
            SELECT e.nombre, COUNT(p.id_permiso) / COUNT(DISTINCT DATE(p.fecha_reserva)) AS promedio_diario
            FROM permisos p
            JOIN servicios s ON p.id_servicio = s.id_servicio
            JOIN empresas e ON s.id_empresa = e.id_empresa
            WHERE p.activo = 1
            GROUP BY e.id_empresa
            ORDER BY promedio_diario DESC
            LIMIT 1
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Operación para obtener el promedio de permisos por tipo
     * @return array
     */
    public function getPromedioPermisosPorTipo(): array
    {
        $stmt = $this->db->prepare("
            SELECT tipo, COUNT(*) / COUNT(DISTINCT DATE(fecha_reserva)) AS promedio_diario
            FROM permisos
            WHERE activo = 1
            GROUP BY tipo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Operación para obtener el recorrido más utilizado
     * @return array|null   
     */
    public function getRecorridoMasUtilizado(): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.nombre, COUNT(*) AS cantidad
            FROM recorridos_permisos rp
            JOIN recorridos r ON r.id_recorrido = rp.id_recorrido
            GROUP BY r.id_recorrido
            ORDER BY cantidad DESC
            LIMIT 1
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     *  Operación para obtener el punto de detención más utilizado
     * @return array|null       
     */
    public function getPuntoMasUtilizado(): ?array
    {
        $stmt = $this->db->prepare("
            SELECT pd.nombre, COUNT(*) AS cantidad
            FROM reservas_puntos rp
            JOIN puntos_detencion pd ON pd.id_punto_detencion = rp.id_punto_detencion
            GROUP BY pd.id_punto_detencion
            ORDER BY cantidad DESC
            LIMIT 1
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
