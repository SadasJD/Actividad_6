<?php
include 'db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        attempts INT DEFAULT 0,
        last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_ip (ip_address)
    )");
    echo "Tabla de intentos de login creada o ya existe.";
} catch (PDOException $e) {
    die("Error al crear tabla: " . $e->getMessage());
}
?>