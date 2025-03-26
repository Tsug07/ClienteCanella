<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ClienteCanella/public/sign-in/");
    exit();
}
$css_files = [
    '../assets/css/feedback.css',
    '../assets/css/dashboard.css'
];
$page_title = "Home | Canella & Santos";
include '../../includes/head.php';
require_once '../../app/database.php';
include '../../includes/header.php';

// Consulta informações da empresa
$cnpj = $_SESSION['cnpj'];
$stmt = $conn->prepare("SELECT razao_social, cnpj FROM empresas WHERE cnpj = ?");
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc() ?: ['razao_social' => 'Empresa não encontrada', 'cnpj' => $cnpj];


// Total de funcionários
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM empregados WHERE codigo_empresa = (SELECT codigo_empresa FROM empresas WHERE cnpj = ?)");
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$totalEmpregados = $stmt->get_result()->fetch_assoc()['total'];

function formatarCNPJ($cnpj)
{
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    return strlen($cnpj) == 14 ? preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj) : $cnpj;
}
?>

<div class="dashboard-wrapper">
    <!-- Cabeçalho da Empresa -->
    <div class="company-header">
        <div class="company-info">
            <h1>Bem-vindo(a), <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Cliente'); ?>!</h1>
            <h2><?php echo htmlspecialchars($empresa['razao_social']); ?></h2>
            <p>Estamos felizes em tê-lo(a) na Área do Cliente Canella & Santos.</p>
        </div>
    </div>

    <!-- Cards de Navegação -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-content">
                    <h3>Total de Funcionários</h3>
                    <p><?php echo $totalEmpregados; ?></p>
                </div>
            </div>
            <div class="stat-card-footer">
                <a href="funcionarios.php">Ver detalhes <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-success">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-card-content">
                    <h3>Vale Transporte</h3>
                    <p>Gestão de Benefícios</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <a href="transport_voucher.php">Acessar <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-accent">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-card-content">
                    <h3>Relatórios</h3>
                    <p>Exportar Dados</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <a href="relatorios.php">Gerar relatório <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>



    <!-- Feedback -->
    <div class="feedback-module" data-page="Home">
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

<?php include '../../includes/footer.php'; ?>