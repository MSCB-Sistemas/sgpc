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
        $errores = [];
        if (isset($_SESSION['error_inicio'])) {
            $errores[] = $_SESSION['error_inicio'];
            unset($_SESSION['error_inicio']); // Borramos el mensaje después de usarlo
        }
        $datos = [
            'title' => 'Inicio',
            'reservas_desde_hoy' => $this->modelo->getReservasDesdeHoy(),
            'errores' => $errores
        ];

        $this->load_view('inicio', $datos);
    }
}
