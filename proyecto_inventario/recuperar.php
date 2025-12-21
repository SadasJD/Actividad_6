<?php
session_start();
include 'db.php';

$error = '';
$success = '';
$paso = 1; // 1: buscar, 2: elegir, 'preguntas', 'otp', 3: resetear
$usuario = null;

// --- Función para enviar OTP a través de UltraMSG ---
function enviar_otp_ultramsg($telefono, $otp) {
    // Asegúrate de que el número de teléfono esté en formato internacional (ej. con código de país, como 593991234567)
    $params = [
        'token' => 'nx3e1n827fu2p1da', // Token de UltraMSG
        'to' => $telefono,
        'body' => "Tu código de recuperación de contraseña es: *" . $otp . "*. Este código es válido por 5 minutos."
    ];
    $url = 'https://api.ultramsg.com/instance155810/messages/chat'; // URL de la API con Instance ID

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200) {
        $decoded_response = json_decode($response, true);
        if (isset($decoded_response['sent']) && $decoded_response['sent'] == 'true') {
            return true;
        }
    }
    // Opcional: Registrar el error para depuración
    // error_log("Error al enviar OTP a $telefono: " . $response);
    return false;
}
// --- Fin de la función de envío de OTP ---

// Paso 1: Buscar usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buscar_usuario'])) {
    $identificador = trim($_POST['identificador']);
    if (empty($identificador)) {
        $error = 'Por favor, ingresa tu cédula o correo electrónico.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE cedula = ? OR correo = ?");
        $stmt->execute([$identificador, $identificador]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            $_SESSION['recuperacion_id_usuario'] = $usuario['id'];
            $paso = 2; // Pasar a la elección de método
        } else {
            $error = 'No se encontró ningún usuario con esa cédula o correo electrónico.';
        }
    }
}

// Paso 2: Elegir método y procesar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['elegir_metodo'])) {
    $paso = 2;
    if (isset($_SESSION['recuperacion_id_usuario'])) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['recuperacion_id_usuario']]);
        $usuario = $stmt->fetch();
        $metodo = $_POST['metodo'];

        if ($metodo == 'preguntas') {
            if (empty($usuario['pregunta1']) || empty($usuario['pregunta2'])) {
                $error = 'No tienes preguntas de seguridad configuradas. Por favor, elige otro método o contacta al administrador.';
            } else {
                $paso = 'preguntas';
            }
        } elseif ($metodo == 'otp') {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_expiry'] = time() + 300; // 5 minutos de validez

            if (enviar_otp_ultramsg($usuario['telefono'], $otp)) {
                $paso = 'otp';
            } else {
                $error = 'No se pudo enviar el código OTP. Intenta con otro método.';
            }
        }
    }
}

// Validar respuestas de seguridad
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['validar_respuestas'])) {
    $paso = 'preguntas';
    if (isset($_SESSION['recuperacion_id_usuario'])) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['recuperacion_id_usuario']]);
        $usuario = $stmt->fetch();

        $respuesta1 = trim($_POST['respuesta1']);
        $respuesta2 = trim($_POST['respuesta2']);

        if (strcasecmp(trim($usuario['respuesta1']), $respuesta1) === 0 && strcasecmp(trim($usuario['respuesta2']), $respuesta2) === 0) {
            $_SESSION['recuperacion_validada'] = true;
            $paso = 3;
        } else {
            $error = 'Una o ambas respuestas son incorrectas.';
        }
    }
}

// Validar OTP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['validar_otp'])) {
    $paso = 'otp';
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry']) && time() < $_SESSION['otp_expiry']) {
        if ($_POST['otp'] == $_SESSION['otp']) {
            $_SESSION['recuperacion_validada'] = true;
            unset($_SESSION['otp'], $_SESSION['otp_expiry']);
            $paso = 3;
        } else {
            $error = 'El código OTP es incorrecto.';
        }
    } else {
        $error = 'El código OTP ha expirado. Por favor, solicita uno nuevo.';
        $paso = 2; // Volver a la selección de método
    }
}


// Paso 3: Restablecer contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['restablecer_clave'])) {
    if (isset($_SESSION['recuperacion_id_usuario']) && isset($_SESSION['recuperacion_validada']) && $_SESSION['recuperacion_validada']) {
        $paso = 3;
        $clave = $_POST['clave'];
        $confirmar_clave = $_POST['confirmar_clave'];

        if (empty($clave) || empty($confirmar_clave)) {
            $error = 'Ambos campos de contraseña son obligatorios.';
        } elseif ($clave !== $confirmar_clave) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            $clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");
            if ($stmt->execute([$clave_hasheada, $_SESSION['recuperacion_id_usuario']])) {
                $success = 'Tu contraseña ha sido actualizada exitosamente. Ahora puedes <a href="login.php">iniciar sesión</a>.';
                session_unset();
                session_destroy();
            } else {
                $error = 'Hubo un error al actualizar tu contraseña.';
            }
        }
    } else {
        $error = 'No tienes permiso para realizar esta acción. Por favor, comienza de nuevo.';
        $paso = 1;
        session_unset();
        session_destroy();
    }
}

// Mantener el paso si la página se recarga
if ($_SERVER["REQUEST_METHOD"] != "POST" && isset($_SESSION['recuperacion_id_usuario'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['recuperacion_id_usuario']]);
    $usuario = $stmt->fetch();
    if (isset($_SESSION['recuperacion_validada']) && $_SESSION['recuperacion_validada']) {
        $paso = 3;
    } elseif (isset($_SESSION['otp'])) {
        $paso = 'otp';
    } else {
        $paso = 2;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <h2 class="text-center mb-4">Recuperar Contraseña</h2>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php else: ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <?php if ($paso == 1): ?>
                <p>Ingresa tu cédula o correo electrónico para buscar tu cuenta.</p>
                <form action="recuperar.php" method="post">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="identificador" name="identificador" placeholder="Cédula o Correo" required>
                        <label for="identificador">Cédula o Correo Electrónico</label>
                    </div>
                    <button type="submit" name="buscar_usuario" class="btn btn-primary w-100">Buscar</button>
                </form>
            <?php elseif ($paso == 2): ?>
                <p>Hola, <strong><?= htmlspecialchars($usuario['nombres']) ?></strong>. ¿Cómo deseas recuperar tu contraseña?</p>
                <form action="recuperar.php" method="post">
                    <input type="hidden" name="elegir_metodo" value="1">
                    <div class="d-grid gap-2">
                        <button type="submit" name="metodo" value="preguntas" class="btn btn-secondary">Usar Preguntas de Seguridad</button>
                        <button type="submit" name="metodo" value="otp" class="btn btn-info">Enviar Código a mi Teléfono</button>
                    </div>
                </form>
            <?php elseif ($paso == 'preguntas' && $usuario): ?>
                <p>Responde tus preguntas de seguridad para continuar.</p>
                <form action="recuperar.php" method="post">
                    <div class="mb-3">
                        <label for="respuesta1" class="form-label"><strong><?= htmlspecialchars($usuario['pregunta1']) ?></strong></label>
                        <input type="text" class="form-control" id="respuesta1" name="respuesta1" required>
                    </div>
                    <div class="mb-3">
                        <label for="respuesta2" class="form-label"><strong><?= htmlspecialchars($usuario['pregunta2']) ?></strong></label>
                        <input type="text" class="form-control" id="respuesta2" name="respuesta2" required>
                    </div>
                    <button type="submit" name="validar_respuestas" class="btn btn-primary w-100">Verificar</button>
                </form>
            <?php elseif ($paso == 'otp'): ?>
                <p>Hemos enviado un código OTP a tu número de teléfono registrado. Ingresa el código para continuar.</p>
                <form action="recuperar.php" method="post">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="otp" name="otp" placeholder="Código OTP" required>
                        <label for="otp">Código OTP</label>
                    </div>
                    <button type="submit" name="validar_otp" class="btn btn-primary w-100">Validar Código</button>
                </form>
            <?php elseif ($paso == 3): ?>
                <p>Ingresa tu nueva contraseña.</p>
                <form action="recuperar.php" method="post">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="clave" name="clave" placeholder="Nueva Contraseña" required>
                        <label for="clave">Nueva Contraseña</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" placeholder="Confirmar Nueva Contraseña" required>
                        <label for="confirmar_clave">Confirmar Nueva Contraseña</label>
                    </div>
                    <button type="submit" name="restablecer_clave" class="btn btn-primary w-100">Restablecer Contraseña</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <div class="mt-3 text-center">
            <a href="login.php">Volver al inicio de sesión</a>
        </div>
    </div>
</div>
</body>
</html>