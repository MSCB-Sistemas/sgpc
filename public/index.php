<?php

  require_once __DIR__ . '/../app/controllers/CalleController.php';

$controller = new CalleController();

$action = $_GET['action'] ?? '';

require_once __DIR__ . '/../app/controllers/CalleController.php';

$controller = new CalleController();
$action = $_GET['action'] ?? 'index';
$id_calle = $_GET['id_calle'] ?? null;

switch ($action) {
    case 'index':
        $controller->index();
        break;

    case 'show':
        if ($id_calle) {
            $controller->show($id_calle);
        } else {
            echo "ID requerido";
        }
        break;

    case 'create':
        $controller->create();
        break;

    case 'store':
        $controller->store();
        break;

    case 'edit':
        if ($id_calle) {
            $controller->edit($id_calle);
        } else {
            echo "ID requerido";
        }
        break;

    case 'update':
        if ($id_calle) {
            $controller->update($id_calle);
        } else {
            echo "ID requerido";
        }
        break;

    case 'delete':
        if ($id_calle) {
            $controller->delete($id_calle);
        } else {
            echo "ID requerido";
        }
        break;

    default:
        echo "accion no reconocida";
        break;
 }

include __DIR__ . '/../app/views/CalleView.php';


?>