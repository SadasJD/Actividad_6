<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
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
