// Bootstrap 5.3.3: cada FocusTrap usa o mesmo namespace de eventos no document.
// Desativar o foco automático evita que a última transição a terminar roube o foco.
function abrirModaisProdutos(modalIds) {
    const elementos = modalIds.map(id => document.querySelector(id)).filter(Boolean);
    const prontos = new Set();
    const pendentes = new Set(elementos);
    const instancias = elementos.map(elemento => new bootstrap.Modal(elemento, { focus: false }));
    const avisados = new Set();

    function focusTrap(indice) {
        // API interna do Bootstrap 5.3.3: listeners globais e flag _isActive por trap.
        const trap = instancias[indice]?._focustrap;
        if (trap && typeof trap.activate === 'function' && typeof trap.deactivate === 'function') {
            return trap;
        }
        if (!avisados.has(indice)) {
            console.warn('SIAPRA: FocusTrap indisponível; confira a instância e a versão do Bootstrap.', elementos[indice]);
            avisados.add(indice);
        }
        return null;
    }

    function podeRestaurarFoco(campo, modal) {
        return campo && campo !== modal && campo.isConnected && modal.contains(campo)
            && typeof campo.focus === 'function' && !campo.matches(':disabled')
            && !campo.closest('[inert], [hidden], [aria-hidden="true"]')
            && campo.getClientRects().length > 0 && getComputedStyle(campo).visibility === 'visible'
            && campo.matches('input:not([type="hidden"]), select, textarea, button, a[href], area[href], [tabindex], [contenteditable]');
    }

    function sincronizarFoco() {
        const abertos = elementos.filter(elemento => elemento.isConnected && elemento.classList.contains('show'));
        const candidatos = elementos.filter(elemento => elemento.isConnected && (abertos.includes(elemento) || pendentes.has(elemento)));
        const superior = candidatos[candidatos.length - 1];
        // Desativar inclusive o superior antes de reativá-lo: outro deactivate() pode
        // ter removido seus listeners globais mesmo com sua flag ainda ativa.
        const traps = elementos.map((elemento, indice) => focusTrap(indice));
        traps.forEach(trap => trap?.deactivate());
        elementos.forEach((elemento, indice) => {
            const inferior = elemento !== superior;
            elemento.inert = inferior && abertos.includes(elemento);
            if (inferior) {
                if (abertos.includes(elemento)) {
                    elemento.removeAttribute('aria-modal');
                    elemento.setAttribute('aria-hidden', 'true');
                }
            }
        });
        if (superior && prontos.has(superior)) {
            superior.removeAttribute('aria-hidden');
            superior.setAttribute('aria-modal', 'true');
            const anterior = document.activeElement;
            const restaurar = podeRestaurarFoco(anterior, superior);
            const trap = traps[elementos.indexOf(superior)];
            if (trap) {
                trap.activate();
                // Autofocus do Bootstrap não deve interromper interação já iniciada.
                if (restaurar && podeRestaurarFoco(anterior, superior)) {
                    anterior.focus({ preventScroll: true });
                }
            }
        }
    }

    elementos.forEach((elemento, indice) => {
        elemento.style.zIndex = 1051 - (elementos.length - 1 - indice) * 10;
        elemento.addEventListener('show.bs.modal', () => {
            pendentes.add(elemento);
            prontos.delete(elemento);
            sincronizarFoco();
        });
        elemento.addEventListener('shown.bs.modal', () => {
            pendentes.delete(elemento);
            prontos.add(elemento);
            sincronizarFoco();
        });
        elemento.addEventListener('hidden.bs.modal', () => {
            prontos.delete(elemento);
            pendentes.delete(elemento);
            elemento.inert = false;
            // Bootstrap restaura o body ao fechar qualquer modal, mesmo havendo outro.
            const abertos = elementos.filter(item => item.classList.contains('show'));
            if (abertos.length) {
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
                abertos.forEach((item, posicao) => {
                    item.style.zIndex = 1051 - (abertos.length - 1 - posicao) * 10;
                });
            }
            sincronizarFoco();
        });
        instancias[indice].show();
    });
}
