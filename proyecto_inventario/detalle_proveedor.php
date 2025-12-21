<?php
include 'header.php';
include 'db.php';
$id = $_GET['id'] ?? null; if (!$id) { header('Location: proveedores.php'); exit; }
$stm = $pdo->prepare("SELECT * FROM proveedores WHERE id = ?"); $stm->execute([$id]); $p = $stm->fetch();
?>
<div class="card">
  <div class="card-body d-flex">
    <div class="me-4">
      <?php if($p['logo']): ?>
        <img src="<?= htmlspecialchars($p['logo']) ?>" alt="logo" style="height:120px;">
      <?php endif; ?>
    </div>
    <div>
      <h4><?= htmlspecialchars($p['empresa']) ?></h4>
      <p><strong>Contacto:</strong> <?= htmlspecialchars($p['contacto']) ?></p>
      <p><strong>Ubicación:</strong> <?= htmlspecialchars($p['ubicacion']) ?></p>
      <p><strong>Teléfono:</strong> <?= htmlspecialchars($p['telefono']) ?></p>
      <a href="proveedores.php" class="btn btn-secondary">Volver</a>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>
