<?php
function verificar_acceso($rol) {
    // Normalizar rol (quitar espacios, primera mayúscula)
    $rol = trim($rol);
    
    // Mapeo de nombres de roles de la DB a las claves del array de permisos
    $mapeo_roles = [
        'Admin' => 'Admin',
        'Administrador' => 'Admin',
        'Vendedor' => 'Vendedor',
        'vendedor' => 'Vendedor'
    ];

    $rol_clave = $mapeo_roles[$rol] ?? $rol;

    $permisos = [
        'Admin' => [
            'principal.php',
            'productos.php',
            'agregar_producto.php',
            'editar_producto.php',
            'eliminar_producto.php',
            'proveedores.php',
            'agregar_proveedor.php',
            'editar_proveedor.php',
            'eliminar_proveedor.php',
            'detalle_proveedor.php',
            'usuarios.php',
            'agregar_usuario.php',
            'editar_usuario.php',
            'eliminar_usuario.php',
            'roles.php',
            'agregar_rol.php',
            'editar_rol.php',
            'eliminar_rol.php',
            'configurar_seguridad.php',
            'logout.php'
        ],
        'Vendedor' => [
            'principal.php',
            'productos.php',
            'proveedores.php',
            'detalle_proveedor.php',
            'logout.php'
        ]
    ];

    $pagina_actual = basename($_SERVER['PHP_SELF']);

    // Si ya estamos en principal.php y hay un error, no volver a redireccionar para evitar bucles
    if ($pagina_actual === 'principal.php' && isset($_GET['error'])) {
        return;
    }

    if (isset($permisos[$rol_clave])) {
        if (!in_array($pagina_actual, $permisos[$rol_clave])) {
            header('Location: principal.php?error=acceso_denegado');
            exit;
        }
    } else {
        // Redireccionar al login si el rol no es válido, evitando bucles en principal.php
        if ($pagina_actual !== 'login.php') {
            header('Location: login.php?error=rol_invalido');
            exit;
        }
    }
}
?>