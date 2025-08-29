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
        if (in_array('ver estadisticas',$_SESSION['usuario_derechos'])) {
            $fecha_inicio = '';
            $fecha_fin    = '';
            $buscar_por   = '';
            $dni          = '';
            $tipo         = '';
            $fecha_inicio_resumen = '2000-01-01';
            $fecha_fin_resumen    = date('Y-m-d');

            if (!empty($_GET['fecha_inicio'])) {
                $fecha_inicio = $_GET['fecha_inicio'];
            }

            if (!empty($_GET['fecha_fin'])) {
                $fecha_fin = $_GET['fecha_fin'];
            }

            if (!empty($_GET['buscar_por'])) {
                $buscar_por = $_GET['buscar_por'];
            }

            if (!empty($_GET['dni'])) {
                $dni = $_GET['dni'];
            }

            if (!empty($_GET['tipo'])) {
                $tipo = $_GET['tipo'];
            }        

            if (!empty($_GET['fecha_inicio_resumen'])) {
                $fecha_inicio_resumen = $_GET['fecha_inicio_resumen'];
            }

        if (!empty($_GET['fecha_fin_resumen'])) {
            $fecha_fin_resumen = $_GET['fecha_fin_resumen'];
        }
        
        // Llamada al modelo
       


            // Paginación

            $pagina_actual = 1;

            if (!empty($_GET['pagina'])){
                $pagina_actual = max(1, (int)$_GET['pagina']);
            }

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
                $limite_por_pagina = 10;
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
            'title' => 'Estadísticas',
            'movimientos'   => $movimientos,
            'fecha_inicio'  => $fecha_inicio,
            'fecha_fin'     => $fecha_fin,
            'dni'           => $dni,
            'tipo'          => $tipo,
            'buscar_por'    => $buscar_por,
            'pagina_actual' => $pagina_actual,
            'total_paginas' => $total_paginas,
            'error'         => $error,
            'total_resultados' => $total_resultados,
            'por_tipo' => $this->model->getServicioMasUsado($fecha_inicio_resumen, $fecha_fin_resumen),
            'empresa_mas_usada' => $this->model->getEmpresaConMasPermisos($fecha_inicio_resumen, $fecha_fin_resumen), 
            'hoteles_usados' => $this->model->getHotelesMasUsados($fecha_inicio_resumen, $fecha_fin_resumen),
            'punto_mas_usado' => $this->model->getPuntosMasUsados($fecha_inicio_resumen, $fecha_fin_resumen),
            'recorrido_mas_usado' => $this->model->getRecorridoMasUtilizado($fecha_inicio_resumen, $fecha_fin_resumen)
            ,
        ];  
        $this->load_view('estadisticas', $datos);
    }

    public function resumenCardsAjax() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $valor_fecha_inicio_resumen = $_POST['fecha_inicio_resumen'];
            $valor_fecha_fin_resumen    = $_POST['fecha_fin_resumen'];

            $datos = [
                'empresa_mas_usada' => $this->model->getEmpresaConMasPermisos($valor_fecha_inicio_resumen, $valor_fecha_fin_resumen), 
                'hoteles_usados' => $this->model->getHotelesMasUsados($valor_fecha_inicio_resumen, $valor_fecha_fin_resumen),
                'punto_mas_usado' => $this->model->getPuntosMasUsados($valor_fecha_inicio_resumen, $valor_fecha_fin_resumen),
                'recorrido_mas_usado' => $this->model->getRecorridoMasUtilizado($valor_fecha_inicio_resumen, $valor_fecha_fin_resumen)
            ];

            
            $this->load_view('resumen', $datos, false); // el tercer parámetro en false para que no cargue el layout
        }
    }


}
