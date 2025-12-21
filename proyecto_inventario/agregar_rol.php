<?php
include 'header.php';
include 'db.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $nombre = $_POST['nombre'];
  $descripcion = $_POST['descripcion'];
  $pdo->prepare("INSERT INTO roles (nombre, descripcion) VALUES (?, ?)")->execute([$nombre,$descripcion]);
  header('Location: roles.php'); exit;
}
?>
<h3>Nuevo Rol</h3>
<form method="post">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input name="nombre" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion" class="form-control"></textarea>
  </div>
  <button class="btn btn-success">Crear</button>
  <a href="roles.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include 'footer.php'; ?>
