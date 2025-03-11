<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ClienteCanella/public/sign-in/");
    exit();
}

// Incluir arquivo de conexão com o banco de dados
require_once '../../app/database.php';

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

// Consulta contagem de empregados
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM empregados WHERE codigo_empresa = ?");
$stmt->bind_param("i", $empresa['codigo_empresa']);
$stmt->execute();
$result = $stmt->get_result();
$totalEmpregados = $result->fetch_assoc()['total'];

// Consulta lista de empregados
$stmt = $conn->prepare("SELECT codigo_empresa, nome, cpf, i_empregados FROM empregados WHERE codigo_empresa = ? ORDER BY nome");
$stmt->bind_param("i", $empresa['codigo_empresa']);
$stmt->execute();
$result = $stmt->get_result();
$empregados = [];
while ($row = $result->fetch_assoc()) {
    $empregados[] = $row;
}

// Formatação do CNPJ para exibição
function formatarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj); // Remove caracteres não numéricos
    if (strlen($cnpj) != 14) return $cnpj; // Retorna sem formatação se não tiver 14 dígitos
    return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "$1.$2.$3/$4-$5", $cnpj);
}

// Incluir o cabeçalho padrão
include '../../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Cabeçalho da Empresa -->
    <div class="company-header">
        <div class="company-info">
            <h1>Área do Cliente</h1>
            <h2><?php echo htmlspecialchars($empresa['razao_social']); ?></h2>
            <p>CNPJ: <?php echo formatarCNPJ($empresa['cnpj']); ?></p>
        </div>
        <div class="company-actions">
            <a href="profile.php" class="btn btn-outline">
                <i class="fas fa-user"></i>
                Perfil
            </a>
            <a href="../sign-out/" class="btn btn-primary">
                <i class="fas fa-sign-out-alt"></i>
                Sair
            </a>
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
                    <p><?php echo $totalEmpregados; ?></p>
                </div>
            </div>
            <div class="stat-card-footer">
                <a href="novo_funcionario.php">
                    Adicionar funcionário
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
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
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchEmployee" placeholder="Buscar funcionário...">
                </div>
                <a href="novo_funcionario.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Novo
                </a>
            </div>
        </div>

        <?php if (count($empregados) > 0): ?>
            <div class="responsive-table">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empregados as $empregado): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($empregado['nome']); ?></td>
                                <td><?php echo htmlspecialchars($empregado['cpf']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="visualizar_empregado.php?id=<?php echo $empregado['i_empregados']; ?>" class="action-btn view-btn" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="editar_empregado.php?id=<?php echo $empregado['i_empregados']; ?>" class="action-btn edit-btn" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="action-btn delete-btn" title="Excluir" 
                                                onclick="showDeleteModal(<?php echo $empregado['i_empregados']; ?>, '<?php echo htmlspecialchars($empregado['nome']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Nenhum funcionário cadastrado</h3>
                <p>Comece adicionando um novo funcionário ao seu cadastro</p>
                <a href="novo_funcionario.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Adicionar Funcionário
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Exclusão</h3>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja excluir o funcionário <strong id="employeeName"></strong>? Esta ação não pode ser desfeita.</p>
        </div>
        <div class="modal-footer">
            <button id="cancelDelete" class="btn btn-outline">Cancelar</button>
            <button id="confirmDelete" class="btn btn-primary" style="background-color: var(--danger-color);">Excluir</button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    // Elemento do modal
    const deleteModal = document.getElementById('deleteModal');
    const employeeName = document.getElementById('employeeName');
    const cancelDelete = document.getElementById('cancelDelete');
    const confirmDelete = document.getElementById('confirmDelete');
    
    // ID do funcionário a ser excluído
    let employeeIdToDelete = null;
    
    // Função para mostrar o modal de exclusão
    function showDeleteModal(id, name) {
        employeeIdToDelete = id;
        employeeName.textContent = name;
        deleteModal.style.display = 'flex';
    }
    
    // Fechar o modal ao clicar em Cancelar
    cancelDelete.addEventListener('click', function() {
        deleteModal.style.display = 'none';
        employeeIdToDelete = null;
    });
    
    // Fechar o modal ao clicar fora dele
    window.addEventListener('click', function(event) {
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
            employeeIdToDelete = null;
        }
    });
    
    // Confirmar exclusão
    confirmDelete.addEventListener('click', function() {
        if (employeeIdToDelete) {
            // Enviar solicitação AJAX para excluir o funcionário
            fetch(`excluir_empregado.php?id=${employeeIdToDelete}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remover linha da tabela
                    const row = document.querySelector(`button[onclick*="${employeeIdToDelete}"]`).closest('tr');
                    row.remove();
                    
                    // Atualizar contador de funcionários
                    const countElement = document.querySelector('.stat-card-content p');
                    const currentCount = parseInt(countElement.textContent);
                    countElement.textContent = currentCount - 1;
                    
                    // Verificar se a tabela está vazia
                    const tbody = document.querySelector('tbody');
                    if (tbody && tbody.children.length === 0) {
                        document.querySelector('.responsive-table').innerHTML = `
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3>Nenhum funcionário cadastrado</h3>
                                <p>Comece adicionando um novo funcionário ao seu cadastro</p>
                                <a href="novo_funcionario.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Adicionar Funcionário
                                </a>
                            </div>
                        `;
                    }
                } else {
                    alert('Erro ao excluir funcionário: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Ocorreu um erro ao processar sua solicitação.');
            })
            .finally(() => {
                deleteModal.style.display = 'none';
                employeeIdToDelete = null;
            });
        }
    });
    
    // Filtrar funcionários
    document.getElementById('searchEmployee').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const name = row.querySelector('td:first-child').textContent.toLowerCase();
            const cpf = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || cpf.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Botão de exportação
    document.getElementById('exportButton').addEventListener('click', function(e) {
        e.preventDefault();
        alert('Funcionalidade de exportação será implementada em breve.');
    });
</script>

<!-- Incluir Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- <?php include '../../includes/footer.php'; ?> -->