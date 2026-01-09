<?php
// Incluir seguridad antes que nada
require_once 'includes/seguridad_global.php';

include 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar CSRF antes de procesar
    verificar_csrf();

    $cedula = $_POST['cedula'];
    $clave_ingresada = $_POST['clave'];

    $stmt = $pdo->prepare("
        SELECT u.*, r.nombre as rol_nombre 
        FROM usuarios u 
        LEFT JOIN roles r ON u.rol_id = r.id 
        WHERE u.cedula = ?
    ");
    $stmt->execute([$cedula]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($clave_ingresada, $usuario['clave'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombres'];
        $_SESSION['usuario_apellido'] = $usuario['apellidos'];
        $_SESSION['usuario_rol'] = $usuario['rol_nombre']; // Guardar el nombre del rol
        $_SESSION['ultimo_acceso'] = time(); // Inicializar tiempo de acceso
        
        header("Location: principal.php");
        exit;
    } else {
        $error = 'Cédula o clave incorrecta.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Extravagante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="text-center mb-4">Acceso al Sistema</h2>
            <form action="login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula de Identidad" required>
                    <label for="cedula">Cédula de Identidad</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="clave" name="clave" placeholder="Clave" required>
                    <label for="clave">Clave</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
            <div class="mt-3 text-center">
                <a href="recuperar.php">¿Olvidaste tu clave?</a> | <a href="registro.php">Registrarse</a>
            </div>
        </div>
    </div>
</body>
</html>