<?php
/**
 * Controlador para gestionar operaciones relacionadas con los permisos.
 */
class Analiticas extends Control
{
    private  $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('AnaliticasModel');
    }
    public function index()
    {
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin = $_GET['fecha_fin'] ?? null;

        $datos = [
            'title' => 'Analíticas',
            'promedio_diario' => $this->model->getPromedioPermisosPorDia($fecha_inicio, $fecha_fin),
            'empresa_mas_usada' => $this->model->getEmpresaConMasPermisos($fecha_inicio, $fecha_fin),
            'promedio_por_tipo' => $this->model->getPromedioPermisosPorTipo($fecha_inicio, $fecha_fin),
            'recorrido_mas_usado' => $this->model->getRecorridoMasUtilizado($fecha_inicio, $fecha_fin),
            'punto_mas_usado' => $this->model->getPuntoMasUtilizado($fecha_inicio, $fecha_fin),
            'movimientos' => $this->model->getMovimientosPorEmpresa($fecha_inicio, $fecha_fin),
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
        ];
        

        $this->load_view('analiticas', $datos);
    }


}