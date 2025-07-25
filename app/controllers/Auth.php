<?php
class Auth extends Control
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['user'] ?? '';
            $password = trim($_POST['password'] ?? '');

            if (empty($user) || empty($password)) {
                echo "Usuario y contraseña requeridos.";
                exit;
            }

            $usuarioModel = $this->load_model('UsuariosModel');
            $usuario = $usuarioModel->getUsuarioByNombreUsuario($user);

            if ($usuario && password_verify($password, $usuario['contrasenia'])) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_tipo'] = $usuario['id_tipo_usuario'];
                $this->load_view("inicio");
                exit;
            } else {
                $this->load_view('login', ['error' => 'Credenciales incorrectas']);
            }
        } else {
            $this->load_view('login');
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header("Location: " . URL . "/auth/login");
        exit;
    }
}
