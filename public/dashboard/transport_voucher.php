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
$page_title = "Vale Transporte | Canella & Santos";
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

// Consulta lista de funcionários da empresa
$stmt = $conn->prepare("SELECT i_empregados, nome FROM empregados WHERE codigo_empresa = (SELECT codigo_empresa FROM empresas WHERE cnpj = ?)");
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$empregados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

function formatarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    return strlen($cnpj) == 14 ? preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj) : $cnpj;
}

// Processamento do formulário (cadastro de vale transporte)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_empregado = $_POST['i_empregados'];
    $valor_vale = $_POST['valor_vale'];
    $data_inicio = $_POST['data_inicio'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO vale_transporte (i_empregados, valor_vale, data_inicio, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $codigo_empregado, $valor_vale, $data_inicio, $status);
    if ($stmt->execute()) {
        $success_message = "Vale Transporte cadastrado com sucesso!";
    } else {
        $error_message = "Erro ao cadastrar o Vale Transporte. Tente novamente.";
    }
}
?>

<div class="dashboard-wrapper">
    <!-- Cabeçalho da Página -->
    <div class="company-header">
        <div class="company-info">
            <h1>Gestão de Vale Transporte</h1>
            <h2><?php echo htmlspecialchars($empresa['razao_social']); ?></h2>
            <p>Cadastre e gerencie os benefícios de transporte dos funcionários.</p>
        </div>
    </div>

    <!-- Botão para abrir o formulário de cadastro -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-success">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-card-content">
                    <h3>Novo Vale Transporte</h3>
                    <p>Cadastrar benefício para um funcionário</p>
                </div>
            </div>
            <div class="stat-card-footer">
                <a href="#" id="open-voucher-form" class="btn">Cadastrar <i class="fas fa-plus"></i></a>
            </div>
        </div>
    </div>

    <!-- Modal para cadastro de Vale Transporte -->
    <div class="modal" id="voucher-modal">
        <div class="employee-modal">
            <div class="modal-header">
                <h3>Cadastrar Novo Vale Transporte</h3>
                <button class="close-btn" id="close-voucher-modal">&times;</button>
            </div>
            <div class="modal-body">
                <?php if (isset($success_message)): ?>
                    <div class="feedback-success">
                        <i class="fas fa-check-circle"></i>
                        <h3><?php echo $success_message; ?></h3>
                    </div>
                <?php elseif (isset($error_message)): ?>
                    <div class="feedback-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3><?php echo $error_message; ?></h3>
                    </div>
                <?php else: ?>
                    <form method="POST" class="tab-content active">
                        <div class="employee-details-grid">
                            <div class="detail-item">
                                <label class="detail-label" for="i_empregados">Funcionário</label>
                                <select name="i_empregados" id="i_empregados" class="detail-value" required>
                                    <option value="">Selecione um funcionário</option>
                                    <?php foreach ($empregados as $empregado): ?>
                                        <option value="<?php echo $empregado['i_empregados']; ?>">
                                            <?php echo htmlspecialchars($empregado['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="detail-item">
                                <label class="detail-label" for="valor_vale">Valor (R$)</label>
                                <input type="number" step="0.01" name="valor_vale" id="valor_vale" class="detail-value" placeholder="Ex.: 200.50" required>
                            </div>
                            <div class="detail-item">
                                <label class="detail-label" for="data_inicio">Data de Início</label>
                                <input type="date" name="data_inicio" id="data_inicio" class="detail-value" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="detail-item">
                                <label class="detail-label" for="status">Status</label>
                                <select name="status" id="status" class="detail-value" required>
                                    <option value="Ativo">Ativo</option>
                                    <option value="Inativo">Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" id="cancel-voucher">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Feedback -->
    <div class="feedback-module" data-page="Vale Transporte">
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

<script>
    const openModalBtn = document.getElementById('open-voucher-form');
    const closeModalBtn = document.getElementById('close-voucher-modal');
    const cancelBtn = document.getElementById('cancel-voucher');
    const modal = document.getElementById('voucher-modal');

    openModalBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.style.display = 'flex';
    });

    closeModalBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>