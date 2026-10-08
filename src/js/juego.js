document.addEventListener('DOMContentLoaded', () => {
    const btnHelp = document.getElementById('btn-help');
    const btnCloseHelp = document.getElementById('btn-close-help');
    const modalHelp = document.getElementById('modal-help');
    const optionButtons = document.querySelectorAll('.btn-option');
    const newtonMessage = document.getElementById('newton-message');
    const scoreDisplay = document.getElementById('score');
    const btnNext = document.getElementById('btn-next');

    // Abrir modal
    if (btnHelp) {
        btnHelp.addEventListener('click', () => {
            modalHelp.classList.remove('hidden');
        });
    }

    // Cerrar modal
    if (btnCloseHelp) {
        btnCloseHelp.addEventListener('click', () => {
            modalHelp.classList.add('hidden');
        });
    }

    // Validar respuestas
    optionButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const selectedOption = e.target.getAttribute('data-option');

            if (selectedOption === RESPUESTA_CORRECTA) {
                let actualXP = parseInt(scoreDisplay.textContent) + PUNTOS_RECOMPENSA;
                scoreDisplay.textContent = actualXP;
                
                // Poner verde el botón seleccionado
                e.target.style.background = "#2ecc71";
                e.target.style.color = "#fff";
                
                newtonMessage.innerHTML = `"¡Excelente razonamiento! Has acertado. Ganaste +${PUNTOS_RECOMPENSA} XP."`;
                
                // Bloquear las opciones para múltiples clics
                optionButtons.forEach(btn => btn.disabled = true);

                // botón de Siguiente
                if (btnNext) {
                    btnNext.classList.remove('hidden');
                }

            } else {
                //  botón incorrecto
                e.target.style.background = "#e74c3c";
                e.target.style.color = "#fff";
                
                newtonMessage.innerHTML = `"¡Cerca, pero no! Revisa bien tu cálculo. Si tienes dudas, presiona <strong>APRENDE A ESTUDIAR</strong>."`;
            }
        });
    });
});