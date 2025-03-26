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
$page_title = "Token | Canella & Santos";

if (isset($_SESSION['error'])) {
    $page_title = "Erro no Token || Canella & Santos";
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
            <h3 class="login-title">Verificação de Token</h3>
            <p class="login-subtitle">Insira o token enviado para seu email</p>

            <form action="/ClienteCanella/app/auth.php?action=login" method="post" class="login-form">
                <div class="input-group">
                    <label for="token">Código Token</label>
                    <input type="text" id="token" name="token" required placeholder="XXXXXX" class="token-input">
                    <div class="input-underline"></div>
                </div>

                <button type="submit" class="login-button">
                    <span class="button-text">Verificar Token</span>
                    <div class="button-loader">
                        <div class="loader-dot"></div>
                        <div class="loader-dot"></div>
                        <div class="loader-dot"></div>
                    </div>
                </button>
            </form>

            <div class="login-links">
                <a href="/ClienteCanella/app/auth.php?action=request_token&resend=true" class="sign-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12A10 10 0 0 0 12 2a10 10 0 0 0-5 18.77"></path>
                        <path d="M12 6v6l4 2"></path>
                        <path d="M18 11.66V16a10 10 0 0 1-10-10"></path>
                    </svg>
                    Reenviar Token
                </a>
            </div>
        </div>
    </div>
</div>