function preventDoubleClick(buttonId) {
    let button = document.getElementById(buttonId);
    
    if (!button) {
        console.error(`Botão com ID "${buttonId}" não encontrado.`);
        return;
    }

    let form = button.closest("form"); // Encontra o formulário mais próximo

    if (!form) {
        console.error(`Botão com ID "${buttonId}" não está dentro de um formulário.`);
        return;
    }

    form.addEventListener("submit", function () {
        button.disabled = true;
        let originalText = button.innerHTML;
        button.innerHTML = "Processando...";

        setTimeout(function () {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 3000);
    });
}
