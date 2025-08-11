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

    public function index()
    {
        // Filtros desde GET
        $fecha_inicio = $_GET['fecha_inicio'] ?? null;
        $fecha_fin    = $_GET['fecha_fin'] ?? null;
        $buscar_por   = $_GET['buscar_por'] ?? null;
        $dni          = $_GET['dni'] ?? null;
        $tipo         = $_GET['tipo'] ?? null;

        // Validar si se busca por chofer pero no se completó DNI
        if ($buscar_por === 'chofer' && empty($dni)) {
            // No hacer consulta, resultados vacíos y mostrar error
            $movimientos = [];
            $total_resultados = 0;
            $total_paginas = 1;
            $error = 'Debe ingresar un DNI para buscar por chofer';
        } else {
            // Ajustar filtros según buscar_por
            if ($buscar_por !== 'chofer') {
                $dni = null;  // Ignorar DNI si no es por chofer
            }
            if ($buscar_por !== 'tipo' && $buscar_por !== 'chofer') {
                $tipo = null; // Ignorar tipo si no es por tipo o chofer
            }

            // Ordenamiento
            $sort_col = $_GET['sort_col'] ?? 'fecha';
            $sort_dir = strtoupper($_GET['sort_dir'] ?? 'ASC');
            if (!in_array($sort_dir, ['ASC', 'DESC'])) {
                $sort_dir = 'ASC';
            }

            // Paginación
            $pagina_actual     = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $limite_por_pagina = 20;
            $offset            = ($pagina_actual - 1) * $limite_por_pagina;

            // Total de resultados y total de páginas para la paginación
            $total_resultados = $this->model->getCantidadPermisosFiltrados($fecha_inicio, $fecha_fin, $dni, $tipo);
            $total_paginas    = max(1, ceil($total_resultados / $limite_por_pagina));

            // Obtener movimientos filtrados
            $movimientos = $this->model->getPermisosFiltrados(
                $fecha_inicio,
                $fecha_fin,
                $dni,
                $tipo,
                $sort_col,
                $sort_dir,
                $limite_por_pagina,
                $offset
            );

            $error = null;
        }

        // Preparar datos para la vista
        $datos = [
            'movimientos'   => $movimientos,
            'fecha_inicio'  => $fecha_inicio,
            'fecha_fin'     => $fecha_fin,
            'dni'           => $dni,
            'tipo'          => $tipo,
            'buscar_por'    => $buscar_por,
            'sort_col'      => $sort_col ?? 'fecha',
            'sort_dir'      => $sort_dir ?? 'ASC',
            'pagina_actual' => $pagina_actual ?? 1,
            'total_paginas' => $total_paginas ?? 1,
            'error'         => $error,
        ];

        $this->load_view('estadisticas', $datos);
    }
}
