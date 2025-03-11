<?php
session_start(); // Inicia a sessão
session_destroy(); // Destrói a sessão
header("Location: ../public/sign-in/"); // Redireciona para a página de login
exit(); // Encerra o script
?>