<?php
require_once __DIR__ . '/../helpers/DerechosUsuariosHelper.php';
require_once __DIR__ . '/../helpers/logHelper.php';
class Auth extends Control
{
    /**
     * Controlador para manejar la autenticación de usuarios.
     */
    public function login()
    {
        $datos = ["title" => "Login"];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['user'];
            $password = trim($_POST['password']);
            // $remember = isset($_POST['remember']);

            if (empty($user) || empty($password)) {
                $datos['error'] = 'Debe ingresar usuario y contraseña';
                writeLog("❌ Error: En inicio de sesión - ".$datos['error']);
                $this->load_view('login', $datos, false);
                exit;
            }

            $usuarioModel = $this->load_model('UsuariosModel');
            $usuario = $usuarioModel->getUsuarioByNombreUsuario($user);

            if ($usuario && password_verify($password, $usuario['contrasenia'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    
                    session_start();
                }
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_apellido'] = $usuario['apellido'];
                $_SESSION['usuario_tipo'] = $usuario['id_tipo_usuario'];
                $_SESSION['usuario_derechos'] = DerechosUsuariosHelper::getDerechos($usuario['id_tipo_usuario']);

                // if ($remember) {
                //     $this->createRememberMeToken($usuario['id_usuario']); 
                // }

                header("Location: " . URL . "/inicio");
                exit;
            } else {
                $datos['error'] = 'Credenciales incorrectas';
                writeLog("❌ Error: En inicio de sesión - ".$datos['error']);
                $this->load_view('login', $datos, false);
            }
        } else {
            if (isset($_SESSION['usuario_id'])) {
                header("Location: " . URL . "/inicio");
          } else {
                $this->load_view('login', $datos, false);
          }
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // $idUsuario = null;
        // $token = null;

        // if(!empty($_SESSION['usuario_id'])) {
        //     $idUsuario = $_SESSION['usuario_id'];
        // } 
        // elseif (!empty($_COOKIE['id_usuario'])) {
        //     $idUsuario = $_COOKIE['id_usuario'];
        // }

        // if (!empty($_COOKIE['remember_token'])) {
        //     $token = $_COOKIE['remember_token'];
        // }

        // if ($idUsuario && $token) {
        // $tokenModel = $this->load_model('RememberTokensModel');
        // $tokenModel->deleteRememberMeToken($idUsuario, $token); 
    // }

        // // Eliminar cookies del navegador
        // setcookie('remember_token', '', time() - 3600, '/');
        // setcookie('id_usuario', '', time() - 3600, '/');

        // Destruir sesión si está activa
        $_SESSION = [];
        session_destroy();

        header("Location: " . URL . "/auth/login");
        exit;
    }
    
}
