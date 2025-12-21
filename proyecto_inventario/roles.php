<?php
include 'header.php';
include 'db.php';
$total = $pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Roles</h3>
  <div>
    <a href="agregar_rol.php" class="btn btn-success">Nuevo Rol</a>
  </div>
</div>
<div class="mb-3"><span class="badge bg-primary">Total roles: <?= $total ?></span></div>
<table class="table table-hover">
  <thead><tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($roles as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['nombre']) ?></td>
        <td><?= htmlspecialchars($r['descripcion']) ?></td>
        <td>
          <a href="editar_rol.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
          <a href="eliminar_rol.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
