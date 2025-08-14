<?php
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
                die(" Error de conexión: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}

?>
