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
        $this->load_view('choferes/index', ['choferes' => $choferes, 'title' => 'Listado de Choferes']);
    }

    public function edit($id)
    {
        // lógica para mostrar vista de edición de un chofer
        $chofer = $this->modelo->getChofer($id);
        $this->load_view('choferes/edit', ['chofer' => $chofer, 'title' => 'Editar Chofer']);
    }
}
