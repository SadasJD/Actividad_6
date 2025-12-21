<?php
include 'header.php';
include 'db.php';
$id = $_GET['id'] ?? null; if (!$id) { header('Location: roles.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre']; $descripcion = $_POST['descripcion'];
    $pdo->prepare("UPDATE roles SET nombre=?, descripcion=? WHERE id=?")->execute([$nombre,$descripcion,$id]);
    header('Location: roles.php'); exit;
}
$r = $pdo->prepare("SELECT * FROM roles WHERE id = ?"); $r->execute([$id]); $role = $r->fetch();
?>
<h3>Editar Rol</h3>
<form method="post">
  <div class="mb-3"><label class="form-label">Nombre</label><input name="nombre" class="form-control" value="<?= htmlspecialchars($role['nombre']) ?>" required></div>
  <div class="mb-3"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control"><?= htmlspecialchars($role['descripcion']) ?></textarea></div>
  <button class="btn btn-primary">Guardar</button>
  <a href="roles.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include 'footer.php'; ?>
