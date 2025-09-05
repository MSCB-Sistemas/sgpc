<?php
/**
 * Controlador para gestionar operaciones relacionadas con las estadísticas.
 */
class Estadisticas extends Control
{
    private EstadisticasModel $model;

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
        

            $pagina_actual = 1;

            if (!empty($_GET['pagina'])){
                $pagina_actual = max(1, (int)$_GET['pagina']);
            }

            // Validar si se busca por chofer pero no se completó DNI
            if ($buscar_por === 'chofer' && empty($dni)) {
                // No hacer consulta, resultados vacíos y mostrar error
                $dni = null;
                $movimientos = $this->model->getPermisosFiltradosChofer(
                    $fecha_inicio,
                    $fecha_fin,
                    $dni,
                    $tipo,
                );
                
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

            

                // Obtener movimientos filtrados
                $movimientos = $this->model->getPermisosFiltradosChofer(
                    $fecha_inicio,
                    $fecha_fin,
                    $dni,
                    $tipo,
                    $limite_por_pagina,
                    $offset
                );

                $error = null;
            }
        $arribo = 'arribo';
        $salida = 'salida';
        // Preparar datos para la vista
        $promediosDiarios = $this->model->getPermisosPorDia($fecha_inicio_resumen, $fecha_fin_resumen);
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
            'recorrido_mas_usado' => $this->model->getRecorridoMasUtilizado($fecha_inicio_resumen, $fecha_fin_resumen),
            'arribo_mas_usado' => $this->model->getLugarMasUsado($arribo,$fecha_inicio_resumen, $fecha_fin_resumen),
            'salida_mas_usado' => $this->model->getLugarMasUsado($salida,$fecha_inicio_resumen, $fecha_fin_resumen),
            'promedio_diario' => $this->model->getPromedioPermisos($fecha_inicio_resumen, $fecha_fin_resumen),
            'labels' => array_column($promediosDiarios, 'dia'),
            'values' => array_column($promediosDiarios, 'total')
        ];  
        $this->load_view('estadisticas', $datos);
    }

    }
}
