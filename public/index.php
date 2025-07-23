<?php
  require_once '../app/init.php';
  $init = new Core;

  require_once __DIR__ . '/../app/models/CalleModel.php';

    $model = new CalleModel();
    $calles = $model->getAllCalles();

    foreach ($calles as $calle) {
        echo $calle['nombre'] . '<br>';
    }

?>