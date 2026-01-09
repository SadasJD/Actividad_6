<?php 
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
include 'header.php'; 
include 'db.php';

// Obtener conteos solo si es administrador
$conteos = [];
if ($_SESSION['usuario_rol'] !== 'Vendedor') {
    $conteos['usuarios'] = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $conteos['roles'] = $pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    $conteos['proveedores'] = $pdo->query("SELECT COUNT(*) FROM proveedores")->fetchColumn();
    $conteos['productos'] = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
}
?>
<div class="row text-center">
    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] !== 'Vendedor'): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Usuarios</h5>
                    <h2 class="display-4 fw-bold text-primary"><?= $conteos['usuarios'] ?></h2>
                    <p class="card-text">Gestiona usuarios activos/inactivos.</p>
                    <a href="usuarios.php" class="btn btn-primary">Ir a Usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Roles</h5>
                    <h2 class="display-4 fw-bold text-primary"><?= $conteos['roles'] ?></h2>
                    <p class="card-text">Crea y edita roles.</p>
                    <a href="roles.php" class="btn btn-primary">Ir a Roles</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Proveedores</h5>
                <?php if (isset($conteos['proveedores'])): ?>
                    <h2 class="display-4 fw-bold text-primary"><?= $conteos['proveedores'] ?></h2>
                <?php endif; ?>
                <p class="card-text">Administra proveedores y logos.</p>
                <a href="proveedores.php" class="btn btn-primary">Ir a Proveedores</a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title">Productos</h5>
                <?php if (isset($conteos['productos'])): ?>
                    <h2 class="display-4 fw-bold text-primary"><?= $conteos['productos'] ?></h2>
                <?php endif; ?>
                <p class="card-text">Productos con proveedor y precio.</p>
                <a href="productos.php" class="btn btn-primary">Ir a Productos</a>
            </div>
        </div>
    </div>
    <?php if (isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] !== 'Vendedor'): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><i class="fas fa-cogs me-2"></i>Seguridad</h5>
                    <p class="card-text">Configura tus preguntas de seguridad para recuperar tu cuenta.</p>
                    <a href="configurar_seguridad.php" class="btn btn-secondary mt-auto">Configurar</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
