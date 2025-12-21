<?php
function verificar_acceso($rol) {
    $permisos = [
        'Admin' => [
            'index.php',
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
            'index.php',
            'productos.php',
            'proveedores.php',
            'detalle_proveedor.php',
            'logout.php'
        ]
    ];

    $pagina_actual = basename($_SERVER['PHP_SELF']);

    if (isset($permisos[$rol])) {
        if (!in_array($pagina_actual, $permisos[$rol])) {
            header('Location: index.php?error=acceso_denegado');
            exit;
        }
    } else {
        // Si el rol no existe en la lista de permisos, denegar por defecto
        header('Location: index.php?error=rol_invalido');
        exit;
    }
}
?>