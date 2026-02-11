<?php
require_once "config.php";

class GestorBBDD {
	//Única instancia de la clase
	//El ? significa que puede ser de tipo GestorBBDD o null
    private static ?GestorBBDD $instance = null;
    private ?PDO $connection = null;

    // Constructor privado para evitar instancias directas
    private function __construct() {
        try {
            $this->connection = new PDO(DSN, DB_USER, DB_PASS);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // Obtenemos la instancia única de GestorBBDD
	// Con :GestorBBDD forzamos a que devuelva una instancia PDO
    public static function getInstance(): GestorBBDD {
        if (self::$instance === null) {
			//Si no existía la instancia, la creamos
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Obtenemos la conexión PDO
	// Con :PDO forzamos a que devuelva una instancia PDO
    public function getConnection(): PDO {
        return $this->connection;
    }
}