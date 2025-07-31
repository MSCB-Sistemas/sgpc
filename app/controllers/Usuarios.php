<?php
/**
 * Controlador para gestionar las operaciones relacionadas con los usuarios.
 */
class Usuarios extends Control
{
    private UsuariosModel $model;
    private TiposUsuariosModel $modelTipoUsuario;

    public function __construct()
    {
        $this->modelTipoUsuario = $this->load_model('TiposUsuariosModel');
        $this->model = $this->load_model('UsuariosModel');
    }

    // Mostrar lista de usuarios activos
    public function index()
    {
        $usuarios = $this->model->getAllUsuarios();
        $datos = [
        'title' => 'Listado de Usuarios',
        'urlCrear' => URL . '/usuarios/create',
        'columnas' => ['Usuario', 'Nombre', 'Apellido', 'Cargo', 'Sector', 'Tipo', 'Activo'],
        'columnas_claves' => ['usuario', 'nombre', 'apellido', 'cargo', 'sector', 'tipo_usuario', 'activo'],
        'data' => $usuarios,
        'acciones' => function($fila) {
            $id = $fila['id_usuario'];
            $url = URL . '/usuarios';
            return '
                <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-outline-primary">Editar</a>
                <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Desactivar este usuario?\');">Desactivar</a>
                <a href="'.$url.'/activate/'.$id.'" class="btn btn-sm btn-outline-success" onclick="return confirm(\'¿Activar este usuario?\');">Activar</a>
            ';
        
        }
        ];
    $this->load_view('partials/tablaAbm', $datos);
    }
    
    public function edit($id)
    {
        $usuario = $this->model->getUsuario(id_usuario: $id);  
        $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();

        if (!$usuario) {
            die("Usuario no encontrado");
        }

        $this->load_view('usuarios/form', [
            'title' => 'Editar usuario',
            'action' => URL . '/usuarios/update/' . $id,
            'values' => [
                'usuario' => $usuario['usuario'],
                'nombre' => $usuario['nombre'],
                'apellido' => $usuario['apellido'],
                'cargo' => $usuario['cargo'],
                'sector' => $usuario['sector'],
                'contrasenia' => $usuario['contrasenia'],
                'id_tipo_usuario' => $usuario['id_tipo_usuario']
            ],
            'errores' => [],
            'tipos' => $tipos
        ]);
    }

    public function update($id)
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $usuario = trim($_POST["usuario"] ?? '');
            $nombre = trim($_POST["nombre"] ?? '');
            $apellido = trim($_POST["apellido"] ?? '');
            $cargo = trim($_POST["cargo"] ?? '');
            $sector = trim($_POST["sector"] ?? '');
            $contrasenia = trim($_POST["password"] ?? '');
            $tipoUsuario = $_POST["tipo_usuario"] ?? '';

            $errores = [];
            if (empty($usuario)) $errores[] = "El usuario es obligatorio.";
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
            if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
            if (empty($contrasenia)) $errores[] = "El nombre es obligatorio.";
            if (empty($tipoUsuario)) $errores[] = "Debe seleccionar un tipo de usuario.";

            if (!empty($errores)) {
                $usuario = [
                    'id_usuario' => $id,
                    'usuario' => $usuario,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'cargo' => $cargo,
                    'sector' => $sector,
                    'contrasenia'=> $contrasenia,
                    'id_tipo_usuario'=> $tipoUsuario
                ];
                $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();
                $this->load_view('usuarios/form', [
                    'title' => 'Editar usuario',
                    'action' => URL . '/usuarios/update/' . $id,
                    'values' => $usuario,
                    'errores' => $errores,
                    'tipos' => $tipos
                ]);
                return;
            }
            $contrasenia = password_hash($contrasenia, PASSWORD_DEFAULT);

            if ($this->model->updateUsuario($id, $usuario, $nombre, $apellido, $cargo, $sector, $contrasenia, $tipoUsuario)) {
                header("Location: " . URL . "/usuarios");
                exit;
            } else {
                die("Error al actualizar el usuario");
            }
        }
    }

    public function create()
    {
        $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();
        $this->load_view('usuarios/form', [
            'title' => 'Crear nuevo usuario',
            'action' => URL . '/usuarios/save',
            'values' => [],
            'errores' => [],
            'tipos' => $tipos
        ]);
    }
    
    public function save()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $usuario = trim($_POST["usuario"] ?? '');
            $nombre = trim($_POST["nombre"] ?? '');
            $apellido = trim($_POST["apellido"] ?? '');
            $cargo = trim($_POST["cargo"] ?? '');
            $sector = trim($_POST["sector"] ?? '');
            $contrasenia = trim($_POST["password"] ?? '');
            $tipoUsuario = $_POST["tipo_usuario"] ?? '';

            // Validaciones simples
            $errores = [];
            if (empty($usuario)) $errores[] = "El usuario es obligatorio.";
            if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
            if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
            if (empty($contrasenia)) $errores[] = "El nombre es obligatorio.";
            if (empty($tipoUsuario)) $errores[] = "Debe seleccionar un tipo de usuario.";

            if (!empty($errores)) {
                $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();
                $this->load_view('usuarios/form', [
                    'title' => 'Crear nuevo usuario',
                    'action' => URL . '/usuarios/save',
                    'values' => $_POST,
                    'errores' => $errores,
                    'tipos' => $tipos
                ]);
                return;
            }
            $contrasenia = password_hash($contrasenia, PASSWORD_DEFAULT);

            if ($this->model->insertUsuario($usuario, $nombre, $apellido, $cargo, $sector, $contrasenia, $tipoUsuario)) {
                header("Location: " . URL . "/usuarios");
                exit;
            } else {
                die("Error al guardar el usuario");
            }
        }
    }

    public function delete($id){
        if($this->model->deleteUsuario($id)) {
            header(header: "Location: " . URL . "/usuarios");
            exit;
        } else {
            die("No se puedo eliminar al usuario.");
        }
    }
    public function activate($id){
        if($this->model->activateUsuario($id)) {
            header(header: "Location: " . URL . "/usuarios");
            exit;
        } else {
            die("No se puedo activar al usuario.");
        }
    }
}
