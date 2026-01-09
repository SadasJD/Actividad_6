document.addEventListener('DOMContentLoaded', function() {
    // Deshabilitar Click Derecho para evitar el acceso fácil a "Inspeccionar"
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // Deshabilitar Teclas de Inspección (F12, Ctrl+Shift+I, Ctrl+U, etc)
    document.onkeydown = function(e) {
        if (e.keyCode == 123) { // F12
            return false;
        }
        if (e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0) || e.keyCode == 'C'.charCodeAt(0) || e.keyCode == 'J'.charCodeAt(0))) {
            return false;
        }
        if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) { // Ctrl+U (Ver Fuente)
            return false;
        }
    };

    // Detectar si la consola de desarrollador se abre
    var devtools = function() {};
    devtools.toString = function() {
        return false;
    }
    var devtools = function() {};
    devtools.toString = function() {
        return false;
    }
    
    // Si intentan modificar campos 'readonly' o 'disabled' via JS, revertir
    // (Esto es preventivo en cliente, la seguridad real está en servidor)
    const inputs = document.querySelectorAll('input[readonly], input[disabled], .unselectable');
    inputs.forEach(input => {
        input.addEventListener('mousedown', (e) => {
            e.preventDefault();
        });
        input.addEventListener('selectstart', (e) => {
            e.preventDefault();
        });
        input.addEventListener('copy', (e) => {
            e.preventDefault();
        });
        input.addEventListener('input', (e) => {
            e.preventDefault();
            return false;
        });
    });
});
