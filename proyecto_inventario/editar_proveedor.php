<?php
include 'header.php';
include 'db.php';
$id = $_GET['id'] ?? null; if (!$id) { header('Location: proveedores.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $empresa = limpiar_entrada($_POST['empresa'] ?? ''); 
    $contacto = limpiar_entrada($_POST['contacto'] ?? ''); 
    $ubicacion = limpiar_entrada($_POST['ubicacion'] ?? ''); 
    $telefono = limpiar_entrada($_POST['telefono'] ?? '');
    
    if (empty($empresa)) {
        echo "<div class='alert alert-danger'>Error: El nombre de la empresa es obligatorio.</div>";
    } else {
        $logoPath = $_POST['current_logo'] ?? null;
        if (!empty($_FILES['logo']['name'])) {
            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($ext), $allowed)) {
                $filename = 'img/logo_' . time() . '.' . $ext;
                move_uploaded_file($_FILES['logo']['tmp_name'], $filename);
                $logoPath = $filename;
            }
        }
        $pdo->prepare("UPDATE proveedores SET empresa=?, contacto=?, ubicacion=?, telefono=?, logo=? WHERE id=?")->execute([$empresa,$contacto,$ubicacion,$telefono,$logoPath,$id]);
        header('Location: proveedores.php'); exit;
    }
}
$s = $pdo->prepare("SELECT * FROM proveedores WHERE id=?"); $s->execute([$id]); $p = $s->fetch();
?>
<h3 class="unselectable">Editar Proveedor</h3>
<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <div class="mb-3">
    <label class="form-label unselectable">Logo (opcional)</label>
    <?php if($p['logo']): ?><div class="mb-2"><img src="<?= htmlspecialchars($p['logo']) ?>" style="height:70px;" class="unselectable"></div><?php endif; ?>
    <input type="file" name="logo" class="form-control">
    <input type="hidden" name="current_logo" value="<?= htmlspecialchars($p['logo']) ?>">
  </div>
  <div class="mb-3"><label class="form-label unselectable">Empresa</label><input name="empresa" class="form-control" value="<?= htmlspecialchars($p['empresa']) ?>" required></div>
  <div class="mb-3"><label class="form-label unselectable">Contacto</label><input name="contacto" class="form-control" value="<?= htmlspecialchars($p['contacto']) ?>"></div>
  <div class="mb-3"><label class="form-label unselectable">Ubicación</label><input name="ubicacion" class="form-control" value="<?= htmlspecialchars($p['ubicacion']) ?>"></div>
  <div class="mb-3"><label class="form-label unselectable">Teléfono</label><input name="telefono" class="form-control" value="<?= htmlspecialchars($p['telefono']) ?>"></div>
  <button class="btn btn-primary">Guardar</button>
  <a href="proveedores.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include 'footer.php'; ?>
