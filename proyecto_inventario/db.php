<?php
// db.php - conexión PDO (ajusta credenciales)
$host = 'localhost';
$db   = 'inventario_db';
$user = 'root';
$pass = 'Admin123';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Protección de datos: No mostrar detalles técnicos del error
    error_log("Error de BD: " . $e->getMessage()); // Guardar en log del servidor
    die("Lo sentimos, hubo un problema de conexión con la base de datos. Por favor intente más tarde.");
}
?>
