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
        $this->requireLogin();
        $this->modelTipoUsuario = $this->load_model('TiposUsuariosModel');
        $this->model = $this->load_model('UsuariosModel');
    }

    // Mostrar lista de usuarios activos
    public function index()
    {
        if (in_array('editar usuarios',$_SESSION['usuario_derechos'])){
            if (isset($_SESSION['error_usuarios'])) {
                $errores[] = $_SESSION['error_usuarios'];
                unset($_SESSION['error_usuarios']); // Borramos el mensaje después de usarlo
            }
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
                $botones = '';

                if ($fila['activo']) {
                    if ($this->tienePermiso('editar usuarios')) {
                        $botones .= '
                            <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a>
                            <a href="'.$url.'/changePass/'.$id.'" class="btn btn-sm btn-warning">Cambiar clave</a>';

                            if ($this->tienePermiso('eliminar usuarios') && $fila['tipo_usuario'] != 'admin') {
                            $botones .= '
                                <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Desactivar este usuario?\');">Desactivar</a>';
                            } else if ($fila['tipo_usuario'] == 'admin' && $this->tienePermiso('god')) {
                                $botones .= '
                                <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-danger" onclick="return confirm(\'¿Desactivar este usuario?\');">Desactivar</a>';
                            }
                    }
                } else {
                    if ($this->tienePermiso('editar usuarios')) {
                        $botones .= '
                            <a href="'.$url.'/edit/'.$id.'" class="btn btn-sm btn-primary">Editar</a>
                            <a href="'.$url.'/activate/'.$id.'" class="btn btn-sm btn-success" onclick="return confirm(\'¿Activar este usuario?\');">Activar</a>
                        ';
                    }
                }

                return $botones;
            }
            ];
            $this->load_view('partials/tablaAbm', $datos);
        } else {
            header("Location: " . URL);
            exit;
        }
    }
    
    public function edit($id)
    {
        if ($this->tienePermiso('editar usuarios')) {
            $usuario = $this->model->getUsuario(id_usuario: $id);  
            $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();

            if (!$usuario) {
                $_SESSION['error_usuarios'] = "Usuario no encontrado.";
                header("Location: " . URL . "/usuarios");
                exit;
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
                    'id_tipo_usuario' => $usuario['id_tipo_usuario']
                ],
                'errores' => [],
                'tipos' => $tipos,
                'update' => true
            ]);
        }
    }

    public function update($id)
    {
        if ($this->tienePermiso('editar usuarios')) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $usuario = trim($_POST["usuario"] );
                $nombre = trim($_POST["nombre"] );
                $apellido = trim($_POST["apellido"] );
                $cargo = trim($_POST["cargo"] );
                $sector = trim($_POST["sector"]  );
                $tipoUsuario = $_POST["tipo_usuario"] ;

                $errores = [];
                if (empty($usuario)) $errores[] = "El usuario es obligatorio.";
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
                if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
                if (empty($tipoUsuario)) $errores[] = "Debe seleccionar un tipo de usuario.";
                // CHECK: No se pueden asignar tipos de usuario Admin (1) ni Director (2)
                $usuarioActual = $this->model->getUsuarioById($id);
                if (!$usuarioActual) {
                    $_SESSION['error_usuarios'] = "Usuario no encontrado.";
                    
                }

                // Validar permisos especiales
                $idTipoActual = $usuarioActual['id_tipo_usuario'];
                $idTipoSesion = $tipoUsuario; // tipo del usuario que edita

                // Nadie puede editar Admin (1)
                // Director (tipo 2) solo puede editarse a sí mismo
                if ($idTipoActual == 1 || ($idTipoActual == 2 && $id != $_SESSION['id_usuario'])) {
                    $errores []= "No tiene permisos para editar este usuario.";
                    
                }
                if (!empty($errores)) {
                    $usuario = [
                        'id_usuario' => $id,
                        'usuario' => $usuario,
                        'nombre' => $nombre,
                        'apellido' => $apellido,
                        'cargo' => $cargo,
                        'sector' => $sector,
                        'id_tipo_usuario'=> $tipoUsuario
                    ];
                    $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();
                    $this->load_view('usuarios/form', [
                        'title' => 'Editar usuario',
                        'action' => URL . '/usuarios/update/' . $id,
                        'values' => $usuario,
                        'errores' => $errores,
                        'tipos' => $tipos,
                        'update' => true
                    ]);
                    return;
                }

                if ($this->model->updateUsuario($id, $usuario, $nombre, $apellido, $cargo, $sector, $tipoUsuario)) {
                    header("Location: " . URL . "/usuarios");
                    exit;
                } else {
                    $_SESSION['error_usuarios'] = "Error al actualizar el usuario.";
                    header("Location: " . URL . "/usuarios");
                    exit;
                }
            }
        }
    }

    public function create()
    {
        if ($this->tienePermiso('crear usuarios')) {
            $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();
            $this->load_view('usuarios/form', [
                'title' => 'Crear nuevo usuario',
                'action' => URL . '/usuarios/save',
                'values' => [],
                'errores' => [],
                'tipos' => $tipos,
                'update' => false
            ]);
        }
    }
    
    public function save()
    {
        if ($this->tienePermiso('crear usuarios')) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $usuario = trim($_POST["usuario"]);
                $nombre = trim($_POST["nombre"]);
                $apellido = trim($_POST["apellido"] );
                $cargo = trim($_POST["cargo"] );
                $sector = trim($_POST["sector"] );
                $contrasenia = trim($_POST["password"] );
                $tipoUsuario = $_POST["tipo_usuario"];


                // Validaciones simples
                $errores = [];
                if (empty($usuario)) $errores[] = "El usuario es obligatorio.";
                if (empty($nombre)) $errores[] = "El nombre es obligatorio.";
                if (empty($apellido)) $errores[] = "El apellido es obligatorio.";
                if (empty($contrasenia)) $errores[] = "El nombre es obligatorio.";
                if (empty($tipoUsuario)) $errores[] = "Debe seleccionar un tipo de usuario.";
                if ($tipoUsuario == 1 ) {
                    $errores[] = "No se pueden crear usuarios admin.";
                }

                if (!empty($errores)) {
                    $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();
                    $this->load_view('usuarios/form', [
                        'title' => 'Crear nuevo usuario',
                        'action' => URL . '/usuarios/save',
                        'values' => $_POST,
                        'errores' => $errores,
                        'tipos' => $tipos,
                        'update' => false
                    ]);
                    return;
                }
                
                try {
                    $contrasenia = password_hash($contrasenia, PASSWORD_DEFAULT);
                    $this->model->insertUsuario($usuario, $nombre, $apellido, $cargo, $sector, $contrasenia, $tipoUsuario);
                    header("Location: " . URL . "/usuarios");
                    exit;
                } catch (\PDOException $e) {

                    if ($e->getCode() == 23000) {
                        $errores[] = "El usuario " . $usuario . " ya existe.";
                    } else {
                        $_SESSION['error_usuarios'] = "Error al guardar el usuario.";
                        header("Location: " . URL . "/usuarios");
                        exit;
                    }
                    $tipos = $this->modelTipoUsuario->getAllTiposUsuarios();
                    $this->load_view('usuarios/form', [
                        'title' => 'Crear nuevo usuario',
                        'action' => URL . '/usuarios/save',
                        'values' => $_POST,
                        'errores' => $errores,
                        'tipos' => $tipos,
                        'update' => false
                    ]);
                }
            }
        }
    }

    public function delete($id){
        if ($this->tienePermiso('eliminar usuarios')) {
            if($this->model->deleteUsuario($id)) {
                header(header: "Location: " . URL . "/usuarios");
                exit;
            } else {
                $_SESSION['error_usuarios'] = "No se pudo eliminar al usuario.";
                header("Location: " . URL . "/usuarios");
                exit;
            }
        }
    }

    public function activate($id){
        if ($this->tienePermiso("editar usuarios")) {
            if($this->model->activateUsuario($id)) {
                header(header: "Location: " . URL . "/usuarios");
                exit;
            } else {
                $_SESSION['error_usuarios'] = "No se pudo activar al usuario.";
                header("Location: " . URL . "/usuarios");
                exit;
            }
        }
    }
    
    public function changePass($id){
        if ($this->tienePermiso("editar usuarios")) {
            $this->load_view('usuarios/formPass', [
                'title' => 'Cambiar clave',
                'action' => URL . '/usuarios/savePass/' . $id,
                'errores' => []
            ]);
        }
    }

    public function savePass($id)
    {
        if ($this->tienePermiso("editar usuarios")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $password = trim($_POST["password"] );

                $errores = [];
                if (empty($password)) $errores[] = "El campo nueva contraseña es obligatorio.";

                if (!empty($errores)) {
                    $this->load_view('usuarios/formPass', [
                        'title' => 'Cambiar clave',
                        'action' => URL . '/usuarios/savePass/' . $id,
                        'errores' => $errores,
                    ]);
                    return;
                }

                $password = password_hash($password, PASSWORD_DEFAULT);
                if ($this->model->updatePassword($id, $password)) {
                    header("Location: " . URL . "/usuarios");
                    exit;
                } else {
                    $_SESSION['error_usuarios'] = "Error al cambiar la clave.";
                    header("Location: " . URL . "/usuarios");
                    exit;
                }
            }
        }
    }
}
