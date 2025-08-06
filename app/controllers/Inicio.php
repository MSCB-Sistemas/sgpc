<?php
class Inicio extends Control
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->load_model('PermisoModel');
    }

    public function index()
    {
        // Capturar fechas desde GET
        if (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
            $fecha_inicio = $_GET['fecha_inicio'];
            $fecha_fin = $_GET['fecha_fin'];
        } else {
            $fecha_fin = date('Y-m-d');
            $fecha_inicio = date('Y-m-d', strtotime('-1 month', strtotime($fecha_fin)));
        }
        
        $datos = [


            'title' => 'Inicio',
            'por_tipo' => $this->modelo->getCantidadPorTipo($fecha_inicio, $fecha_fin),
            'hoteles_usados' => $this->modelo->getHotelesMasUsados($fecha_inicio, $fecha_fin),
            'empresas_frecuentes' => $this->modelo->getEmpresasMasFrecuentes($fecha_inicio, $fecha_fin),
            'promedio_ingresos' => $this->modelo->getPromediosPermisos($fecha_inicio, $fecha_fin),
            'reservas_desde_hoy' => $this->modelo->getReservasDesdeHoy()
            
        ];

        $this->load_view('Inicio', $datos);
    }
}
