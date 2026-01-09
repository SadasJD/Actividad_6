<?php
require_once 'seguridad_global.php';
include_once __DIR__ . '/../control_acceso.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Control de inactividad
$tiempo_inactividad = 3600; // 1 hora por defecto para scripts de acción

if (isset($_SESSION['ultimo_acceso'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultimo_acceso'];
    if ($tiempo_transcurrido > $tiempo_inactividad) {
        session_unset();
        session_destroy();
        header("Location: login.php?error=inactividad");
        exit;
    }
}
$_SESSION['ultimo_acceso'] = time();

verificar_acceso($_SESSION['usuario_rol']);
?>