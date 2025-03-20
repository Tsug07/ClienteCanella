<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Definindo o título dinâmico para a página
$page_title = "Login | Canella & Santos"; // Título padrão para a página de login

// Caso haja uma mensagem de erro, você pode alterar o título para algo como "Erro ao fazer login"
if (isset($_SESSION['error'])) {
    $page_title = "Erro no Login || Canella & Santos";
}

include '../../includes/head.php';
?>
<div class="main-container">

    <div class="login-container">
        <?php

        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        <img src="../assets/images/logo_canella.png" alt="Logo Canella & Santos" class="logo">
        <h3>Acessar Área do Cliente</h3>

        <form action="/ClienteCanella/app/auth.php?action=request_token" method="post">
            <label for="cnpj">CNPJ:</label>
            <input type="text" id="cnpj" name="cnpj" required>
            <button type="submit">Solicitar Token</button>
        </form>
        <!-- <p class="auth-login"><a class="sign-link" href="../forget-password/">Esqueceu a Senha?</a></p> -->
        <p class="auth-login"><a class="sign-link" href="../register/">Não possui conta? Cadastrar</a></p>
    </div>
</div>