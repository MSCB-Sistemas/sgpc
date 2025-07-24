<?php
  require_once '../app/init.php';
  $init = new Core;

  require_once __DIR__ . '/../app/models/CalleModel.php';
  require_once __DIR__ . '/../app/models/EmpresaModel.php';

    $model2 = new EmpresaModel();
    $calles = $model2->getAllEmpresas();

    foreach ($calles as $calle) {
        echo $calle['nombre'] . '<br>';
    }
    // insert
    //$nombre_empresa = "galo entreteinment";
    //$resultado = $model2->insertEmpresa($nombre_empresa);

    // delete
    //$resultado2 = $model2->deleteEmpresa(4);
    $resultado = $model2->getEmpresa(8);
    echo $resultado['nombre']. '<br> <br>';
      
    $id_a_eliminar = 8; 
      $nombre_empresa = "nueva calle";
      $resultado = $model2->updateEmpresa($id_a_eliminar,$nombre_empresa);

      if ($resultado == true) {
          echo "Calle eliminada correctamente.";
      } else {
          echo "No se encontró ninguna calle con ese ID.";
    }
?>