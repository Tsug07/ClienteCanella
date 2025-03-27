<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit();
}

// Incluir arquivo de conexão com o banco de dados
require_once '../app/database.php';

// Obter o código da empresa da sessão
$cnpj = $_SESSION['cnpj'];
$stmt = $conn->prepare("SELECT codigo_empresa FROM empresas WHERE cnpj = ?");
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();
$codigo_empresa = $empresa['codigo_empresa'] ?? 0;

// Parâmetros da requisição
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 10;

// Construir a consulta
$countQuery = "SELECT COUNT(*) as total FROM empregados WHERE codigo_empresa = ?";
$employeesQuery = "SELECT i_empregados, nome, cpf FROM empregados WHERE codigo_empresa = ?";
$params = [$codigo_empresa];
$types = "i";

if (!empty($searchTerm)) {
    $searchWildcard = "%{$searchTerm}%";
    $countQuery .= " AND (nome LIKE ? OR cpf LIKE ?)";
    $employeesQuery .= " AND (nome LIKE ? OR cpf LIKE ?)";
    $params[] = $searchWildcard;
    $params[] = $searchWildcard;
    $types .= "ss";
}

// Consultar o total de empregados
$stmt = $conn->prepare($countQuery);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$totalEmployees = $result->fetch_assoc()['total'];

// Calcular paginação
$totalPages = ceil($totalEmployees / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;

// Consultar os empregados
$employeesQuery .= " ORDER BY nome LIMIT ? OFFSET ?";
$params[] = $itemsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($employeesQuery);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = [
        'i_empregados' => $row['i_empregados'],
        'nome' => htmlspecialchars($row['nome']),
        'cpf' => htmlspecialchars($row['cpf'])
    ];
}

// Calcular índices para exibição
$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalEmployees - 1);

// Retornar os dados em JSON
echo json_encode([
    'employees' => $employees,
    'totalEmployees' => $totalEmployees,
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'startIndex' => $startIndex,
    'endIndex' => $endIndex,
    'searchTerm' => $searchTerm
]);
?>