<?php
// Incluir Seguridad Global (Headers, Session hardening)
require_once 'includes/seguridad_global.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Control de inactividad (10 minutos)
$tiempo_inactividad = 600; 

if (isset($_SESSION['ultimo_acceso'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
    if ($tiempo_transcurrido > $tiempo_inactividad) {
        session_unset();
        session_destroy();
        header("Location: login.php?error=inactividad");
        exit;
    }
}
$_SESSION['ultimo_acceso'] = time(); // Actualizar tiempo de acceso

include 'control_acceso.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
verificar_acceso($_SESSION['usuario_rol']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Script de Seguridad Cliente (Anti-Inspección) -->
    <script src="assets/js/seguridad_cliente.js"></script>
    <script>
        // Redirección automática por inactividad en el cliente
        // Esto asegura que la sesión se cierre visualmente sin necesidad de recargar
        setTimeout(function() {
            window.location.href = 'logout.php?reason=inactivity';
        }, <?= $tiempo_inactividad * 1000 ?>);
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="principal.php">
        <i class="fas fa-boxes-stacked"></i>
        Inventario
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="navbar-text me-3">
            <i class="fas fa-user"></i>
            <?php 
                if (isset($_SESSION["usuario_nombre"]) && isset($_SESSION["usuario_apellido"])) {
                    echo htmlspecialchars($_SESSION["usuario_nombre"] . ' ' . $_SESSION["usuario_apellido"]);
                }
            ?>
            <strong>(<?php if (isset($_SESSION["usuario_rol"])) {
                echo htmlspecialchars($_SESSION["usuario_rol"]);
            } ?>)</strong>
          </span>
        </li>
        <li class="nav-item">
          <a class="btn btn-outline-danger" href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Salir
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container main-container">
