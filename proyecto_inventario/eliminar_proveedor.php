<?php
require_once 'includes/verificar_sesion.php';
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM proveedores WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: proveedores.php");
exit;
?>