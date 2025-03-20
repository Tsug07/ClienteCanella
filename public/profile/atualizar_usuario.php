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
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Validação básica
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Email inválido.";
    header("Location: /ClienteCanella/public/profile/");
    exit();
}
    
    // Verificar se o email já está em uso por outro usuário
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Este email já está sendo utilizado por outro usuário.";
        header("Location: index.php");
        exit();
    }
    
    // Atualizar dados do usuário
    $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
    $stmt->bind_param("si", $email, $user_id);
    
    if ($stmt->execute()) {
        // Registrar atividade
        registrar_atividade($user_id, 'perfil_atualizado', 'Email de usuário atualizado', $conn);
        
        $_SESSION['success'] = "Suas informações foram atualizadas com sucesso!";
    } else {
        $_SESSION['error'] = "Erro ao atualizar suas informações. Por favor, tente novamente.";
    }
    
    header("Location: /ClienteCanella/public/profile/");
    exit();
}

header("Location: /ClienteCanella/public/profile/");
exit();
?>