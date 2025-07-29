<?php
  class Control
  {
    public function load_model($model)
    {
      require_once '../app/models/' . $model . '.php';

      return new $model;
    }

    public function load_view($view, $datos = [], $layout = 'main')
    {
      $viewFile = APP . '/views/pages/' . $view . '.php';

      if (file_exists($viewFile)) {
        if ($layout) {
          $viewPath = $viewFile;
          require_once APP . "/views/layout/{$layout}.php";
        } else {
          require_once $viewFile;
        }
      } else {
        die($viewFile);
      }
    }
  }
?>