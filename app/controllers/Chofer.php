<?php
class Chofer extends Control
{
    private $modelo;

    public function __construct()
    {
        $this->modelo = $this->load_model('ChoferesModel');
    }

    public function index()
    {
        $choferes = $this->modelo->getAllChoferes();
        $datos = [
            'title' => 'Listado de Choferes',
            'urlCrear' => URL . '/chofer/create',
            'columnas' => ['Nombre', 'Apellido', 'DNI', 'Nacionalidad'],
            'columnas_claves' => ['nombre', 'apellido', 'dni', 'nacionalidad'],
            'data' => $choferes,
            'acciones' => function($fila) {
                $id = $fila['id_chofer'];
                $url = URL . '/chofer';
                return '
                    <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                    <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este chofer?\');">Eliminar</a>
                ';
            }
        ];    
        $this->load_view('partials/tablaAbm', $datos);
    }

    public function edit($id)
    {
        // lógica para mostrar vista de edición de un chofer
        $chofer = $this->modelo->getChofer($id);
        $this->load_view('choferes/edit', ['chofer' => $chofer, 'title' => 'Editar Chofer']);
    }
}
