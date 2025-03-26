document.addEventListener('DOMContentLoaded', function() {
    // Fechar alertas ao clicar no botão
    document.querySelectorAll('.alert-close').forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            alert.classList.add('fade-out');
            
            // Remover completamente o alerta após a animação
            setTimeout(() => {
                alert.remove();
            }, 300);
        });
    });
    
    // Fechar alertas automaticamente após 5 segundos (opcional)
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
});