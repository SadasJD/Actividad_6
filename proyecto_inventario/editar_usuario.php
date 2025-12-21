<?php
include 'header.php';
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: usuarios.php');
    exit;
}

// Obtener roles para el dropdown
$roles_stmt = $pdo->query("SELECT id, nombre FROM roles");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = $_POST['cedula'] ?? '';
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $estado = $_POST['estado'] ?? 'activo';
    $rol_id = $_POST['rol_id'] ?? null;
    $clave = $_POST['clave'];

    // Si se proporciona una nueva clave, hashearla. Si no, no actualizarla.
    if (!empty($clave)) {
        $clave_hashed = password_hash($clave, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET cedula=?, nombres=?, apellidos=?, correo=?, telefono=?, clave=?, estado=?, rol_id=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombres, $apellidos, $correo, $telefono, $clave_hashed, $estado, $rol_id, $id]);
    } else {
        $sql = "UPDATE usuarios SET cedula=?, nombres=?, apellidos=?, correo=?, telefono=?, estado=?, rol_id=? WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cedula, $nombres, $apellidos, $correo, $telefono, $estado, $rol_id, $id]);
    }

    header('Location: usuarios.php');
    exit;
}

$u = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$u->execute([$id]);
$user = $u->fetch();

if (!$user) {
    echo "Usuario no encontrado.";
    exit;
}
?>

<h3>Editar Usuario</h3>
<form method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula</label>
                <input id="cedula" name="cedula" type="text" class="form-control" value="<?= htmlspecialchars($user['cedula'] ?? '') ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="nombres" class="form-label">Nombres</label>
                <input id="nombres" name="nombres" type="text" class="form-control" value="<?= htmlspecialchars($user['nombres'] ?? '') ?>" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input id="apellidos" name="apellidos" type="text" class="form-control" value="<?= htmlspecialchars($user['apellidos'] ?? '') ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo Electrónico</label>
                <input id="correo" name="correo" type="email" class="form-control" value="<?= htmlspecialchars($user['correo'] ?? '') ?>" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input id="telefono" name="telefono" type="text" class="form-control" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="clave" class="form-label">Nueva Clave (dejar en blanco para no cambiar)</label>
                <input id="clave" name="clave" type="password" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="activo" <?= ($user['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= ($user['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="rol_id" class="form-label">Rol</label>
                <select id="rol_id" name="rol_id" class="form-select">
                    <option value="">Sin rol</option>
                    <?php foreach ($roles as $rol) : ?>
                        <option value="<?= $rol['id'] ?>" <?= ($user['rol_id'] ?? '') == $rol['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($rol['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
</form>

<?php include 'footer.php'; ?>