<?php

/**
 * Controlador de LugarModel.php
 */
class Lugar extends Control
{
    private LugarModel $model;
    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('LugarModel');
    }

    // Mostrar todos los lugares en una vista.
    public function index()
    {
        $lugares = $this->model->getAllLugares();
        $datos = [
            'title' => 'Listado de Lugares',
            'urlCrear' => URL . '/lugar/create',
            'columnas' => ['Nombre'],
            'columnas_claves' => ['nombre'],
            'data' => $lugares,
            'acciones' => function($fila) {
                $id = $fila['id_lugar'];
                $url = URL . '/lugar';
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este lugar?\');">Eliminar</a>
                ';
            }
        ];    
        $this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar formulario para crear un nuevo.
    public function create()
    {
        $this->load_view('lugares/form', [
            'title' => 'Crear nuevo Lugar',
            'action' => URL . '/lugar/save',
            'values' => [],
            'errores' => [],
        ]);
    }

    // Procesar el formulario para guardar lugar nuevo.
    public function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"]);

            // Validaciones simples
            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";

            if (!empty($errores)) {
                $this->load_view('lugares/form', [
                    'title' => 'Crear nuevo Lugar',
                    'action' => URL . '/lugar/save',
                    'values' => $_POST,
                    'errores' => $errores,
                ]);
                return;
            }

            if ($this->model->insertLugar( $nombre)) {
                header("Location: " . URL . "/lugar");
                exit;
            } else {
                die("Error al guardar calle");
            }
        }
    }

    // Mostrar formulario para editar un lugar.
    public function edit($id)
    {
        $lugar = $this->model->getLugar($id);  

        if (!$lugar) {
            die("Lugar no encontrado");
        }

        $this->load_view('lugares/form', [
            'title' => 'Editar lugar',
            'action' => URL . '/lugar/update/' . $id,
            'values' => [
                'nombre' => $lugar['nombre']
            ],
            'errores' => [],
        ]);
    }

    // Procesar la actualización de lugar.
    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST["nombre"]);


            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";

            if (!empty($errores)) {
                $lugar = [
                    'nombre' => $nombre
                ];
                $this->load_view('lugares/form', [
                    'title' => 'Editar lugar',
                    'action' => URL . '/lugar/update/' . $id,
                    'values' => $lugar,
                    'errores' => $errores,
                ]);
                return;
            }

            if ($this->model->updateLugar($id,  $nombre)) {
                header("Location: " . URL . "/lugar");
                exit;
            } else {
                die("Error al actualizar lugar");
            }
        }
    }

    // Eliminar un lugar.
    public function delete($id)
    {
        $eliminado = $this->model->deleteLugar($id);
        if (!$eliminado) {
            die("No se puede eliminar el lugar.");
        }
        header("Location: " . URL . "/lugar");
        exit;
        
        
    }
    
    public function saveAjax()
    {
        header('Content-Type: application/json');
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = trim($_POST['nombre']);

            if ($nombre === '') {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                return;
            }
            $idLugar = $this->model->insertLugar($nombre);
            if ($idLugar) {
                echo json_encode([
                    'success' => true,
                    'id_lugar' => $idLugar,
                    'nombre' => $nombre
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar el lugar']);
            }
        }
    }
}