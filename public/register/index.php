<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$css_files = [
    '../assets/css/auth.css'
];
$js_files = [
    '../assets/js/auth.js'
];
// Definindo o título dinâmico para a página
$page_title = "Registrar | Canella & Santos";

if (isset($_SESSION['error'])) {
    $page_title = "Erro no Registrar || Canella & Santos";
}

include '../../includes/head.php';
?>
<div class="main-container">
    <div class="login-container">
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error animate-fade-in">';
            echo '<span>' . $_SESSION['error'] . '</span>';
            echo '<button class="alert-close" aria-label="Fechar">&times;</button>';
            echo '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success animate-fade-in">';
            echo '<span>' . $_SESSION['success'] . '</span>';
            echo '<button class="alert-close" aria-label="Fechar">&times;</button>';
            echo '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <div class="logo-container">
            <img src="../assets/images/logo_canella.png" alt="Logo Canella & Santos" class="logo">
        </div>

        <div class="login-content">
            <h3 class="login-title">Criar Nova Conta</h3>
            <p class="login-subtitle">Preencha os dados abaixo para se registrar</p>

            <form action="/ClienteCanella/app/auth.php?action=register" method="post" class="login-form">
                <div class="input-group">
                    <label for="cnpj">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" required placeholder="00.000.000/0000-00" class="cnpj-input">
                    <div class="input-underline"></div>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="seu@email.com" class="email-input">
                    <div class="input-underline"></div>
                </div>

                <button type="submit" class="login-button">
                    <span class="button-text">Registrar</span>
                    <div class="button-loader">
                        <div class="loader-dot"></div>
                        <div class="loader-dot"></div>
                        <div class="loader-dot"></div>
                    </div>
                </button>
            </form>

            <div class="login-links">
                <a href="../sign-in/" class="sign-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    Já possui conta? Entrar
                </a>
            </div>
        </div>
    </div>
</div>