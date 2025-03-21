<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit("Usuário não autenticado");
}

require_once '../app/database.php';

$user_id = $_SESSION['user_id'];
$cnpj = $_SESSION['cnpj'];

// Consulta para obter o codigo_empresa a partir do CNPJ
$stmt = $conn->prepare("SELECT codigo_empresa FROM empresas WHERE cnpj = ?");
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

if ($empresa) {
    $codigo_empresa = $empresa['codigo_empresa'];
} else {
    exit("Empresa não encontrada para o CNPJ fornecido.");
}

// Define $pagina a partir do POST
$pagina = $_POST['pagina'] ?? 'desconhecida';


// Validação de $resposta
$resposta = $_POST['response'] ?? null;
if (!in_array($resposta, ['Sim', 'Não'])) {
    exit("Valor inválido para 'resposta'. Esperado: 'Sim' ou 'Não'. Recebido: " . htmlspecialchars($resposta ?? 'nulo'));
}

$comentario = !empty($_POST['comment']) ? $_POST['comment'] : null;

// Insere o feedback
$stmt = $conn->prepare("INSERT INTO feedback (user_id, codigo_empresa, pagina, resposta, comentario) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisss", $user_id, $codigo_empresa, $pagina, $resposta, $comentario);

if ($stmt->execute()) {
    echo "Feedback registrado!";
} else {
    echo "Erro ao registrar feedback: " . $conn->error;
}
$stmt->close();
?>