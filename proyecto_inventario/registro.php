<?php
session_start();
include 'db.php';

$error = '';
$success = '';

// Lista de preguntas de seguridad
$preguntas_seguridad = [
    '¿Cuál es el nombre de tu primera mascota?',
    '¿Cuál es tu comida favorita?',
    '¿En qué ciudad naciste?',
    '¿Cuál es el segundo nombre de tu padre?',
    '¿Cuál era tu apodo de niño?',
    '¿Cuál es el nombre de tu abuela materna?',
    '¿Cuál es tu película favorita?'
];

// Cargar roles para el formulario
$stmt_roles = $pdo->query("SELECT id, nombre FROM roles ORDER BY nombre");
$roles = $stmt_roles->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validaciones básicas del lado del servidor
    $cedula = trim($_POST['cedula']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $clave = $_POST['clave'];
    $confirmar_clave = $_POST['confirmar_clave'];
    $rol_id = $_POST['rol_id'];
    $pregunta1 = !empty($_POST['pregunta1']) ? $_POST['pregunta1'] : null;
    $respuesta1 = !empty($_POST['respuesta1']) ? trim($_POST['respuesta1']) : null;
    $pregunta2 = !empty($_POST['pregunta2']) ? $_POST['pregunta2'] : null;
    $respuesta2 = !empty($_POST['respuesta2']) ? trim($_POST['respuesta2']) : null;

    if (empty($cedula) || empty($nombres) || empty($apellidos) || empty($correo) || empty($telefono) || empty($clave) || empty($rol_id)) {
        $error = 'Todos los campos básicos son obligatorios.';
    } elseif (($pregunta1 && !$respuesta1) || ($pregunta2 && !$respuesta2)) {
        $error = 'Debe proporcionar una respuesta para cada pregunta de seguridad seleccionada.';
    } elseif ($pregunta1 && $pregunta1 === $pregunta2) {
        $error = 'Las preguntas de seguridad deben ser diferentes.';
    } elseif ($clave !== $confirmar_clave) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del correo electrónico no es válido.';
    } else {
        // Verificar si la cédula o el correo ya existen
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE cedula = ? OR correo = ?");
        $stmt->execute([$cedula, $correo]);
        if ($stmt->fetch()) {
            $error = 'La cédula o el correo electrónico ya están registrados.';
        } else {
            // Hashear la contraseña
            $clave_hasheada = password_hash($clave, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario
            $stmt_insert = $pdo->prepare(
                "INSERT INTO usuarios (cedula, nombres, apellidos, correo, telefono, clave, rol_id, estado, pregunta1, respuesta1, pregunta2, respuesta2) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'activo', ?, ?, ?, ?)"
            );
            
            if ($stmt_insert->execute([$cedula, $nombres, $apellidos, $correo, $telefono, $clave_hasheada, $rol_id, $pregunta1, $respuesta1, $pregunta2, $respuesta2])) {
                $success = '¡Registro exitoso! Ahora puedes <a href="login.php">iniciar sesión</a>.';
            } else {
                $error = 'Hubo un error al crear tu cuenta. Por favor, inténtalo de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Nuevo Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<div class="login-container">
    <div class="login-box" style="max-width: 600px;">
        <h2 class="text-center mb-4">Crear una Cuenta</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php else: ?>
            <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <form action="registro.php" method="post" id="registroForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres" required value="<?= htmlspecialchars($_POST['nombres'] ?? '') ?>">
                            <label for="nombres">Nombres</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos" required value="<?= htmlspecialchars($_POST['apellidos'] ?? '') ?>">
                            <label for="apellidos">Apellidos</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula de Identidad" required value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>">
                    <label for="cedula">Cédula de Identidad</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo Electrónico" required value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                    <label for="correo">Correo Electrónico</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" required value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                    <label for="telefono">Teléfono</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="rol_id" name="rol_id" required>
                        <option value="" disabled selected>Selecciona un rol</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id'] ?>" <?= (isset($_POST['rol_id']) && $_POST['rol_id'] == $rol['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="rol_id">Rol</label>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="clave" name="clave" placeholder="Clave" required>
                            <label for="clave">Clave</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" placeholder="Confirmar Clave" required>
                            <label for="confirmar_clave">Confirmar Clave</label>
                        </div>
                    </div>
                </div>

                <h4 class="text-center my-4">Preguntas de Seguridad</h4>

                <div class="form-floating mb-3">
                    <select class="form-select" id="pregunta1" name="pregunta1">
                        <option value="" selected>No establecer primera pregunta</option>
                        <?php foreach ($preguntas_seguridad as $pregunta): ?>
                            <option value="<?= htmlspecialchars($pregunta) ?>" <?= (isset($_POST['pregunta1']) && $_POST['pregunta1'] == $pregunta) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pregunta) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="pregunta1">Pregunta 1</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="respuesta1" name="respuesta1" placeholder="Respuesta a la pregunta 1" value="<?= htmlspecialchars($_POST['respuesta1'] ?? '') ?>">
                    <label for="respuesta1">Respuesta 1</label>
                </div>

                <div class="form-floating mb-3">
                    <select class="form-select" id="pregunta2" name="pregunta2">
                        <option value="" selected>No establecer segunda pregunta</option>
                        <?php foreach ($preguntas_seguridad as $pregunta): ?>
                            <option value="<?= htmlspecialchars($pregunta) ?>" <?= (isset($_POST['pregunta2']) && $_POST['pregunta2'] == $pregunta) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pregunta) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="pregunta2">Pregunta 2</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="respuesta2" name="respuesta2" placeholder="Respuesta a la pregunta 2" value="<?= htmlspecialchars($_POST['respuesta2'] ?? '') ?>">
                    <label for="respuesta2">Respuesta 2</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
            </form>
        <?php endif; ?>
        <div class="mt-3 text-center">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
        </div>
    </div>
</div>
<script src="assets/js/registro.js"></script>
</body>
</html>