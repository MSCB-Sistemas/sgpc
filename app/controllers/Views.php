<?php
  class Views extends Control
  {

    public function inicio()
    {
      $datos = [
        "title" => "Inicio"
      ];
      $this->load_view('inicio', $datos);
    }

    public function login()
    {
      $datos = [
        "title" => "Login"
      ];
      $this->load_view('login', $datos);
    }

    public function pginicio()
    {
      $datos = [
        "title" => "Página de Inicio"
      ];
      $this->load_view('pginicio', $datos);
    }

    public function update($id)
    {
      echo "Update view " . $id;
    }

  }
?>