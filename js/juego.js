document.addEventListener('DOMContentLoaded', () => {
    const btnHelp        = document.getElementById('btn-help');
    const btnCloseHelp   = document.getElementById('btn-close-help');
    const modalHelp      = document.getElementById('modal-help');
    const optionButtons  = document.querySelectorAll('.btn-option');
    const newtonMessage  = document.getElementById('newton-message');
    const explicacionBox = document.getElementById('explicacion');
    const scoreDisplay   = document.getElementById('score');
    const btnNext        = document.getElementById('btn-next');

    let enviando = false;   // evita enviar dos respuestas al mismo tiempo

    // Abrir y cerrar la ayuda
    if (btnHelp) {
        btnHelp.addEventListener('click', () => modalHelp.classList.remove('hidden'));
    }
    if (btnCloseHelp) {
        btnCloseHelp.addEventListener('click', () => modalHelp.classList.add('hidden'));
    }

    // Enviar la respuesta al servidor, que es quien la valida
    optionButtons.forEach(button => {
        button.addEventListener('click', async () => {
            if (enviando) return;
            enviando = true;

            const datos = new FormData();
            datos.append('opcion', button.dataset.option);

            try {
                const respuesta = await fetch('responder.php', { method: 'POST', body: datos });
                const r = await respuesta.json();

                if (!respuesta.ok || r.error) {
                    throw new Error(r.error || 'Error del servidor');
                }

                newtonMessage.textContent = r.mensaje;

                if (r.correcta) {
                    scoreDisplay.textContent = r.xp_total;
                    button.style.background = '#2ecc71';
                    button.style.color = '#fff';

                    explicacionBox.textContent = r.explicacion;
                    explicacionBox.classList.remove('hidden');

                    optionButtons.forEach(btn => btn.disabled = true);
                    if (btnNext) btnNext.classList.remove('hidden');
                } else {
                    button.style.background = '#e74c3c';
                    button.style.color = '#fff';
                    button.disabled = true;   // puede intentar con otra opción
                    enviando = false;
                }
            } catch (err) {
                newtonMessage.textContent = 'No se pudo enviar tu respuesta. Intenta de nuevo.';
                enviando = false;
            }
        });
    });
});
