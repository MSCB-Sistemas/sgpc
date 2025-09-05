<?php
  class Core
  {
    protected $controller;
    protected $method = 'index';
    protected $parameters = [];

    public function __construct()
    {
      $url = $this->getUrl();
      session_set_cookie_params([
            'lifetime' => 0,       // Expira al cerrar el navegador
            'path' => '/',
            'secure' => true,      // Solo HTTPS (false en desarrollo local)
            'httponly' => true,   // Protección contra JS malicioso
            'samesite' => 'Lax'   // Seguridad CSRF
      ]);
      session_start();
     // var_dump($_SESSION);
      //exit();
      // Si no hay controlador definido en la URL, redirigir según el login
      if (!$url || empty($url[0])) {
          if (isset($_SESSION['usuario_id'])) {
              $this->controller = 'Inicio';
              $this->method = 'index';
          } else {
              $this->controller = 'Auth';
              $this->method = 'login';
          }
      } else if(file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
          $this->controller = ucwords($url[0]);
          unset($url[0]);
      } else {
        if ($url[0]!='css'){
          $_SESSION['error_inicio'] = "Error: Controlador no encontrado: " . $url[0] ;
        }
        header("Location: " . URL);
        exit;
      }

      // Cargar el controlador
      require_once '../app/controllers/' . $this->controller . '.php';
      $this->controller = new $this->controller;

      // Si se definió el método en la URL
      if (isset($url[1])) {
          if (method_exists($this->controller, $url[1])) {
              $this->method = $url[1];
              unset($url[1]);
          }
      }

      // Parámetros si hay
      $this->parameters = $url ? array_values($url) : [];
      
      if (session_status() === PHP_SESSION_NONE) {
          session_start();
      }

      // Llamar al método con parámetros
      call_user_func_array([$this->controller, $this->method], $this->parameters);
    }

    public function getUrl()
    {
      if(isset($_GET['url']))
      {
        $url = rtrim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);

        return $url;
      }
    }
  }

?>