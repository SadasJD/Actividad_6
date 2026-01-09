<?php
include 'header.php';
include 'db.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  verificar_csrf();
  $empresa = limpiar_entrada($_POST['empresa'] ?? '');
  $contacto = limpiar_entrada($_POST['contacto'] ?? '');
  $ubicacion = limpiar_entrada($_POST['ubicacion'] ?? '');
  $telefono = limpiar_entrada($_POST['telefono'] ?? '');
  
  if (empty($empresa)) {
    echo "<div class='alert alert-danger'>Error: El nombre de la empresa es obligatorio.</div>";
  } else {
    $logoPath = null;
    if (!empty($_FILES['logo']['name'])){
      $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
      // Validación básica de extensiones
      $allowed = ['jpg', 'jpeg', 'png', 'gif'];
      if (in_array(strtolower($ext), $allowed)) {
          $filename = 'img/logo_' . time() . '.' . $ext;
          move_uploaded_file($_FILES['logo']['tmp_name'], $filename);
          $logoPath = $filename;
      }
    }
    $pdo->prepare("INSERT INTO proveedores (empresa, contacto, ubicacion, telefono, logo) VALUES (?, ?, ?, ?, ?)")->execute([$empresa,$contacto,$ubicacion,$telefono,$logoPath]);
    header('Location: proveedores.php'); exit;
  }
}
?>
<h3>Nuevo Proveedor</h3>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <div class="mb-3">
    <label class="form-label">Logo (opcional)</label>
    <input type="file" name="logo" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Empresa</label>
    <input name="empresa" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Contacto</label>
    <input name="contacto" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Ubicación</label>
    <input name="ubicacion" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input name="telefono" class="form-control">
  </div>
  <button class="btn btn-success">Crear proveedor</button>
  <a href="proveedores.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include 'footer.php'; ?>
