<?php
include 'header.php';
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_csrf();
    $cedula = limpiar_entrada($_POST['cedula'] ?? '');
    $nombres = limpiar_entrada($_POST['nombres'] ?? '');
    $apellidos = limpiar_entrada($_POST['apellidos'] ?? '');
    $correo = limpiar_entrada($_POST['correo'] ?? '');
    $telefono = limpiar_entrada($_POST['telefono'] ?? '');
    $clave = password_hash($_POST['clave'] ?? '', PASSWORD_DEFAULT);
    $estado = limpiar_entrada($_POST['estado'] ?? 'activo');
    $rol_id = limpiar_entrada($_POST['rol_id'] ?? null);
    
    $sql = "INSERT INTO usuarios (cedula, nombres, apellidos, correo, telefono, clave, estado, rol_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cedula, $nombres, $apellidos, $correo, $telefono, $clave, $estado, $rol_id]);
    header('Location: usuarios.php');
    exit;
}
?>
<h3>Nuevo Usuario</h3>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
  <div class="mb-3">
    <label class="form-label">Cédula</label>
    <input name="cedula" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Nombres</label>
    <input name="nombres" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Apellidos</label>
    <input name="apellidos" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Correo</label>
    <input type="email" name="correo" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Teléfono</label>
    <input name="telefono" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Clave</label>
    <input type="password" name="clave" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Rol</label>
    <select name="rol_id" class="form-select">
      <option value="">Sin rol</option>
      <?php 
      $roles = $pdo->query("SELECT id, nombre FROM roles")->fetchAll();
      foreach ($roles as $rol) {
          echo "<option value='{$rol['id']}'>{$rol['nombre']}</option>";
      }
      ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Estado</label>
    <select name="estado" class="form-select">
      <option value="activo">Activo</option>
      <option value="inactivo">Inactivo</option>
    </select>
  </div>
  <button class="btn btn-success">Crear</button>
  <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php include 'footer.php'; ?>
