<?php
/**
 * Controlador para gestionar las operaciones relacionadas con los usuarios.
 */
class Usuarios extends Control
{
    private UsuariosModel $model;

    public function __construct()
    {
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
                <a href="'.$url.'/delete/'.$id.'" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'¿Eliminar este usuario?\');">Eliminar</a>
            ';
        
        }
        ];
    $this->load_view('partials/tablaAbm', $datos);
    }

    // Mostrar detalles de un usuario específico
    public function show($id)
    {
        $usuario = $this->model->getUsuario($id);
        if (!$usuario) {
            $this->load_view('usuarios/index', [
                'error' => 'Usuario no encontrado.',
                'usuarios' => $this->model->getAllUsuarios()
            ]);
            return;
        }
        $this->load_view('usuarios/show', ['usuario' => $usuario]);
    }

    // Formulario para crear usuario
    public function create()
    {
        $this->load_view('usuarios/create');
    }

    // Procesar creación
    public function store()
    {
        $usuario = trim($_POST['usuario'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $sector = trim($_POST['sector'] ?? '');
        $contrasenia = trim($_POST['contrasenia'] ?? '');
        $id_tipo_usuario = intval($_POST['id_tipo_usuario'] ?? 0);

        // Validar campos obligatorios
        if ($usuario === '' || $nombre === '' || $apellido === '' || $contrasenia === '' || $id_tipo_usuario === 0) {
            $this->load_view('usuarios/create', [
                'error' => 'Por favor, complete todos los campos obligatorios.',
                'data' => $_POST
            ]);
            return;
        }

        // Verificar que no exista usuario con ese nombre
        $existe = $this->model->getUsuarioByNombreUsuario($usuario);
        if ($existe) {
            $this->load_view('usuarios/create', [
                'error' => 'El nombre de usuario ya existe.',
                'data' => $_POST
            ]);
            return;
        }

        // Modulo para hashear la contraseña, actualmente esta en el modo default de php (para modificar el hash "PASSWORD_DEFAULT")
        $hashContrasenia = password_hash($contrasenia, PASSWORD_DEFAULT);

        $this->model->insertUsuario($usuario, $nombre, $apellido,
         $cargo, $sector, $hashContrasenia, $id_tipo_usuario);

        $this->load_view('usuarios/index', [
            'message' => 'Usuario creado correctamente.',
            'usuarios' => $this->model->getAllUsuarios()
        ]);
    }

    // Formulario para editar usuario
    public function edit($id)
    {
        $usuario = $this->model->getUsuario($id);
        if (!$usuario) {
            $this->load_view('usuarios/index', [
                'error' => 'Usuario no encontrado.',
                'usuarios' => $this->model->getAllUsuarios()
            ]);
            return;
        }
        $this->load_view('usuarios/edit', ['usuario' => $usuario]);
    }

    // Procesar actualización
    public function update($id)
    {
        $usuario = trim($_POST['usuario'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $sector = trim($_POST['sector'] ?? '');
        $contrasenia = trim($_POST['contrasenia'] ?? '');
        $id_tipo_usuario = intval($_POST['id_tipo_usuario'] ?? 0);
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($usuario === '' || $nombre === '' || $apellido === '' || $id_tipo_usuario === 0) {
            $usuarioData = $this->model->getUsuario($id);
            $this->load_view('usuarios/edit', [
                'error' => 'Por favor, complete todos los campos obligatorios.',
                'usuario' => $usuarioData
            ]);
            return;
        }

        // Si el campo contraseña está vacío, mantener la actual
        $usuarioExistente = $this->model->getUsuario($id);
        if (!$usuarioExistente) {
            $this->load_view('usuarios/index', [
                'error' => 'Usuario no encontrado.',
                'usuarios' => $this->model->getAllUsuarios()
            ]);
            return;
        }

        if ($contrasenia === '') {
            // Mantener contraseña antigua
            $hashContrasenia = $usuarioExistente['contrasenia'];
        } else {
            $hashContrasenia = password_hash($contrasenia, PASSWORD_DEFAULT);
        }

        $this->model->updateUsuario($id, $usuario, $nombre,
         $apellido, $cargo, $sector, $hashContrasenia, $id_tipo_usuario, $activo);

        $this->load_view('usuarios/index', [
            'message' => 'Usuario actualizado correctamente.',
            'usuarios' => $this->model->getAllUsuarios()
        ]);
    }

    // Desactivar usuario (activo = '0')
    public function delete($id)
    {
        $eliminado = $this->model->deleteUsuario($id);
        $usuarios = $this->model->getAllUsuarios();

        if (!$eliminado) {
            $this->load_view('usuarios/index', [
                'error' => 'No se pudo desactivar el usuario.',
                'usuarios' => $usuarios
            ]);
            return;
        }

        $this->load_view('usuarios/index', [
            'message' => 'Usuario desactivado correctamente.',
            'usuarios' => $usuarios
        ]);
    }
}
