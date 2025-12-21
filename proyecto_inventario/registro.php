<?php
session_start();
include 'db.php';

$error = '';
$success = '';

// Lista de preguntas de seguridad (Obtenidas aleatoriamente de la BD)
$stmt_preguntas = $pdo->query("SELECT id, pregunta FROM preguntas_seguridad ORDER BY RAND() LIMIT 3");
$preguntas_random = $stmt_preguntas->fetchAll();

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
    
    $preguntas_ids = $_POST['pregunta_id'] ?? [];
    $respuestas = $_POST['respuesta'] ?? [];

    // Validar respuestas
    $respuestas_validas = 0;
    foreach ($respuestas as $resp) {
        if (!empty(trim($resp))) {
            $respuestas_validas++;
        }
    }

    if (empty($cedula) || empty($nombres) || empty($apellidos) || empty($correo) || empty($telefono) || empty($clave) || empty($rol_id)) {
        $error = 'Todos los campos básicos son obligatorios.';
    } elseif ($respuestas_validas < 3) {
        $error = 'Debe responder las 3 preguntas de seguridad.';
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

            try {
                $pdo->beginTransaction();

                // Insertar el nuevo usuario
                $stmt_insert = $pdo->prepare(
                    "INSERT INTO usuarios (cedula, nombres, apellidos, correo, telefono, clave, rol_id, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')"
                );
                
                $stmt_insert->execute([$cedula, $nombres, $apellidos, $correo, $telefono, $clave_hasheada, $rol_id]);
                $usuario_id = $pdo->lastInsertId();

                // Insertar respuestas de seguridad
                $stmt_respuestas = $pdo->prepare("INSERT INTO respuestas_seguridad_usuario (usuario_id, pregunta_id, respuesta) VALUES (?, ?, ?)");
                
                foreach ($preguntas_ids as $index => $p_id) {
                    if (isset($respuestas[$index])) {
                        $resp_texto = trim($respuestas[$index]);
                        // Guardar respuesta hasheada y en minúsculas
                        $resp_hash = password_hash(strtolower($resp_texto), PASSWORD_DEFAULT);
                        $stmt_respuestas->execute([$usuario_id, $p_id, $resp_hash]);
                    }
                }

                $pdo->commit();
                $success = '¡Registro exitoso! Ahora puedes <a href="login.php">iniciar sesión</a>.';
            } catch (Exception $e) {
                $pdo->rollBack();
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

                <h4 class="text-center my-4">Preguntas de Seguridad (Aleatorias)</h4>
                <p class="text-muted small text-center">Responde estas 3 preguntas para proteger tu cuenta.</p>

                <?php foreach ($preguntas_random as $index => $p): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?= htmlspecialchars($p['pregunta']) ?></label>
                        <input type="hidden" name="pregunta_id[]" value="<?= $p['id'] ?>">
                        <input type="text" class="form-control" name="respuesta[]" placeholder="Tu respuesta" required>
                    </div>
                <?php endforeach; ?>
                
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