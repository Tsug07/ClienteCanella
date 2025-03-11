<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ClienteCanella/public/sign-in/");
    exit();
}

// Verificar se é uma requisição AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Verificar se o ID do funcionário foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'ID do funcionário não fornecido']);
    } else {
        $_SESSION['error'] = 'ID do funcionário não fornecido';
        header('Location: index.php');
    }
    exit();
}

$id = intval($_GET['id']);

// Incluir arquivo de conexão com o banco de dados
require_once '../../includes/database.php';

// Verificar se o funcionário pertence à empresa do usuário logado
$stmt = $conn->prepare("SELECT e.id FROM empregados e 
                        JOIN empresas emp ON e.codigo_empresa = emp.codigo 
                        WHERE e.id = ? AND emp.cnpj = ?");
$stmt->bind_param("is", $id, $_SESSION['cnpj']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Funcionário não encontrado ou não pertence à sua empresa']);
    } else {
        $_SESSION['error'] = 'Funcionário não encontrado ou não pertence à sua empresa';
        header('Location: index.php');
    }
    exit();
}

// Excluir o funcionário
$stmt = $conn->prepare("DELETE FROM empregados WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($isAjax) {
        echo json_encode(['success' => true]);
    } else {
        $_SESSION['success'] = 'Funcionário excluído com sucesso';
        header('Location: index.php');
    }
} else {
    if ($isAjax) {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir funcionário: ' . $conn->error]);
    } else {
        $_SESSION['error'] = 'Erro ao excluir funcionário: ' . $conn->error;
        header('Location: index.php');
    }
}
?>