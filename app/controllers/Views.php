<?php
  require_once APP . '/helpers/authHelper.php';
  class Views extends Control
  {
    
    public function inicio()
    {
      require_login();
      if (!is_logged_in_admin()) {
        echo "No autorizado";
        exit;
      }
      $datos = [
          'title' => 'Inicio'
      ];
      $this->load_view('inicio', $datos);
    }

    public function pdf()
    {

      $this->load_view('partials/permisoPdf');

    }

    public function manual()
    {
      $file = APP . '/views/manual.pdf'; // Ruta absoluta del PDF

      if (file_exists($file)) {
          // Forzar descarga o abrir en navegador
          header('Content-Type: application/pdf');
          header('Content-Disposition: inline; filename="manual.pdf"');
          header('Content-Length: ' . filesize($file));
          readfile($file);
          exit;
      } else {
          $_SESSION['error_inicio'] = "El manual de usuario no está disponible. Ruta: ". $file;
          header("Location: " . URL);
          exit;
      }
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