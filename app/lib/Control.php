<?php

require_once __DIR__ . '/../helpers/DerechosUsuariosHelper.php';

require_once __DIR__ . '/../helpers/logHelper.php';
class Control
{

    /**
     * Constructor de la clase Control.
     * Inicializa el sistema y verifica el token de "Recuérdame".
     */
    public function __construct()
    {
        $this->checkRememberMeToken(); // Verifica el token al instanciar cualquier controlador
    }

    /**
     * Carga un modelo específico.
     *
     * @param string $model Nombre del modelo a cargar.
     * @return object Instancia del modelo cargado.
     */
    public function load_model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model;
    }

    /**
     * Carga una vista y opcionalmente un layout.
     *
     * @param string $view Nombre de la vista a cargar.
     * @param array $datos Datos a pasar a la vista.
     * @param string $layout Nombre del layout (por defecto 'main').
     */
    public function load_view($view, $datos = [], $layout = 'main')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $viewFile = APP . '/views/pages/' . $view . '.php';

        if (file_exists($viewFile)) {
            if ($layout) {
                $viewPath = $viewFile;
                require_once APP . "/views/layout/{$layout}.php";
            } else {
                require_once $viewFile;
            }
        } else {
            $_SESSION['error_inicio'] = "Error: La vista {$viewFile} no existe.";
            writeLog("❌ Error: La vista {$viewFile} no existe.");
            header("Location: " . URL);
            exit;
        }
    }

    /**
     * Comprobar si el usuario está autenticado.
     */
    protected function checkRememberMeToken()
    {
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        // }

        // if (!isset($_SESSION['usuario_id']) &&
        //     isset($_COOKIE['remember_token']) &&
        //     isset($_COOKIE['id_usuario'])) {

        //     $token = $_COOKIE['remember_token'];
        //     $usuarioId = $_COOKIE['id_usuario'];

        //     $tokenModel = $this->load_model('RememberTokensModel');
        //     $usuarioData = $tokenModel->validateRememberMeToken($usuarioId, $token);

        //     if ($usuarioData) {
        //         $_SESSION['usuario_id'] = $usuarioData['id_usuario'];
        //         $_SESSION['usuario_nombre'] = $usuarioData['nombre'];
        //         $_SESSION['usuario_apellido'] = $usuarioData['apellido'];
        //         $_SESSION['usuario_tipo'] = $usuarioData['id_tipo_usuario'];
        //         $_SESSION['usuario_derechos'] = DerechosUsuariosHelper::getDerechos($usuarioData['id_tipo_usuario']);

        //         $this->createRememberMeToken($usuarioData['id_usuario']); // renovar token

        //         header("Location: " . URL . "/inicio");
        //         exit;
        //     }
        // }
    }

    /**
     * 
     * Crea un token de "Recuérdame" para el usuario.
     * @param mixed $id_usuario
     * @return void
     */
    protected function createRememberMeToken($id_usuario)
    {
        // $token = bin2hex(random_bytes(32));
        // $expiry = time() + 60 * 60 * 24 * 30;

        // $tokenModel = $this->load_model('RememberTokensModel');
        // $tokenModel->insertRememberMeToken($id_usuario, $token, $expiry);

        // $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        // setcookie('remember_token', $token, $expiry, '/', '', $secure, true);
        // setcookie('id_usuario', $id_usuario, $expiry, '/', '', $secure, true);
    }

    //**
    // Función para requerir autenticación en controladores específicos
    // Si el usuario no está autenticado, redirige al login
    //  */
    protected function requireLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario_id'])) {
            header("Location: " . URL . "/auth/login");
            exit;
        }
    }

    protected function tienePermiso($permiso){
        return (in_array($permiso,$_SESSION['usuario_derechos']));
    }
}

?>
