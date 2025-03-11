<?php
// Incluir o cabeçalho padrão
include '../../includes/header.php';
?>

<!-- Início do conteúdo da página -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Novo Funcionário</h1>
            <form action="../../includes/funcionario.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="form-group
                ">
                    <label for="email">E-mail:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group
                ">
                    <label for="senha">Senha:</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>