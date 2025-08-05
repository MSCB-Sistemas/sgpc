<?php
class Inicio extends Control
{
    private $modelo;

    public function __construct()
    {
        
        $this->modelo = $this->load_model('ReservasPuntosModel');
    }

    public function index()
    {
        $datos = [
            'title' => 'Inicio',
            'por_tipo' => $this->modelo->getCantidadPorTipo(),
            'hoteles_usados' => $this->modelo->getHotelesMasUsados(),
            'empresas_frecuentes' => $this->modelo->getEmpresasMasFrecuentes(),
            'promedio_ingresos' => $this->modelo->getPromedioIngresos(),
            'reservas_desde_hoy' => $this->modelo->getReservasDesdeHoy()
        ];

        $this->load_view('Inicio', $datos);
    }
}
