<?php
include 'header.php';
include 'db.php';
$proveedores = $pdo->query("SELECT id, empresa FROM proveedores")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $nombre = $_POST['nombre'];
  $detalle = $_POST['detalle'];
  $proveedor_id = $_POST['proveedor_id'] ?: null;
  $precio = floatval($_POST['precio']);
  $cantidad = intval($_POST['cantidad']);
  $estado = $_POST['estado'];
  $pdo->prepare("INSERT INTO productos (nombre, detalle, proveedor_id, precio, cantidad, estado) VALUES (?, ?, ?, ?, ?, ?)")->execute([$nombre,$detalle,$proveedor_id,$precio,$cantidad,$estado]);
  header('Location: productos.php'); exit;
}
?>
<h3>Nuevo Producto</h3>
<form method="post">
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
