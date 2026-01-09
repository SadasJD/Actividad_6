<?php
require_once 'includes/verificar_sesion.php';
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: productos.php");
exit;
?>