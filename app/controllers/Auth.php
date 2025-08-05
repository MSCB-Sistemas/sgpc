<?php
class Auth extends Control
{
    /**
     * Controlador para manejar la autenticación de usuarios.
     */
    public function login()
    {
        $datos = ["title" => "Login"];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['user'] ?? '';
            $password = trim($_POST['password'] ?? '');
            $remember = isset($_POST['remember']) ? true : false;

            if (empty($user) || empty($password)) {
                $datos['error'] = 'Debe ingresar usuario y contraseña';
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

                if ($remember) {
                    $this->createRememberMeToken($usuario['id_usuario']); 
                }

                header("Location: " . URL . "/views/inicio");
                exit;
            } else {
                $datos['error'] = 'Credenciales incorrectas';
                $this->load_view('login', $datos, false);
            }
        } else {
            $this->checkRememberMeToken();
            $this->load_view('login', $datos, false);
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['usuario_id'] ?? $_COOKIE['id_usuario'] ?? null;

        if ($idUsuario) {
            $tokenModel = $this->load_model('RememberTokensModel');
            $tokenModel->deleteRememberMeToken($idUsuario);
        }

        // Eliminar cookies del navegador
        setcookie('remember_token', '', time() - 3600, '/');
        setcookie('id_usuario', '', time() - 3600, '/');

        // Destruir sesión si está activa
        $_SESSION = [];
        session_destroy();

        header("Location: " . URL . "/auth/login");
        exit;
    }


    
    
}
