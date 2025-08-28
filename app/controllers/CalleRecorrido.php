<?php
/**
 * Controlador para manejar las operaciones relacionadas con las calles recorridas.
 * Permite crear, editar, eliminar y listar las relaciones entre calles y recorridos.
 */
class CalleRecorrido extends Control
{
    private CalleRecorridoModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model('CalleRecorridoModel');
    }
}
