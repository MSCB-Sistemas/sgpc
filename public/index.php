<?php
  require_once '../app/init.php';
  $init = new Core;

  require_once __DIR__ . '/../app/models/UsuariosModel.php';

    $model = new UsuariosModel();

    $usuarios = $model->getAllUsuarios();

    foreach ($usuarios as $usuario) {
        echo $usuario['id_usuario'] . ' ' . $usuario['nombre'] . ' ' . $usuario['apellido'] . '<br>' . 
             $usuario['cargo'] . ' ' . $usuario['sector'] . ' ' . $usuario['contrasenia'] . '<br>' . 
             $usuario['id_tipo_usuario'] . ' ' . $usuario['activo'] . '<br>' ;
    }
?>