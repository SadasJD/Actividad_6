document.addEventListener('DOMContentLoaded', function() {
    const selects = [
        document.getElementById('pregunta1'),
        document.getElementById('pregunta2'),
        document.getElementById('pregunta3')
    ];

    function getOpcionesDisponibles() {
        const seleccionadas = selects.map(s => s.value).filter(Boolean);
        return preguntasDisponibles.filter(p => !seleccionadas.includes(p.id.toString()));
    }

    function popularSelect(selectElement) {
        const valorActual = selectElement.value;
        const opciones = getOpcionesDisponibles();
        
        // Si el valor actual es válido, añadirlo a las opciones para que no se borre
        if (valorActual) {
            const opcionActual = preguntasDisponibles.find(p => p.id.toString() === valorActual);
            if (opcionActual) {
                opciones.unshift(opcionActual);
            }
        }

        selectElement.innerHTML = '<option value="">Selecciona una pregunta...</option>';
        
        opciones.forEach(p => {
            const option = document.createElement('option');
            option.value = p.id;
            option.textContent = p.pregunta;
            selectElement.appendChild(option);
        });

        selectElement.value = valorActual;
    }

    function actualizarTodosLosSelects() {
        selects.forEach(s => popularSelect(s));
    }

    selects.forEach(select => {
        select.addEventListener('change', () => {
            actualizarTodosLosSelects();
        });
    });

    // Inicializar los selects
    actualizarTodosLosSelects();
});
