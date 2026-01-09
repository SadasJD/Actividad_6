<?php
require_once 'includes/seguridad_global.php';
// include 'header.php'; // Header ya incluye seguridad_global, pero evitar doble sesión
if (session_status() === PHP_SESSION_NONE) session_start();
include 'header.php';
include 'db.php';
$proveedores = $pdo->query("SELECT id, empresa FROM proveedores")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  verificar_csrf();
  $nombre = limpiar_entrada($_POST['nombre'] ?? '');
  $detalle = limpiar_entrada($_POST['detalle'] ?? '');
  $proveedor_id = limpiar_entrada($_POST['proveedor_id'] ?? '') ?: null;
  $precio = floatval($_POST['precio'] ?? 0);
  $cantidad = intval($_POST['cantidad'] ?? 0);
  $estado = limpiar_entrada($_POST['estado'] ?? 'disponible');
  
  if (empty($nombre)) {
      echo "<div class='alert alert-danger'>Error: El nombre del producto es obligatorio.</div>";
  } else {
      $pdo->prepare("INSERT INTO productos (nombre, detalle, proveedor_id, precio, cantidad, estado) VALUES (?, ?, ?, ?, ?, ?)")->execute([$nombre,$detalle,$proveedor_id,$precio,$cantidad,$estado]);
      header('Location: productos.php'); exit;
  }
}
?>
<h3>Nuevo Producto</h3>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <div class="mb-3"><label class="form-label">Producto</label><input name="nombre" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Detalle</label><textarea name="detalle" class="form-control"></textarea></div>
  <div class="mb-3"><label class="form-label">Proveedor</label>
    <select name="proveedor_id" class="form-select">
      <option value="">-- Sin proveedor --</option>
      <?php foreach($proveedores as $pr): ?>
        <option value="<?= $pr['id'] ?>"><?= htmlspecialchars($pr['empresa']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">Precio</label><input name="precio" class="form-control" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Cantidad</label><input name="cantidad" class="form-control" value="1" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Estado</label>
      <select name="estado" class="form-select"><option value="disponible">Disponible</option><option value="no disponible">No disponible</option></select>
    </div>
  </div>
  <button class="btn btn-success">Crear producto</button>
  <a href="productos.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include 'footer.php'; ?>
