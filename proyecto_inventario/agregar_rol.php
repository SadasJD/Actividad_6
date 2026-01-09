<?php
include 'header.php';
include 'db.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  verificar_csrf();
  $nombre = limpiar_entrada($_POST['nombre'] ?? '');
  $descripcion = limpiar_entrada($_POST['descripcion'] ?? '');
  
  if (empty($nombre)) {
    echo "<div class='alert alert-danger'>Error: El nombre del rol es obligatorio.</div>";
  } else {
    $pdo->prepare("INSERT INTO roles (nombre, descripcion) VALUES (?, ?)")->execute([$nombre,$descripcion]);
    header('Location: roles.php'); exit;
  }
}
?>
<h3>Nuevo Rol</h3>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
