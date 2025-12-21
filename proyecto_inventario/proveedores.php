<?php
include 'header.php';
include 'db.php';
$total = $pdo->query("SELECT COUNT(*) FROM proveedores")->fetchColumn();
$prov = $pdo->query("SELECT * FROM proveedores")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Proveedores</h3>
  <div>
    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] !== 'Vendedor'): ?>
    <a href="agregar_proveedor.php" class="btn btn-success">Nuevo Proveedor</a>
    <?php endif; ?>
  </div>
</div>
<div class="mb-3"><span class="badge bg-primary">Total proveedores: <?= $total ?></span></div>
<table class="table align-middle">
  <thead><tr><th>Logo</th><th>Empresa</th><th>Contacto</th><th>Ubicación</th><th>Teléfono</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($prov as $p): ?>
      <tr>
        <td>
          <?php if($p['logo']): ?>
            <img src="<?= htmlspecialchars($p['logo']) ?>" alt="logo" style="height:50px;">
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['empresa']) ?></td>
        <td><?= htmlspecialchars($p['contacto']) ?></td>
        <td><?= htmlspecialchars($p['ubicacion']) ?></td>
        <td><?= htmlspecialchars($p['telefono']) ?></td>
        <td>
          <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] !== 'Vendedor'): ?>
          <a href="editar_proveedor.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
          <a href="eliminar_proveedor.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
          <?php endif; ?>
          <a href="detalle_proveedor.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info">Ver</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
