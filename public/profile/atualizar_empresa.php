<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ClienteCanella/public/sign-in/");
    exit();
}

// Incluir arquivo de conexão com o banco de dados
require_once '../../app/database.php';
require_once 'activity_logger.php';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Método de requisição inválido.";
    header("Location: /ClienteCanella/public/profile/");
    exit();
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados do formulário
    $user_id = $_SESSION['user_id'];
    $cnpj = $_SESSION['cnpj'];
    $razao_social = filter_input(INPUT_POST, 'razao_social', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
    $inscricao_estadual = filter_input(INPUT_POST, 'inscricao_estadual', FILTER_SANITIZE_STRING);
    $inscricao_municipal = filter_input(INPUT_POST, 'inscricao_municipal', FILTER_SANITIZE_STRING);
    $regime_tributario = filter_input(INPUT_POST, 'regime_tributario', FILTER_SANITIZE_STRING);
    
    // Validação básica
if (empty($razao_social)) {
    $_SESSION['error'] = "A razão social é obrigatória.";
    header("Location: /ClienteCanella/public/profile/");
    exit();
}
    
    // Remover formatação do CEP
    $cep = preg_replace('/[^0-9]/', '', $cep);
    
    // Atualizar dados da empresa
    $stmt = $conn->prepare("
        UPDATE empresas 
        SET razao_social = ?, email = ?, telefone = ?, endereco = ?, cidade = ?, 
            estado = ?, cep = ?, inscricao_estadual = ?, inscricao_municipal = ?, 
            regime_tributario = ? 
        WHERE cnpj = ?
    ");
    
    $stmt->bind_param(
        "sssssssssss", 
        $razao_social, 
        $email, 
        $telefone, 
        $endereco, 
        $cidade, 
        $estado, 
        $cep, 
        $inscricao_estadual, 
        $inscricao_municipal, 
        $regime_tributario, 
        $cnpj
    );
    
    if ($stmt->execute()) {
        // Registrar atividade
        registrar_atividade($user_id, 'empresa_atualizada', 'Dados da empresa atualizados', $conn);
        
        $_SESSION['success'] = "Dados da empresa atualizados com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao atualizar os dados da empresa. Por favor, tente novamente.";
    }
    
    header("Location: index.php");
    exit();
}

header("Location: /ClienteCanella/public/profile/");
exit();
?>