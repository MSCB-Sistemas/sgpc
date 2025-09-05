<?php
require_once __DIR__ . '/../helpers/logHelper.php';
//Conexion de base de datos
class Database   {
    private static PDO $connection;

    public static function connect(): PDO {
        if (!isset(self::$connection)) {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
                self::$connection = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                $_SESSION['error_inicio'] = "Error de conexión a la base de datos.";
                writeLog("❌ Error de conexión a la base de datos: " . $e->getMessage());
                header("Location: " . URL);
                exit;
            }
        }

        return self::$connection;
    }
}

?>
