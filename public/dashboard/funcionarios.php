<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ClienteCanella/public/sign-in/");
    exit();
}

// Definindo o título dinâmico para a página
$css_files = [
    '../assets/css/feedback.css',
    '../assets/css/dashboard.css'
];
$page_title = "Dashboard | Canella & Santos"; // Título padrão para a página de login

// Caso haja uma mensagem de erro, você pode alterar o título para algo como "Erro ao fazer login"
if (isset($_SESSION['error'])) {
    $page_title = "Erro no Dashboard || Canella & Santos";
}

include '../../includes/head.php';

// Incluir arquivo de conexão com o banco de dados
require_once '../../app/database.php';
// Incluir o cabeçalho padrão
include '../../includes/header.php';

// Consulta informações da empresa
$cnpj = $_SESSION['cnpj'];
$stmt = $conn->prepare("SELECT codigo_empresa, razao_social, cnpj FROM empresas WHERE cnpj = ?");
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

// Se não encontrar a empresa, define valores padrão
if (!$empresa) {
    $empresa = [
        'codigo_empresa' => 0,
        'razao_social' => 'Empresa não encontrada',
        'cnpj' => $cnpj
    ];
}

// Verificar se há um termo de busca
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Construir a consulta base
$totalFixosQuery = "SELECT COUNT(*) as total FROM empregados WHERE codigo_empresa = ?";
$countQuery = "SELECT COUNT(*) as total FROM empregados WHERE codigo_empresa = ?";
$employeesQuery = "SELECT i_empregados, nome, cpf FROM empregados WHERE codigo_empresa = ?";

// Parâmetros para a consulta
$params = [$empresa['codigo_empresa']];
$types = "i";

// Consultar o total fixo de empregados (sem filtro de busca)
$stmtTotalFixos = $conn->prepare($totalFixosQuery);
$stmtTotalFixos->bind_param($types, ...$params);
$stmtTotalFixos->execute();
$resultTotalFixos = $stmtTotalFixos->get_result();
$totalEmpregadosFixos = $resultTotalFixos->fetch_assoc()['total'];

// Adicionar filtro de busca se necessário
if (!empty($searchTerm)) {
    $searchWildcard = "%{$searchTerm}%";
    $countQuery .= " AND (nome LIKE ? OR cpf LIKE ?)";
    $employeesQuery .= " AND (nome LIKE ? OR cpf LIKE ?)";
    $params[] = $searchWildcard;
    $params[] = $searchWildcard;
    $types .= "ss";
}

// Consultar o total de empregados (para paginação)
$stmt = $conn->prepare($countQuery);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$totalEmployees = $result->fetch_assoc()['total'];

// Configurações de paginação
$itemsPerPage = 10; // Número de funcionários por página
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalPages = ceil($totalEmployees / $itemsPerPage);

// Ajustar página atual se exceder o total de páginas
if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}

// Calcular offset para a consulta SQL
$offset = ($currentPage - 1) * $itemsPerPage;

// Consultar os empregados para a página atual
$employeesQuery .= " ORDER BY nome LIMIT ? OFFSET ?";
$params[] = $itemsPerPage;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($employeesQuery);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$empregados = [];
while ($row = $result->fetch_assoc()) {
    $empregados[] = $row;
}

// Calcular índices para a mensagem de exibição
$startIndex = ($currentPage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalEmployees - 1);

// Formatação do CNPJ para exibição
function formatarCNPJ($cnpj)
{
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj); // Remove caracteres não numéricos
    if (strlen($cnpj) != 14) return $cnpj; // Retorna sem formatação se não tiver 14 dígitos
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj);
}

?>

<div class="dashboard-wrapper">
    <!-- Cabeçalho da Empresa -->
    <div class="company-header">
        <div class="company-info">
            <h1>Área do Cliente</h1>
            <h2><?php echo htmlspecialchars($empresa['razao_social']); ?></h2>
            <p>CNPJ: <?php echo formatarCNPJ($empresa['cnpj']); ?></p>
        </div>

    </div>

    <!-- Cards de Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-content">
                    <h3>Total de Funcionários</h3>
                    <p><?php echo $totalEmpregadosFixos; ?></p>
                </div>
            </div>
            <!-- <div class="stat-card-footer">
                <a href="novo_funcionario.php">
                    Adicionar funcionário
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div> -->
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-success">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-card-content">
                    <h3>Vale Transporte</h3>
                    <p>Gestão</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <a href="transport_voucher.php">
                    Cadastrar vale transporte
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-accent">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-card-content">
                    <h3>Relatórios</h3>
                    <p>Exportar dados</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <a href="#" id="exportButton">
                    Gerar relatório
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Lista de Funcionários -->
    <div class="main-content">
        <div class="content-header">
            <h2>Funcionários</h2>
            <div class="content-actions">
                <div class="search-bar">
                <i class="fas fa-magnifying-glass"></i>
                    <input type="text" id="searchEmployee" placeholder="Buscar funcionário...">
                </div>
                <!-- <a href="novo_funcionario.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Novo
            </a> -->
            </div>
        </div>

        <?php if ($totalEmployees > 0): ?>
            <div class="responsive-table">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Visualizar</th>
                        </tr>
                    </thead>
                    <tbody id="employeeTableBody">
                        <?php foreach ($empregados as $empregado): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($empregado['nome']); ?></td>
                                <td><?php echo htmlspecialchars($empregado['cpf']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view-btn" title="Visualizar"
                                            onclick="showEmployeePopup(<?php echo $empregado['i_empregados']; ?>, '<?php echo htmlspecialchars($empregado['nome']); ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!-- <a href="editar_empregado.php?id=<?php echo $empregado['i_empregados']; ?>" class="action-btn edit-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="action-btn delete-btn" title="Excluir" 
                                            onclick="showDeleteModal(<?php echo $empregado['i_empregados']; ?>, '<?php echo htmlspecialchars($empregado['nome']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button> -->
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="pagination-container">
                <div class="pagination-info">
                    Mostrando <?php echo $startIndex + 1; ?> a <?php echo min($endIndex + 1, $totalEmployees); ?> de <?php echo $totalEmployees; ?> funcionários
                </div>
                <div class="pagination">
                    <!-- Botão para primeira página -->
                    <a href="?page=1<?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="pagination-btn <?php echo $currentPage == 1 ? 'disabled' : ''; ?>" title="Primeira página">
                        <i class="fas fa-angle-double-left"></i>
                    </a>

                    <!-- Botão para página anterior -->
                    <a href="?page=<?php echo max(1, $currentPage - 1); ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="pagination-btn <?php echo $currentPage == 1 ? 'disabled' : ''; ?>" title="Página anterior">
                        <i class="fas fa-angle-left"></i>
                    </a>

                    <!-- Números das páginas -->
                    <?php
                    $startPage = max(1, min($currentPage - 2, $totalPages - 4));
                    $endPage = min($startPage + 4, $totalPages);

                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="pagination-btn <?php echo $currentPage == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Botão para próxima página -->
                    <a href="?page=<?php echo min($totalPages, $currentPage + 1); ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="pagination-btn <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>" title="Próxima página">
                        <i class="fas fa-angle-right"></i>
                    </a>

                    <!-- Botão para última página -->
                    <a href="?page=<?php echo $totalPages; ?><?php echo !empty($searchTerm) ? '&search=' . urlencode($searchTerm) : ''; ?>" class="pagination-btn <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>" title="Última página">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Nenhum funcionário cadastrado</h3>
            </div>
        <?php endif; ?>
    </div>

    <!-- Feedback -->
    <div class="feedback-module" data-page="Funcionários">
    <div class="feedback-wrapper">
        <div class="feedback-main">
            <div class="feedback-header">
                <h3>Avaliação de Experiência</h3>
                <p>Esta página foi útil para você?</p>
            </div>
            <div class="feedback-body">
                <div class="feedback-buttons">
                    <div class="feedback-btn yes" data-response="Sim">
                        <i class="fas fa-thumbs-up"></i><span>Sim</span>
                    </div>
                    <div class="feedback-btn no" data-response="Não">
                        <i class="fas fa-thumbs-down"></i><span>Não</span>
                    </div>
                </div>
                <div class="feedback-comment">
                    <label for="feedback-text">Sua opinião é importante. Conte-nos mais:</label>
                    <textarea class="feedback-text" placeholder="Compartilhe seus comentários..."></textarea>
                    <div class="feedback-actions">
                        <button class="cancel-feedback btn btn-outline">Cancelar</button>
                        <button class="submit-feedback btn btn-primary">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="feedback-success">
            <i class="fas fa-check-circle"></i>
            <h3>Obrigado pelo seu feedback!</h3>
            <p>Sua opinião nos ajuda a melhorar.</p>
        </div>
    </div>
</div>

</div>

<!-- Modal de Visualização de Funcionário -->
<div id="employeePopup" class="modal">
    <div class="modal-content employee-modal">
        <div class="modal-header">
            <h3 id="popupEmployeeName">Informações do Funcionário</h3>
            <button class="close-btn" onclick="closeEmployeePopup()">✕</button>
        </div>
        <div class="modal-body">
            <div id="employeeDetails" class="loading">
                <div class="loader-container">
                    <div class="loader"></div>
                    <p>Carregando informações...</p>
                </div>
            </div>

            <div id="employeeData" class="employee-data-container" style="display: none;">
                <!-- Cabeçalho -->
                <div class="employee-section employee-header-section">
                    <div class="employee-avatar">
                        <div class="avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="employee-header-info">
                        <h4 id="employeeHeaderName">-</h4>
                        <div class="employee-id-badges">
                            <span class="badge badge-cpf" id="employeeHeaderCpf">-</span>
                            <span class="badge badge-code" id="employeeEsocialCode">-</span>
                        </div>
                    </div>
                </div>

                <!-- Abas de informações -->
                <div class="employee-tabs">
                    <button class="tab-btn active" data-tab="personal">Dados Pessoais</button>
                    <button class="tab-btn" data-tab="contract">Contrato</button>
                    <button class="tab-btn" data-tab="contact">Contato</button>
                    <button class="tab-btn" data-tab="benefits">Benefícios</button>
                </div>

                <!-- Conteúdo das abas -->
                <div class="tabs-content">
                    <!-- Tab: Dados Pessoais -->
                    <div class="tab-content active" id="tab-personal">
                        <div class="employee-details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Nome Completo</span>
                                <span class="detail-value" id="employeeName">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">CPF</span>
                                <span class="detail-value" id="employeeCpf">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Data de Nascimento</span>
                                <span class="detail-value" id="employeeBirthdate">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Sexo</span>
                                <span class="detail-value" id="employeeGender">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Estado Civil</span>
                                <span class="detail-value" id="employeeMaritalStatus">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Nacionalidade</span>
                                <span class="detail-value" id="employeeNationality">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Contrato -->
                    <div class="tab-content" id="tab-contract">
                        <div class="employee-details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Data de Admissão</span>
                                <span class="detail-value" id="employeeAdmission">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Cargo</span>
                                <span class="detail-value" id="employeePosition">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Salário</span>
                                <span class="detail-value" id="employeeSalary">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Horas/Mês</span>
                                <span class="detail-value" id="employeeHours">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Vencimento de Férias</span>
                                <span class="detail-value" id="employeeVacation">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Tipo de Contrato</span>
                                <span class="detail-value" id="employeeContractType">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Contato -->
                    <div class="tab-content" id="tab-contact">
                        <div class="employee-details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Telefone</span>
                                <span class="detail-value" id="employeePhone">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email</span>
                                <span class="detail-value" id="employeeEmail">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Endereço</span>
                                <span class="detail-value" id="employeeAddress">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Cidade/Estado</span>
                                <span class="detail-value" id="employeeCityState">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">CEP</span>
                                <span class="detail-value" id="employeeZipCode">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Benefícios -->
                    <div class="tab-content" id="tab-benefits">
                        <div class="employee-details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Plano de Saúde</span>
                                <span class="detail-value" id="employeeHealthPlan">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Contribuição Sindical</span>
                                <span class="detail-value" id="employeeUnion">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Taxas Autorizadas</span>
                                <span class="detail-value" id="employeeFees">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Vale Transporte</span>
                                <span class="detail-value" id="employeeTransport">-</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Vale Refeição</span>
                                <span class="detail-value" id="employeeFood">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeEmployeePopup()">Fechar</button>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>