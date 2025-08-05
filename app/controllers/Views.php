<?php
  class Views extends Control
  {
    
    public function inicio()
    {
      $this->requireLogin();
      if (!is_logged_in_admin()) {
        echo "No autorizado";
        exit;
      }
      $datos = [
          'title' => 'Inicio'
      ];
      $this->load_view('inicio', $datos);
    }

    /**public function login()
    {
      $datos = [
        'title' => "Login"
      ];
      $this->load_view('login', $datos);
    }
    */
    
    public function update($id)
    {
      echo "Update view " . $id;
    }

  }
?>