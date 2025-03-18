<?php
// Limpar qualquer saída anterior
ob_start();
header('Content-Type: application/json');

// Iniciar a sessão
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Incluir o arquivo de conexão
require_once '../app/database.php';

// Pegar o ID do funcionário e o CNPJ da sessão
$employeeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cnpj = $_SESSION['cnpj'];

if ($employeeId <= 0) {
    echo json_encode(['error' => 'ID de funcionário inválido']);
    exit;
}

// Consulta ao banco MariaDB
$stmt = $conn->prepare("
    SELECT e.nome, e.cpf, e.admissao, e.salario, e.horas_mes, e.venc_ferias, data_nascimento, sexo, estado_civil, e.cep, cargos, nacionalidade,
           e.email, e.fone, e.endereco, e.cidade, e.estado, e.plano_saude_optantes, CODIGO_ESOCIAL
    FROM empregados e
    JOIN empresas emp ON e.codigo_empresa = emp.codigo_empresa
    WHERE e.i_empregados = ? AND emp.cnpj = ?
");
if (!$stmt) {
    echo json_encode(['error' => 'Erro na preparação da consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param("is", $employeeId, $cnpj);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Erro na execução da consulta: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    // Garantir que os dados sejam codificados como JSON válido
    echo json_encode($data, JSON_NUMERIC_CHECK);
} else {
    echo json_encode(['error' => 'Funcionário não encontrado ou não pertence à sua empresa']);
}

// Fechar a conexão
$stmt->close();
$conn->close();

// Descartar qualquer saída indesejada antes do JSON
ob_end_flush();
?>