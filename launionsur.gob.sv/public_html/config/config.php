<?php
// Cargar el autoload de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar las variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Obtener las variables de entorno de forma segura
$host    = $_ENV['DB_HOST'] ?? 'localhost';
$dbname  = $_ENV['DB_NAME'] ?? 'Dev_a1c4lu5v_alcaldia';
$user    = $_ENV['DB_USER'] ?? 'Dev_sistma';
$pass    = $_ENV['DB_PASS'] ?? '8XcLkLJ^b4HpE#d2Sa6!hj';
$port = (int) ($_ENV['DB_PORT'] ?? 3306);
$charset = 'utf8mb4';

// Configurar el DSN de MySQL
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

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
