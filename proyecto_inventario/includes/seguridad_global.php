<?php
// Evitar acceso directo al archivo
if (count(get_included_files()) == 1) exit("Acceso directo no permitido");

// 1. Configuración segura de Cookies de Sesión 
// (Deben configurarse ANTES de session_start)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); // No accesible vía JS
    ini_set('session.use_only_cookies', 1); // Evitar ID en URL
    ini_set('session.cookie_samesite', 'Lax'); // Protección CSRF recomendada compatible
    
    // Si tienes HTTPS activado, descomenta la siguiente línea:
    // ini_set('session.cookie_secure', 1); 
    
    session_start();
}

// 2. Cabeceras de Seguridad HTTP (Security Headers)
header("X-Frame-Options: SAMEORIGIN"); // Previene Clickjacking (iFrames)
header("X-XSS-Protection: 1; mode=block"); // filtro XSS del navegador
header("X-Content-Type-Options: nosniff"); // Evita MIME-sniffing
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https:;"); // CSP Permitir CDNs comunes

// 3. Protección CSRF (Cross-Site Request Forgery)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 4. Función para verificar CSRF en peticiones POST
function verificar_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Error de Seguridad: Token CSRF inválido. La solicitud ha sido rechazada.");
        }
    }
}

// 5. Protección de Datos Global (Data Loss Prevention & Error Handling)
// Deshabilitar visualización de errores para evitar fugas de información (SQL, rutas, etc.)
error_reporting(0); 
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Función personalizada de manejo de errores para no revelar paths o consultas SQL
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log del error internamente pero no mostrar nada al usuario
    error_log("Error [$errno] en $errfile:$errline: $errstr");
    if (!(error_reporting() & $errno)) return false;
    
    // Mostrar un mensaje genérico si el error es grave
    if ($errno == E_USER_ERROR || $errno == E_ERROR) {
        die("Ha ocurrido un error interno de seguridad. Por favor, contacte con el administrador.");
    }
    return true; // Prevenir handler nativo de PHP que podría mostrar información
});

// Limpieza de datos de entrada global (Prevención XSS básica en $_GET/$_POST)
// NOTA: Idealmente usar htmlspecialchars() al mostrar, no al guardar, 
// pero esto es una capa extra de defensa en profundidad.
function limpiar_entrada($data) {
    if (is_array($data)) {
        return array_map('limpiar_entrada', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
// Aplicar limpieza si se desea, o usar individualmente.