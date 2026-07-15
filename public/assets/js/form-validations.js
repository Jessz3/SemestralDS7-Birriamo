document.addEventListener('DOMContentLoaded', () => {
    const compareFields = (form, lowerName, upperName, message, allowEqual = true) => {
        const lower = form.elements.namedItem(lowerName);
        const upper = form.elements.namedItem(upperName);

        if (!lower || !upper) return;

        const validate = () => {
            upper.setCustomValidity('');
            if (!lower.value || !upper.value) return;

            const lowerValue = lower.type === 'number' ? Number(lower.value) : lower.value;
            const upperValue = upper.type === 'number' ? Number(upper.value) : upper.value;
            const invalid = allowEqual ? upperValue < lowerValue : upperValue <= lowerValue;

            if (invalid) upper.setCustomValidity(message);
        };

        lower.addEventListener('input', validate);
        upper.addEventListener('input', validate);
        form.addEventListener('submit', validate);
    };

    document.querySelectorAll('form').forEach((form) => {
        compareFields(form, 'edad_minima', 'edad_maxima',
            'La edad maxima no puede ser menor que la edad minima.');
        compareFields(form, 'minimo_jugadores', 'maximo_jugadores',
            'El maximo de jugadores no puede ser menor que el minimo.');
        compareFields(form, 'fecha_inicio', 'fecha_fin',
            'La fecha de fin debe ser posterior a la fecha de inicio.', false);

        const inicio = form.elements.namedItem('fecha_inicio');
        const cierre = form.elements.namedItem('fecha_cierre_inscripcion');
        if (inicio && cierre) {
            const validateClosingDate = () => {
                cierre.setCustomValidity('');
                if (inicio.value && cierre.value && cierre.value > inicio.value) {
                    cierre.setCustomValidity(
                        'El cierre de inscripcion no puede ser posterior al inicio de la actividad.'
                    );
                }
            };
            inicio.addEventListener('input', validateClosingDate);
            cierre.addEventListener('input', validateClosingDate);
            form.addEventListener('submit', validateClosingDate);
        }
    });
});
