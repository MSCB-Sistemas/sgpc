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

        $datos = [
            'title' => 'Analíticas',
            'promedio_diario' => $this->model->getPromedioPermisosPorDia(),
            'empresa_mas_usada' => $this->model->getEmpresaConMasPermisos(),
            'promedio_por_tipo' => $this->model->getPromedioPermisosPorTipo(),
            'recorrido_mas_usado' => $this->model->getRecorridoMasUtilizado(),
            'punto_mas_usado' => $this->model->getPuntoMasUtilizado(),
        ];
        

        $this->load_view('analiticas', $datos);
    }


}