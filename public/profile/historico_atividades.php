<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ClienteCanella/public/sign-in/");
    exit();
}

// Incluir arquivo de conexão com o banco de dados
require_once '../../app/database.php';
require_once 'activity_logger.php';
require_once '../utils.php';

// Consulta para paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itens_por_pagina = 15;
$offset = ($page - 1) * $itens_por_pagina;

// Consulta para contar o total de registros
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM log_atividades WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$total_registros = $result->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $itens_por_pagina);

// Consulta para obter os registros da página atual
$stmt = $conn->prepare("
    SELECT id, tipo_atividade, descricao, ip, user_agent, data_hora 
    FROM log_atividades 
    WHERE user_id = ? 
    ORDER BY data_hora DESC 
    LIMIT ?, ?
");
$stmt->bind_param("iii", $_SESSION['user_id'], $offset, $itens_por_pagina);
$stmt->execute();
$result = $stmt->get_result();
$atividades = [];

while ($row = $result->fetch_assoc()) {
    $atividades[] = $row;
}

// Incluir o cabeçalho padrão
include '../../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Cabeçalho da página -->
    <div class="page-header">
        <div>
            <h1>Histórico de Atividades</h1>
            <p>Visualize todas as suas atividades registradas no sistema</p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar ao Perfil
            </a>
        </div>
    </div>

    <!-- Conteúdo principal -->
    <div class="card">
        <div class="card-body">
            <div class="activity-full-log">
                <?php if (empty($atividades)): ?>
                    <div class="activity-empty">
                        <i class="fas fa-info-circle"></i>
                        <p>Nenhuma atividade registrada ainda.</p>
                    </div>
                <?php else: ?>
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Data e Hora</th>
                                <th>Atividade</th>
                                <th>Descrição</th>
                                <th>Endereço IP</th>
                                <th>Navegador</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($atividades as $atividade): ?>
                                <tr>
                                    <td><?php echo formatarData($atividade['data_hora']); ?></td>
                                    <td>
                                        <div class="activity-type">
                                            <i class="<?php echo obter_icone_atividade($atividade['tipo_atividade']); ?>"></i>
                                            <span><?php echo formatar_titulo_atividade($atividade['tipo_atividade']); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($atividade['descricao']); ?></td>
                                    <td><?php echo htmlspecialchars($atividade['ip']); ?></td>
                                    <td><?php echo formatar_navegador($atividade['user_agent']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Paginação -->
                    <?php if ($total_paginas > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>" class="pagination-item">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php 
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_paginas, $start_page + 4);
                            
                            if ($start_page > 1): ?>
                                <a href="?page=1" class="pagination-item">1</a>
                                <?php if ($start_page > 2): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" class="pagination-item <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                            <?php endfor; ?>
                            
                            <?php 
                            if ($end_page < $total_paginas): 
                                if ($end_page < $total_paginas - 1): ?>
                                    <span class="pagination-ellipsis">...</span>
                                <?php endif; ?>
                                <a href="?page=<?php echo $total_paginas; ?>" class="pagination-item"><?php echo $total_paginas; ?></a>
                            <?php endif; ?>
                            
                            <?php if ($page < $total_paginas): ?>
                                <a href="?page=<?php echo $page + 1; ?>" class="pagination-item">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.activity-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

.activity-table th, 
.activity-table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

.activity-table th {
    background-color: #f9f9f9;
    font-weight: 500;
    color: var(--dark-color);
}

.activity-table tbody tr:hover {
    background-color: #f5f7f9;
}

.activity-type {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.activity-type i {
    color: var(--primary-color);
}

.activity-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 0;
    color: var(--gray-color);
    text-align: center;
}

.activity-empty i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.pagination-item {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 4px;
    background-color: #f5f7f9;
    color: var(--dark-color);
    text-decoration: none;
    transition: all 0.2s;
}

.pagination-item:hover {
    background-color: #e9f1fe;
    color: var(--primary-color);
}

.pagination-item.active {
    background-color: var(--primary-color);
    color: white;
}

.pagination-ellipsis {
    padding: 0 0.5rem;
    color: var(--gray-color);
}

@media (max-width: 768px) {
    .activity-table {
        display: block;
        overflow-x: auto;
    }
}

@media (max-width: 576px) {
    .pagination {
        flex-wrap: wrap;
    }
}
</style>

<?php include '../../includes/footer.php'; ?>