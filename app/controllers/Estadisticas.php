<?php
/**
 * Controlador para gestionar operaciones relacionadas con las estadísticas.
 */
class Estadisticas extends Control
{
    private $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('EstadisticasModel');
    }

    public function index($errores = [])
{
    if ($this->tienePermiso("ver estadisticas")) {

        // Cargar modelo si no está cargado
        $this->load_model('EstadisticasModel');

        // Obtener filtros desde GET o POST
        $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
        $dni = $_GET['dni'] ?? null;
        $tipo = $_GET['tipo'] ?? null; // línea, charter, otros
        $buscar_por = $_GET['buscar_por'] ?? 'chofer'; // o empresa
        $pagina_actual = $_GET['pagina'] ?? 1;
        $limite_por_pagina = 20;
        $offset = ($pagina_actual - 1) * $limite_por_pagina;

        // Validar fechas
        if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
            $errores[] = "La fecha de inicio no puede ser mayor a la de fin.";
            $movimientos = [];
            $total_resultados = 0;
            $total_paginas = 1;
        } else {
            // Obtener resultados filtrados
            $movimientos = $this->model->getPermisosFiltradosChofer(
                $fecha_inicio,
                $fecha_fin,
                $dni,
                $tipo,
                $limite_por_pagina,
                $offset
            );

            
        }

        // Datos de resumen
        $fecha_inicio_resumen = $fecha_inicio;
        $fecha_fin_resumen = $fecha_fin;

        $datos = [
            'title' => 'Estadísticas de Movimientos',
            'urlCrear' => null, // No se usa en este caso
            'columnas' => ['Fecha', 'Chofer', 'DNI', 'Empresa', 'Tipo', 'Lugar', 'Pasajeros'],
            'columnas_claves' => ['fecha', 'nombre_chofer', 'dni', 'empresa', 'tipo', 'lugar', 'cantidad_pasajeros'],
            'data' => $movimientos,
            'acciones' => null, // No hay acciones en estadísticas

            // Filtros y navegación
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'dni' => $dni,
            'tipo' => $tipo,
            'buscar_por' => $buscar_por,
            'pagina_actual' => $pagina_actual,
            //'total_paginas' => $total_paginas,
            //'total_resultados' => $total_resultados,
            'errores' => $errores,

            // Resumenes
            'por_tipo' => $this->model->getServicioMasUsado($fecha_inicio_resumen, $fecha_fin_resumen),
            'empresa_mas_usada' => $this->model->getEmpresaConMasPermisos($fecha_inicio_resumen, $fecha_fin_resumen),
            'hoteles_usados' => $this->model->getHotelesMasUsados($fecha_inicio_resumen, $fecha_fin_resumen),
            'punto_mas_usado' => $this->model->getPuntosMasUsados($fecha_inicio_resumen, $fecha_fin_resumen),
            'recorrido_mas_usado' => $this->model->getRecorridoMasUtilizado($fecha_inicio_resumen, $fecha_fin_resumen),
            'arribo_mas_usado' => $this->model->getLugarMasUsado($fecha_inicio_resumen, $fecha_fin_resumen, 'arribo'),
            'salida_mas_usada' => $this->model->getLugarMasUsado($fecha_inicio_resumen, $fecha_fin_resumen, 'salida')
        ];

        $this->load_view('partials/tablaAbm', $datos);
    } else {
        header("Location: " . URL);
        exit;
    }
}

}
