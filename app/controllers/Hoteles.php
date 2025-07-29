<?php
/**
 * Controlador para manejar las operaciones relacionadas con los hoteles.
 */
class Hoteles extends Control
{
    private HotelesModel $model;

    public function __construct()
    {
        $this->model = $this->load_model('HotelesModel');
    }

    // Listar todos los hoteles.
    public function index()
    {
        $hoteles = $this->model->getAllHoteles();
        $this->load_view('hoteles/index', ['hoteles' => $hoteles]);
    }

    // Mostrar un hotel específico.
    public function show($id)
    {
        $hotel = $this->model->getHotel($id);
        if (!$hotel) {
            $hoteles = $this->model->getAllHoteles();
            $this->load_view('hoteles/index', [
                'error' => 'Hotel no encontrado.',
                'hoteles' => $hoteles
            ]);
            return;
        }
        $this->load_view('hoteles/show', ['hotel' => $hotel]);
    }

    // Mostrar formulario para crear hotel.
    public function create()
    {
        $this->load_view('hoteles/create');
    }

    // Procesar creación de hotel.
    public function store()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if ($nombre === '' || $direccion === '') {
            $this->load_view('hoteles/create', [
                'error' => 'El nombre y la dirección son obligatorios.',
                'nombre' => $nombre,
                'direccion' => $direccion
            ]);
            return;
        }

        $this->model->insertHotel($nombre, $direccion);

        $hoteles = $this->model->getAllHoteles();
        $this->load_view('hoteles/index', [
            'message' => 'Hotel creado exitosamente.',
            'hoteles' => $hoteles
        ]);
    }

    // Mostrar formulario para editar hotel.
    public function edit($id)
    {
        $hotel = $this->model->getHotel($id);
        if (!$hotel) {
            $hoteles = $this->model->getAllHoteles();
            $this->load_view('hoteles/index', [
                'error' => 'Hotel no encontrado.',
                'hoteles' => $hoteles
            ]);
            return;
        }
        $this->load_view('hoteles/edit', ['hotel' => $hotel]);
    }

    // Procesar actualización de hotel.
    public function update($id)
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if ($nombre === '' || $direccion === '') {
            $hotel = $this->model->getHotel($id);
            $this->load_view('hoteles/edit', [
                'error' => 'El nombre y la dirección no pueden estar vacíos.',
                'hotel' => $hotel
            ]);
            return;
        }

        $actualizado = $this->model->updateHotel($id, $nombre, $direccion);

        $hoteles = $this->model->getAllHoteles();

        if (!$actualizado) {
            $this->load_view('hoteles/index', [
                'error' => 'No se pudo actualizar el hotel o no hubo cambios.',
                'hoteles' => $hoteles
            ]);
            return;
        }

        $this->load_view('hoteles/index', [
            'message' => 'Hotel actualizado correctamente.',
            'hoteles' => $hoteles
        ]);
    }

    // Eliminar hotel.
    public function delete($id)
    {
        $eliminado = $this->model->deleteHotel($id);
        $hoteles = $this->model->getAllHoteles();

        if (!$eliminado) {
            $this->load_view('hoteles/index', [
                'error' => 'No se pudo eliminar el hotel o no existe.',
                'hoteles' => $hoteles
            ]);
            return;
        }

        $this->load_view('hoteles/index', [
            'message' => 'Hotel eliminado correctamente.',
            'hoteles' => $hoteles
        ]);
    }
}
