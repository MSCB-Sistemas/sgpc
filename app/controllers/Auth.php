<?php
class Auth extends Control
{
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
                session_start();
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
                $this->load_view('login', $datos,false);
            }
        } else {
            $this->checkRememberMeToken();
            $this->load_view('login',$datos, false);
        }
    }

    public function logout()
    {
        session_start();

        if (!isset($_SESSION['usuario_id']) && isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
            $tokenModel = $this->load_model('RememberTokensModel');
            $tokenModel->deleteRememberMeToken($_SESSION['usuario_id']);
            
            setcookie('remember_token', '', time() - 3600, '/');
            setcookie('id_usuario', '', time() - 3600, '/');
        }

        session_destroy();
        header("Location: " . URL . "/auth/login");
        exit;
    }

    private function createRememberMeToken($id_usuario)
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + 60 * 60 * 24 * 30;

        $tokenModel = $this->load_model('RememberTokensModel');
        $tokenModel->insertRememberMeToken($id_usuario, $token, $expiry);

        setcookie('remember_token', $token, $expiry, '/', '', true, true);
        setcookie('id_usuario', $id_usuario, $expiry, '/', '', true, true);
    }

    private function checkRememberMeToken()
    {
        if (basename($_SERVER['PHP_SELF']) === 'login.php') {
            $hasSession = isset($_SESSION['id_usuario']);
            $hasTokenCookie = isset($_COOKIE['remember_token']);
            $hasIdUsuarioCookie = isset($_COOKIE['id_usuario']);

            if (!$hasSession && $hasTokenCookie && $hasIdUsuarioCookie) {
                $token = $_COOKIE['remember_token'];
                $id_usuario = $_COOKIE['id_usuario'];

                $tokenModel = $this->load_model('RememberTokensModel');
                $token = $tokenModel->validateRememberMeToken($id_usuario, $token);

                if ($usuario) {
                    session_start();
                    $_SESSION['usuario_id'] = $usuario['id_usuario'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_apellido'] = $usuario['apellido'];
                    $_SESSION['usuario_tipo'] = $usuario['id_tipo_usuario'];

                    $this->createRememberMeToken($id_usuario);
                    header("Location: " . URL . "/views/inicio");
                    exit;
                }
            }
        }
    }
}
