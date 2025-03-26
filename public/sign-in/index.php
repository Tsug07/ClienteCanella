<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Definindo os arquivos CSS específicos para essa página
$css_files = [
    '../assets/css/auth.css'
];
$js_files = [
    '../assets/js/auth.js'
];
$page_title = "Login | Canella & Santos";
if (isset($_SESSION['error'])) {
    $page_title = "Erro no Login || Canella & Santos";
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
            <h3 class="login-title">Acessar Área do Cliente</h3>
            <p class="login-subtitle">Informe seu CNPJ para continuar</p>

            <form action="/ClienteCanella/app/auth.php?action=request_token" method="post" class="login-form">
                <div class="input-group">
                    <label for="cnpj">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" required placeholder="00.000.000/0000-00" class="cnpj-input">
                    <div class="input-underline"></div>
                </div>
                <button type="submit" class="login-button">
                    <span class="button-text">Solicitar Token</span>
                    <div class="button-loader">
                        <div class="loader-dot"></div>
                        <div class="loader-dot"></div>
                        <div class="loader-dot"></div>
                    </div>
                </button>
            </form>

            <div class="login-links">
                <a href="../register/" class="sign-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    Não possui conta? Cadastrar
                </a>
            </div>
        </div>
    </div>
</div>