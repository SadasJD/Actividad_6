<?php
/**
 * Script de Verificación de Seguridad para Actividad_6
 * Comprueba configuraciones críticas y mejores prácticas.
 */

$archivos_criticos = [
    'login.php',
    'registro.php',
    'principal.php',
    'usuarios.php',
    'roles.php',
    'configurar_seguridad.php',
    'eliminar_usuario.php'
];

echo "=== VERIFICACIÓN DE SEGURIDAD ===\n";

// 1. Verificar existencia de archivos de seguridad
$seguridad_files = ['includes/seguridad_global.php', 'control_acceso.php', 'db.php'];
foreach ($seguridad_files as $f) {
    if (file_exists(__DIR__ . '/' . $f)) {
        echo "[OK] Archivo de seguridad encontrado: $f\n";
    } else {
        echo "[ERROR] Archivo de seguridad NO ENCONTRADO: $f\n";
    }
}

// 2. Verificar configuración en seguridad_global.php
$seg_content = file_get_contents(__DIR__ . '/includes/seguridad_global.php');
if (strpos($seg_content, "ini_set('session.cookie_httponly', 1)") !== false) {
    echo "[OK] Cookies HttpOnly configuradas.\n";
} else {
    echo "[AVISO] Cookies HttpOnly NO detectadas en seguridad_global.php\n";
}

if (strpos($seg_content, "header(\"Content-Security-Policy\"") !== false) {
    echo "[OK] Cabecera CSP configurada.\n";
}

// 3. Verificar que los archivos críticos incluyen protección y CSRF
foreach ($archivos_criticos as $archivo) {
    $content = file_get_contents(__DIR__ . '/' . $archivo);
    echo "Analizando $archivo:\n";
    
    $protegido = (strpos($content, "header.php") !== false || strpos($content, "seguridad_global.php") !== false || strpos($content, "verificar_sesion.php") !== false);
    echo "  - Inclusión de seguridad: " . ($protegido ? "[OK]" : "[ERROR]") . "\n";
    
    if (strpos($content, "method=\"post\"") !== false || strpos($content, "method='post'") !== false) {
        $csrf_input = (strpos($content, "name=\"csrf_token\"") !== false || strpos($content, "name='csrf_token'") !== false);
        $csrf_verify = (strpos($content, "verificar_csrf()") !== false);
        echo "  - Token CSRF en form: " . ($csrf_input ? "[OK]" : "[ERROR]") . "\n";
        echo "  - Verificación CSRF en PHP: " . ($csrf_verify ? "[OK]" : "[ERROR]") . "\n";
    }
}

// 4. Verificar uso de password_hash
$registro_content = file_get_contents(__DIR__ . '/registro.php');
if (strpos($registro_content, "password_hash") !== false) {
    echo "[OK] registro.php usa password_hash.\n";
} else {
    echo "[ERROR] registro.php NO usa hashing de contraseñas.\n";
}

// 5. Verificar protección contra acceso directo en db.php (opcional pero bueno)
$db_content = file_get_contents(__DIR__ . '/db.php');
if (strpos($db_content, "display_errors") === false && strpos($db_content, "PDO::ATTR_ERRMODE") !== false) {
    echo "[OK] db.php configurado con PDO y sin exposición de errores directos.\n";
}

echo "=== FIN DE LA VERIFICACIÓN ===\n";
?>