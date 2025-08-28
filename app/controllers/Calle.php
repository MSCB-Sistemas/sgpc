<?php

/**
 * Controlador de CalleModel.php
 */
class Calle extends Control
{
    private CalleModel $model;
    private PuntosDetencionModel $pDModel;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('CalleModel');
        $this->pDModel = $this->load_model('PuntosDetencionModel');
    }

    // Mostrar todas las calles en una vista.
    public function index($errores = [])
    {
        if ($this->tienePermiso('ver abm')) {
            if (isset($_SESSION['error_calle'])) {
                $errores[] = $_SESSION['error_calle'];
                unset($_SESSION['error_calle']); // Borramos el mensaje después de usarlo
            }

            $calles = $this->model->getAllCalles();
            $datos = [
                'title' => 'Listado de Calles',
                'urlCrear' => URL . '/calle/create',
                'columnas' => ['Nombre'],
                'columnas_claves' => ['nombre'],
                'data' => $calles,
                'acciones' => function($fila) {
                    $id = $fila['id_calle'];
                    $url = URL . '/calle';
                    $botones = '';
                    if ($this->tienePermiso('editar abm')){
                        $botones .= '<a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>';
                    }
                    if ($this->tienePermiso('borrar abm')){
                        $botones .='<a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar esta calle?\');">Eliminar</a>';
                    }
                    return $botones;
                },
                'errores' => $errores
            ];    
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }

    // Mostrar formulario para crear una calle nueva.
    public function create()
    {
        if ($this->tienePermiso("cargar abm")) {
            $this->load_view('calle/form', [
                'title' => 'Crear nueva calle',
                'action' => URL . '/calle/save',
                'values' => [],
                'errores' => [],
            ]);
        } else {
            header("Location: " . URL);
        }
    }

    // Procesar el formulario para guardar calle nueva.
    public function save()
    {
        if ($this->tienePermiso("cargar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre = trim($_POST["nombre"]);

                // Validaciones simples
                $errores = [];
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
                if (!empty($errores)) {
                        $this->load_view('calle/form', [
                            'title' => 'Crear nueva calle',
                            'action' => URL . '/calle/save',
                            'values' => $_POST,
                            'errores' => $errores,
                        ]);
                        return;
                    }
                try{
                    
                    if ($this->model->insertCalle( $nombre)) {
                        header("Location: " . URL . "/calle");
                        exit;
                    } else {
                        die("Error al guardar calle");
                    }
                } catch (Exception $e) {
                    if ($e->getCode() == 23000) {
                        $errores[] = "La calle '{$_POST['nombre']}' ya existe en el sistema.";
                    } else {
                        $errores[] = "Error al guardar calle: " . $e->getMessage();
                    }
                    $this->load_view('calle/form', [
                        'title' => 'Crear nueva calle',
                        'action' => URL . '/calle/save',
                        'values' => $_POST,
                        'errores' => $errores,
                    ]);

                }
            }
        } else {
            header("Location: " . URL);
        }
    }

    // Mostrar formulario para editar una calle.
    public function edit($id)
    {
        if ($this->tienePermiso("editar abm")) {
            $calle = $this->model->getCalle($id);
            $permisos = $this->model->getPermisosByCalle($id);

            if (!$calle) {
                die("Calle no encontrada");
            }

            if (!empty($permisos)) {
                $_SESSION['error_calle'] = "Esta calle no se puede editar porque tiene permisos asociados";
                header("Location: " . URL . "/calle");
                exit;
            }

            $this->load_view('calle/form', [
                'title' => 'Editar calle',
                'action' => URL . '/calle/update/' . $id,
                'values' => [
                    'nombre' => $calle['nombre']
                ],
                'errores' => [],
            ]);
        
        } else {
            header("Location: " . URL);
        }
    }

    // Procesar la actualización de calle.
    public function update($id)
    {
        if ($this->tienePermiso("editar abm")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nombre = trim($_POST["nombre"]);


                $errores = [];
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";

                if (!empty($errores)) {
                    $calle = [
                        'nombre' => $nombre
                    ];
                    $this->load_view('calle/form', [
                        'title' => 'Editar calle',
                        'action' => URL . '/calle/update/' . $id,
                        'values' => $calle,
                        'errores' => $errores,
                    ]);
                    return;
                }
                try {
                    if ($this->model->updateCalle($id,  nombre_calle: $nombre)) {
                        header("Location: " . URL . "/calle");
                        exit;
                    } else {
                        die("Error al actualizar calle");
                    }
                } catch (Exception $e) {
                    if ($e->getCode() == 23000) {
                        $errores[] = "La calle '{$_POST['nombre']}' ya existe en el sistema.";
                    } else {
                        $errores[] = "Error al guardar calle: " . $e->getMessage();
                    }
                    $this->load_view('calle/form', [
                        'title' => 'Crear nueva calle',
                        'action' => URL . '/calle/save',
                        'values' => $_POST,
                        'errores' => $errores,
                    ]);

                }
            }
        
        } else {
            header("Location: " . URL);
        }
    }

    // Eliminar una calle.
    public function delete($id)
    {
        if ($this->tienePermiso("borrar abm")) {
            $puntos = $this->pDModel->getPuntosByCalle($id);
            $recorridos =$this->load_model('CalleRecorridoModel')->getRecorridosByCalle($id);
            $errores = [];
            if (empty($puntos) && empty($recorridos)) {
                $eliminado = $this->model->deleteCalle($id);
                if (!$eliminado) {
                    $this->index(errores: ["No se puede eliminar la calle."]);
                }
                header("Location: " . URL . "/calle");
                exit;
            }

            if (!empty($puntos)) {
                $nombres_puntos = $puntos ? array_column($puntos, 'nombre') : [];
                $string_puntos = implode(', ', $nombres_puntos);
                $errores[] = "No se puede eliminar la calle, tiene los siguientes puntos de detención asociados: ". $string_puntos;
            } 
            
            if (!empty($recorridos)){
                $nombres_recorridos = $recorridos ? array_column($recorridos, 'nombre') : [];
                $string_recorridos = implode(', ', $nombres_recorridos);
                $errores[] = "No se puede eliminar la calle, tiene los siguientes recorridos asociados: ". $string_recorridos;
            }

            $this->index($errores);
        } else {
            header("Location: " . URL);
        }
    }

    public function puntos($id)
    {
        $puntos = $this->pDModel->getPuntosByCalle($id);
        header('Content-Type: application/json');
        echo json_encode($puntos);
    }

    public function calles()
    {
        $calles = $this->model->getAllCalles();
        header('Content-Type: application/json');
        echo json_encode($calles);
    }
}

