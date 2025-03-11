<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

      session_start();
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
        <h2>Inserir Token</h2>
        
        <form action="/ClienteCanella/app/auth.php?action=login" method="post">
            <label for="token">Token:</label>
            <input type="text" id="token" name="token" required>
            <button type="submit">Entrar</button>
        </form>
        <!-- Link para reenviar o token -->
        <p class="resend-token-link">
            Não recebeu o token? <a href="/ClienteCanella/app/auth.php?action=request_token&resend=true">Clique aqui para reenviar</a>.
        </p>
        </div>
    </div>
