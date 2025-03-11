
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

   session_start();
<<<<<<< HEAD
include '../../includes/head.php'; 
=======
include '../../includes/head.php';
>>>>>>> 8a5c728 (CI/CD Config Commit)
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
        <h3>Cadastrar</h3>
        <form action="/ClienteCanella/app/auth.php?action=register" method="post">
        <label for="cnpj">CNPJ:</label>
            <input type="text" id="cnpj" name="cnpj" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit">Registrar</button>
        </form>
        <p class="auth-login"><a class="sign-link" href="../sign-in/">Já possui conta? Entrar</a></p>
    </div>
    </div>
    <!-- <?php include '../includes/footer.php'; ?> -->