<?php

class Nacionalidad extends Control
{
    private NacionalidadModel $model;

    public function __construct()
    {
        $this->requireLogin();
        $this->model = $this->load_model("NacionalidadModel");
    }
}
