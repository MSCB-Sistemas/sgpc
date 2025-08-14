<?php
class Inicio extends Control
{
    private $modelo;

    public function __construct()
    {
        $this->requireLogin();
        $this->modelo = $this->load_model('EstadisticasModel');
    }

    public function index()
    {
        $datos = [
            'title' => 'Inicio',
            'reservas_desde_hoy' => $this->modelo->getReservasDesdeHoy(),
        ];

        $this->load_view('Inicio', $datos);
    }
}
