<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$error = '';
$success = '';

// Cargar preguntas de seguridad disponibles
$stmt_preguntas = $pdo->query("SELECT id, pregunta FROM preguntas_seguridad ORDER BY id");
$preguntas_disponibles = $stmt_preguntas->fetchAll(PDO::FETCH_ASSOC);

// Cargar preguntas ya configuradas por el usuario
$stmt_configuradas = $pdo->prepare(
    "SELECT rsu.pregunta_id, ps.pregunta 
     FROM respuestas_seguridad_usuario rsu
     JOIN preguntas_seguridad ps ON rsu.pregunta_id = ps.id
     WHERE rsu.usuario_id = ?"
);
$stmt_configuradas->execute([$usuario_id]);
$preguntas_configuradas = $stmt_configuradas->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuestas = $_POST['respuestas'] ?? [];
    $preguntas_seleccionadas = $_POST['preguntas'] ?? [];

    if (count($respuestas) < 3) {
        $error = 'Debes configurar al menos 3 preguntas de seguridad.';
    } else {
        try {
            $pdo->beginTransaction();

            // Primero, eliminar las preguntas anteriores para este usuario
            $stmt_delete = $pdo->prepare("DELETE FROM respuestas_seguridad_usuario WHERE usuario_id = ?");
            $stmt_delete->execute([$usuario_id]);

            // Luego, insertar las nuevas respuestas
            $stmt_insert = $pdo->prepare(
                "INSERT INTO respuestas_seguridad_usuario (usuario_id, pregunta_id, respuesta) VALUES (?, ?, ?)"
            );

            foreach ($respuestas as $pregunta_id => $respuesta_texto) {
                if (!empty(trim($respuesta_texto))) {
                    // Guardar la respuesta hasheada y en minúsculas para una comparación insensible a mayúsculas
                    $respuesta_hasheada = password_hash(strtolower(trim($respuesta_texto)), PASSWORD_DEFAULT);
                    $stmt_insert->execute([$usuario_id, $pregunta_id, $respuesta_hasheada]);
                }
            }

            $pdo->commit();
            $success = '¡Tus preguntas de seguridad se han actualizado correctamente!';
            // Recargar las preguntas configuradas para mostrar los cambios
            $stmt_configuradas->execute([$usuario_id]);
            $preguntas_configuradas = $stmt_configuradas->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error al guardar la configuración: ' . $e->getMessage();
        }
    }
}

include 'header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Configurar Preguntas de Seguridad</h3>
    </div>
    <div class="card-body">
        <p>Configura tus preguntas de seguridad para poder recuperar tu cuenta si olvidas la contraseña. Debes configurar al menos 3.</p>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (count($preguntas_configuradas) > 0): ?>
            <div class="alert alert-info">
                <p class="mb-0"><strong>Ya tienes preguntas configuradas.</strong> Si guardas una nueva configuración, la anterior será reemplazada.</p>
                <p class="fw-bold mt-2">Preguntas actuales:</p>
                <ul>
                    <?php foreach ($preguntas_configuradas as $pc): ?>
                        <li><?= htmlspecialchars($pc['pregunta']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="configurar_seguridad.php" method="post">
            <p><strong>Selecciona tus preguntas y escribe las respuestas:</strong></p>
            
            <?php foreach ($preguntas_disponibles as $pregunta): ?>
            <div class="mb-3">
                <label for="respuesta_<?= $pregunta['id'] ?>" class="form-label"><?= htmlspecialchars($pregunta['pregunta']) ?></label>
                <input type="text" class="form-control" 
                       id="respuesta_<?= $pregunta['id'] ?>" 
                       name="respuestas[<?= $pregunta['id'] ?>]"
                       placeholder="Escribe tu respuesta aquí (no la olvides)">
            </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Guardar Configuración</button>
            <a href="principal.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
