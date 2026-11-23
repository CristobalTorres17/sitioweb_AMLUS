<?php
// Cargar el autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar las variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Obtener las variables de entorno de forma segura
$host    = $_ENV['DB_HOST'] ?? 'localhost';
$dbname  = $_ENV['DB_NAME'] ?? 'nombre_por_defecto';
$user    = $_ENV['DB_USER'] ?? 'root';
$pass    = $_ENV['DB_PASS'] ?? '';
$charset = 'utf8mb4';

// Configurar el DSN de MySQL
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

// Opciones seguras para PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activa excepciones para errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Modo de obtención asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa sentencias preparadas nativas
];

try {
    // Crear una nueva instancia de PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Registrar el error y mostrar un mensaje genérico al usuario
    error_log('Error de conexión: ' . $e->getMessage());
    die('Error al conectar a la base de datos.');
}
?>
