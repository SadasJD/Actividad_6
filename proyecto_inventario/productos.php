<?php
include 'header.php';
include 'db.php';
$total = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$valor = $pdo->query("SELECT IFNULL(SUM(precio * cantidad),0) FROM productos")->fetchColumn();
$prod = $pdo->query("SELECT p.*, pr.empresa, pr.logo FROM productos p LEFT JOIN proveedores pr ON p.proveedor_id = pr.id")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Productos</h3>
  <div>
    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] !== 'Vendedor'): ?>
    <a href="agregar_producto.php" class="btn btn-success">Nuevo Producto</a>
    <?php endif; ?>
  </div>
</div>
<div class="mb-3">
  <span class="badge bg-primary">Total productos: <?= $total ?></span>
  <span class="badge bg-success">Valor inventario: $<?= number_format($valor,2) ?></span>
</div>
<table class="table align-middle">
  <thead><tr><th>Producto</th><th>Detalle</th><th>Proveedor</th><th>Precio</th><th>Cantidad</th><th>Estado</th><th>Acciones</th></tr></thead>
  <tbody>
    <?php foreach($prod as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td><?= htmlspecialchars($p['detalle']) ?></td>
        <td>
          <?php if($p['logo']): ?><img src="<?= htmlspecialchars($p['logo']) ?>" style="height:40px;" alt="logo" class="me-2"><?php endif; ?>
          <?= htmlspecialchars($p['empresa']) ?>
        </td>
        <td>$<?= number_format($p['precio'],2) ?></td>
        <td><?= intval($p['cantidad']) ?></td>
        <td><?= htmlspecialchars($p['estado']) ?></td>
        <td>
          <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] !== 'Vendedor'): ?>
          <a href="editar_producto.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
          <a href="eliminar_producto.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include 'footer.php'; ?>
