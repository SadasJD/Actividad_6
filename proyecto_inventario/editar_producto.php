<?php
include 'header.php';
include 'db.php';
$id = $_GET['id'] ?? null; if (!$id) { header('Location: productos.php'); exit; }
$proveedores = $pdo->query("SELECT id, empresa FROM proveedores")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre']; $detalle = $_POST['detalle']; $proveedor_id = $_POST['proveedor_id'] ?: null;
    $precio = floatval($_POST['precio']); $cantidad = intval($_POST['cantidad']); $estado = $_POST['estado'];
    $pdo->prepare("UPDATE productos SET nombre=?, detalle=?, proveedor_id=?, precio=?, cantidad=?, estado=? WHERE id=?")->execute([$nombre,$detalle,$proveedor_id,$precio,$cantidad,$estado,$id]);
    header('Location: productos.php'); exit;
}
$s = $pdo->prepare("SELECT * FROM productos WHERE id=?"); $s->execute([$id]); $p = $s->fetch();
?>
<h3>Editar Producto</h3>
<form method="post">
  <div class="mb-3"><label class="form-label">Producto</label><input name="nombre" class="form-control" value="<?= htmlspecialchars($p['nombre']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Detalle</label><textarea name="detalle" class="form-control"><?= htmlspecialchars($p['detalle']) ?></textarea></div>
  <div class="mb-3"><label class="form-label">Proveedor</label>
    <select name="proveedor_id" class="form-select">
      <option value="">-- Sin proveedor --</option>
      <?php foreach($proveedores as $pr): ?>
        <option value="<?= $pr['id'] ?>" <?= $p['proveedor_id']==$pr['id']?'selected':'' ?>><?= htmlspecialchars($pr['empresa']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="row">
    <div class="col-md-4 mb-3"><label class="form-label">Precio</label><input name="precio" class="form-control" value="<?= htmlspecialchars($p['precio']) ?>" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Cantidad</label><input name="cantidad" class="form-control" value="<?= intval($p['cantidad']) ?>" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Estado</label>
      <select name="estado" class="form-select"><option value="disponible" <?= $p['estado']=='disponible'?'selected':'' ?>>Disponible</option><option value="no disponible" <?= $p['estado']=='no disponible'?'selected':'' ?>>No disponible</option></select>
    </div>
  </div>
  <button class="btn btn-primary">Guardar</button>
  <a href="productos.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include 'footer.php'; ?>
