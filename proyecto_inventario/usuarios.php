<?php
include 'header.php';
include 'db.php';
$total = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$activos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE estado = 'activo'")->fetchColumn();
$inactivos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE estado = 'inactivo'")->fetchColumn();
$stm = $pdo->query("SELECT u.*, r.nombre as rol_nombre FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id");
$usuarios = $stm->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Usuarios</h3>
  <div>
    <a href="agregar_usuario.php" class="btn btn-success">Nuevo Usuario</a>
  </div>
</div>
<div class="mb-3">
  <span class="badge bg-primary">Total: <?= $total ?></span>
  <span class="badge bg-success">Activos: <?= $activos ?></span>
  <span class="badge bg-secondary">Inactivos: <?= $inactivos ?></span>
</div>
<table class="table table-striped">
  <thead>
    <tr><th>Cédula</th><th>Nombre Completo</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr>
  </thead>
  <tbody>
    <?php foreach($usuarios as $u): ?>
      <tr>
        <td><?= htmlspecialchars($u['cedula']) ?></td>
        <td><?= htmlspecialchars($u['nombres'] . ' ' . $u['apellidos']) ?></td>
        <td><?= htmlspecialchars($u['correo']) ?></td>
        <td><span class="badge bg-info"><?= htmlspecialchars($u['rol_nombre'] ?? 'Sin rol') ?></span></td>
        <td>
            <span class="badge <?= $u['estado'] == 'activo' ? 'bg-success' : 'bg-secondary' ?>">
                <?= htmlspecialchars($u['estado']) ?>
            </span>
        </td>
        <td>
          <a href="editar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
          <a href="eliminar_usuario.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?')">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
