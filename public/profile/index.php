<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /ClienteCanella/public/sign-in/");
    exit();
}

// Incluir arquivo de conexão com o banco de dados
require_once '../../app/database.php';
require_once '../utils.php';

$page_title = "Perfil | Canella & Santos"; // Título padrão para a página de login

// Caso haja uma mensagem de erro, você pode alterar o título para algo como "Erro ao fazer login"
if (isset($_SESSION['error'])) {
    $page_title = "Erro no Perfil || Canella & Santos";
}
include '../../includes/head.php';

// Consulta informações da empresa
$cnpj = $_SESSION['cnpj'];
$stmt = $conn->prepare("SELECT codigo_empresa, razao_social, cnpj, email, telefone, endereco, cidade, estado, cep, inscricao_estadual, inscricao_municipal, regime_tributario FROM empresas WHERE cnpj = ?");
$stmt->bind_param("s", $cnpj);
$stmt->execute();
$result = $stmt->get_result();
$empresa = $result->fetch_assoc();

// Se não encontrar a empresa, define valores padrão
if (!$empresa) {
    $empresa = [
        'codigo_empresa' => 0,
        'razao_social' => 'Empresa não encontrada',
        'cnpj' => $cnpj,
        'email' => '',
        'telefone' => '',
        'endereco' => '',
        'cidade' => '',
        'estado' => '',
        'cep' => '',
        'inscricao_estadual' => '',
        'inscricao_municipal' => '',
        'regime_tributario' => ''
    ];
}

// Consulta informações do usuário
$stmt = $conn->prepare("SELECT id, cnpj, email, ultimo_acesso FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Se não encontrar o usuário, define valores padrão
if (!$usuario) {
    $usuario = [
        'id' => $_SESSION['user_id'],
        'cnpj' => $cnpj,
        'email' => '',
        'ultimo_acesso' => date('Y-m-d H:i:s')
    ];
}


// Verificar se há mensagens de sucesso ou erro
$mensagemSucesso = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$mensagemErro = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Limpar mensagens da sessão após uso
unset($_SESSION['success']);
unset($_SESSION['error']);

// Incluir o cabeçalho padrão
include '../../includes/header.php';
?>

<div class="dashboard-wrapper">
    <!-- Alertas de sucesso ou erro -->
    <?php if ($mensagemSucesso): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $mensagemSucesso; ?></span>
            <button class="alert-close-btn">&times;</button>
        </div>
    <?php endif; ?>

    <?php if ($mensagemErro): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $mensagemErro; ?></span>
            <button class="alert-close-btn">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Cabeçalho da página -->
    <div class="page-header">
        <div>
            <h1>Perfil</h1>
            <p>Gerencie suas informações e preferências</p>
        </div>
        <div>
            <a href="../dashboard" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar ao Dashboard
            </a>
        </div>
    </div>

    <!-- Layout principal com duas colunas -->
    <div class="profile-layout">
        <!-- Coluna de navegação -->
        <div class="profile-sidebar">
            <ul class="profile-nav">
                <li class="nav-item active" data-tab="company-info">
                    <i class="fas fa-building"></i>
                    <span>Dados da Empresa</span>
                </li>
                <li class="nav-item" data-tab="user-info">
                    <i class="fas fa-user"></i>
                    <span>Minha Conta</span>
                </li>
                <!-- <li class="nav-item" data-tab="security">
                    <i class="fas fa-shield-alt"></i>
                    <span>Segurança</span>
                </li> -->
                <!-- <li class="nav-item" data-tab="notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notificações</span>
                </li> -->
                <li class="nav-item" data-tab="activity">
                    <i class="fas fa-history"></i>
                    <span>Atividade Recente</span>
                </li>
            </ul>
        </div>

        <!-- Conteúdo principal -->
        <div class="profile-content">
            <!-- Tab: Dados da Empresa -->
            <div class="profile-tab active" id="company-info">
                <div class="section-header">
                    <h2>Dados da Empresa</h2>
                    <button class="btn btn-outline btn-edit-section" data-form="company-form">
                        <i class="fas fa-edit"></i>
                        Editar
                    </button>
                </div>

                <!-- Visualização dos dados da empresa -->
                <div class="company-details" id="company-view">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Razão Social</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['razao_social']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">CNPJ</span>
                            <span class="info-value"><?php echo formatarCNPJ($empresa['cnpj']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['email'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telefone</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['telefone'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item full-width">
                            <span class="info-label">Endereço</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['endereco'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Cidade</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['cidade'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Estado</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['estado'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">CEP</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['cep'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Inscrição Estadual</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['inscricao_estadual'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Inscrição Municipal</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['inscricao_municipal'] ?: 'Não informado'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Regime Tributário</span>
                            <span class="info-value"><?php echo htmlspecialchars($empresa['regime_tributario'] ?: 'Não informado'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Formulário de edição dos dados da empresa (inicialmente oculto) -->
                <div class="company-form" id="company-form" style="display: none;">
                    <form action="atualizar_empresa.php" method="post">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="razao_social">Razão Social</label>
                                <input type="text" id="razao_social" name="razao_social" value="<?php echo htmlspecialchars($empresa['razao_social']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="cnpj">CNPJ</label>
                                <input type="text" id="cnpj" name="cnpj" value="<?php echo htmlspecialchars($empresa['cnpj']); ?>" readonly>
                                <small>O CNPJ não pode ser alterado</small>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($empresa['email']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($empresa['telefone']); ?>">
                            </div>
                            <div class="form-group full-width">
                                <label for="endereco">Endereço</label>
                                <input type="text" id="endereco" name="endereco" value="<?php echo htmlspecialchars($empresa['endereco']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($empresa['cidade']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado">
                                    <option value="">Selecione...</option>
                                    <option value="AC" <?php echo $empresa['estado'] == 'AC' ? 'selected' : ''; ?>>Acre</option>
                                    <option value="AL" <?php echo $empresa['estado'] == 'AL' ? 'selected' : ''; ?>>Alagoas</option>
                                    <option value="AP" <?php echo $empresa['estado'] == 'AP' ? 'selected' : ''; ?>>Amapá</option>
                                    <option value="AM" <?php echo $empresa['estado'] == 'AM' ? 'selected' : ''; ?>>Amazonas</option>
                                    <option value="BA" <?php echo $empresa['estado'] == 'BA' ? 'selected' : ''; ?>>Bahia</option>
                                    <option value="CE" <?php echo $empresa['estado'] == 'CE' ? 'selected' : ''; ?>>Ceará</option>
                                    <option value="DF" <?php echo $empresa['estado'] == 'DF' ? 'selected' : ''; ?>>Distrito Federal</option>
                                    <option value="ES" <?php echo $empresa['estado'] == 'ES' ? 'selected' : ''; ?>>Espírito Santo</option>
                                    <option value="GO" <?php echo $empresa['estado'] == 'GO' ? 'selected' : ''; ?>>Goiás</option>
                                    <option value="MA" <?php echo $empresa['estado'] == 'MA' ? 'selected' : ''; ?>>Maranhão</option>
                                    <option value="MT" <?php echo $empresa['estado'] == 'MT' ? 'selected' : ''; ?>>Mato Grosso</option>
                                    <option value="MS" <?php echo $empresa['estado'] == 'MS' ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                    <option value="MG" <?php echo $empresa['estado'] == 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                                    <option value="PA" <?php echo $empresa['estado'] == 'PA' ? 'selected' : ''; ?>>Pará</option>
                                    <option value="PB" <?php echo $empresa['estado'] == 'PB' ? 'selected' : ''; ?>>Paraíba</option>
                                    <option value="PR" <?php echo $empresa['estado'] == 'PR' ? 'selected' : ''; ?>>Paraná</option>
                                    <option value="PE" <?php echo $empresa['estado'] == 'PE' ? 'selected' : ''; ?>>Pernambuco</option>
                                    <option value="PI" <?php echo $empresa['estado'] == 'PI' ? 'selected' : ''; ?>>Piauí</option>
                                    <option value="RJ" <?php echo $empresa['estado'] == 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                    <option value="RN" <?php echo $empresa['estado'] == 'RN' ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                    <option value="RS" <?php echo $empresa['estado'] == 'RS' ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                    <option value="RO" <?php echo $empresa['estado'] == 'RO' ? 'selected' : ''; ?>>Rondônia</option>
                                    <option value="RR" <?php echo $empresa['estado'] == 'RR' ? 'selected' : ''; ?>>Roraima</option>
                                    <option value="SC" <?php echo $empresa['estado'] == 'SC' ? 'selected' : ''; ?>>Santa Catarina</option>
                                    <option value="SP" <?php echo $empresa['estado'] == 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                                    <option value="SE" <?php echo $empresa['estado'] == 'SE' ? 'selected' : ''; ?>>Sergipe</option>
                                    <option value="TO" <?php echo $empresa['estado'] == 'TO' ? 'selected' : ''; ?>>Tocantins</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cep">CEP</label>
                                <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($empresa['cep']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="inscricao_estadual">Inscrição Estadual</label>
                                <input type="text" id="inscricao_estadual" name="inscricao_estadual" value="<?php echo htmlspecialchars($empresa['inscricao_estadual']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="inscricao_municipal">Inscrição Municipal</label>
                                <input type="text" id="inscricao_municipal" name="inscricao_municipal" value="<?php echo htmlspecialchars($empresa['inscricao_municipal']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="regime_tributario">Regime Tributário</label>
                                <select id="regime_tributario" name="regime_tributario">
                                    <option value="">Selecione...</option>
                                    <option value="Simples Nacional" <?php echo $empresa['regime_tributario'] == 'Simples Nacional' ? 'selected' : ''; ?>>Simples Nacional</option>
                                    <option value="Lucro Presumido" <?php echo $empresa['regime_tributario'] == 'Lucro Presumido' ? 'selected' : ''; ?>>Lucro Presumido</option>
                                    <option value="Lucro Real" <?php echo $empresa['regime_tributario'] == 'Lucro Real' ? 'selected' : ''; ?>>Lucro Real</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" id="cancel-company-edit">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tab: Minha Conta -->
            <div class="profile-tab" id="user-info">
                <div class="section-header">
                    <h2>Minha Conta</h2>
                    <button class="btn btn-outline btn-edit-section" data-form="user-form">
                        <i class="fas fa-edit"></i>
                        Editar
                    </button>
                </div>

                <!-- Visualização dos dados do usuário -->
                <div class="user-details" id="user-view">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">CNPJ Vinculado</span>
                            <span class="info-value"><?php echo formatarCNPJ($usuario['cnpj']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Último Acesso</span>
                            <span class="info-value"><?php echo formatarData($usuario['ultimo_acesso']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Formulário de edição dos dados do usuário (inicialmente oculto) -->
                <div class="user-form" id="user-form" style="display: none;">
                    <form action="atualizar_usuario.php" method="post">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="user_email">Email</label>
                                <input type="email" id="user_email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="user_cnpj">CNPJ Vinculado</label>
                                <input type="text" id="user_cnpj" name="cnpj" value="<?php echo htmlspecialchars($usuario['cnpj']); ?>" readonly>
                                <small>O CNPJ não pode ser alterado</small>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" id="cancel-user-edit">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tab: Segurança -->
            <!-- <div class="profile-tab" id="security">
                <div class="section-header">
                    <h2>Segurança</h2>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Renovar Token de Acesso</h3>
                    </div>
                    <div class="card-body">
                        <p>Solicite um novo token de acesso para sua conta. Um token será enviado para o email cadastrado.</p>
                        <form action="renovar_token.php" method="post" class="token-form">
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i>
                                    Solicitar Novo Token
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> -->

            <!-- Tab: Notificações -->
            <!-- <div class="profile-tab" id="notifications">
                <div class="section-header">
                    <h2>Notificações</h2>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="atualizar_notificacoes.php" method="post">
                            <div class="notification-settings">
                                <div class="notification-item">
                                    <div>
                                        <h4>Alertas de Vencimento</h4>
                                        <p>Receba notificações sobre vencimentos de férias e documentos</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notifications[]" value="vencimentos" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="notification-item">
                                    <div>
                                        <h4>Atualizações do Sistema</h4>
                                        <p>Seja notificado sobre novas funcionalidades e melhorias</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notifications[]" value="atualizacoes" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="notification-item">
                                    <div>
                                        <h4>Relatórios Mensais</h4>
                                        <p>Receba um resumo mensal das atividades da sua empresa</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notifications[]" value="relatorios">
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                                <div class="notification-item">
                                    <div>
                                        <h4>Notificações por Email</h4>
                                        <p>Receba todas as notificações por email</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notifications[]" value="email" checked>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-primary">Salvar Preferências</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> -->

            <!-- Tab: Atividade Recente -->
            <div class="profile-tab" id="activity">
                <div class="section-header">
                    <h2>Atividades Recentes</h2>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="activity-log">
                        <?php
    // Incluir o arquivo com as funções de atividade
    require_once 'activity_logger.php';
    
    // Obter atividades recentes do usuário
    $atividades = obter_atividades_recentes($_SESSION['user_id'], $conn, 5);
    
    if (empty($atividades)): 
    ?>
        <div class="activity-empty">
            <i class="fas fa-info-circle"></i>
            <p>Nenhuma atividade registrada ainda.</p>
        </div>
    <?php else: ?>
        <?php foreach ($atividades as $atividade): ?>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="<?php echo obter_icone_atividade($atividade['tipo_atividade']); ?>"></i>
                </div>
                <div class="activity-details">
                    <div class="activity-title"><?php echo formatar_titulo_atividade($atividade['tipo_atividade']); ?></div>
                    <div class="activity-time"><?php echo formatarData($atividade['data_hora']); ?></div>
                    <div class="activity-description">
                        <?php echo htmlspecialchars($atividade['descricao']); ?> | 
                        IP: <?php echo htmlspecialchars($atividade['ip']); ?> | 
                        Navegador: <?php echo formatar_navegador($atividade['user_agent']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="text-center mt-4">
    <a href="historico_atividades.php" class="btn btn-outline">
        <i class="fas fa-history"></i>
        Ver histórico completo
    </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir Font Awesome para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- CSS adicional para a página de perfil -->
<style>
/* Estilos para a página de perfil */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.page-header h1 {
    margin: 0;
    color: var(--dark-color);
    font-size: 1.75rem;
}

.page-header p {
    margin: 0.25rem 0 0;
    color: var(--gray-color);
}

/* Alertas */
.alert {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    animation: fadeIn 0.3s ease-out;
}

.alert i {
    margin-right: 0.75rem;
    font-size: 1.25rem;
}

.alert-success {
    background-color: #e6f7ed;
    color: #34a853;
    border-left: 4px solid #34a853;
}

.alert-error {
    background-color: #fae9e8;
    color: #ea4335;
    border-left: 4px solid #ea4335;
}

.alert-close-btn {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.alert-close-btn:hover {
    opacity: 1;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Layout de perfil */
.profile-layout {
    display: grid;
    grid-template-columns: minmax(200px, 250px) 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (max-width: 768px) {
    .profile-layout {
        grid-template-columns: 1fr;
    }
}

/* Sidebar de navegação */
.profile-sidebar {
    background-color: white;
    border-radius: 8px;
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.profile-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.profile-nav .nav-item {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    color: var(--gray-color);
    cursor: pointer;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.profile-nav .nav-item:hover {
    background-color: #f5f7f9;
    color: var(--primary-color);
}

.profile-nav .nav-item.active {
    color: var(--primary-color);
    background-color: #e9f1fe;
    border-left-color: var(--primary-color);
}

.profile-nav .nav-item i {
    width: 24px;
    margin-right: 0.75rem;
}

/* Conteúdo das abas */
.profile-content {
    background-color: white;
    border-radius: 8px;
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.profile-tab {
    display: none;
    animation: fadeIn 0.3s ease-out;
}

.profile-tab.active {
    display: block;
}

/* Cabeçalho de seção */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.section-header h2 {
    margin: 0;
    font-size: 1.25rem;
    color: var(--dark-color);
}

/* Grids para visualização e formulários */
.info-grid, .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1.25rem;
    padding: 1.5rem;
}

.info-item, .form-group {
    display: flex;
    flex-direction: column;
}

.info-item.full-width, .form-group.full-width {
    grid-column: 1 / -1;
}

.info-label, label {
    font-size: 0.75rem;
    color: var(--gray-color);
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.9375rem;
    color: var(--dark-color);
}

/* Estilos para formulários */
input, select, textarea {
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 0.9375rem;
    transition: border-color 0.2s;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
}

small {
    font-size: 0.75rem;
    color: var(--gray-color);
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f0f0f0;
}

/* Cards */
.card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    color: var(--dark-color);
}

.card-body {
    padding: 1.5rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

.mt-4 {
    margin-top: 1rem;
}

.text-center {
    text-align: center;
}

.text-muted {
    color: var(--gray-color);
}

/* Toggle switch */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: var(--primary-color);
}

input:focus + .slider {
    box-shadow: 0 0 1px var(--primary-color);
}

input:checked + .slider:before {
    transform: translateX(24px);
}

.slider.round {
    border-radius: 34px;
}

.slider.round:before {
    border-radius: 50%;
}

/* Configurações de notificações */
.notification-settings {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.notification-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #f0f0f0;
}

.notification-item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.notification-item h4 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
    color: var(--dark-color);
}

.notification-item p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-color);
}

/* Log de atividades */
.activity-log {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.activity-item {
    display: flex;
    gap: 1rem;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f0f4f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    flex-shrink: 0;
}

.activity-details {
    flex: 1;
}

.activity-title {
    font-weight: 500;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.75rem;
    color: var(--gray-color);
    margin-bottom: 0.25rem;
}

.activity-description {
    font-size: 0.875rem;
    color: var(--gray-color);
}

/* Responsividade para telas pequenas */
@media (max-width: 576px) {
    .info-grid, .form-grid {
        grid-template-columns: 1fr;
    }

    .activity-item {
        flex-direction: column;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .activity-icon {
        margin-bottom: 0.5rem;
    }

    .profile-nav .nav-item {
        padding: 0.875rem 1rem;
    }

    .profile-nav .nav-item span {
        font-size: 0.875rem;
    }
}
</style>

<script>
// Gerenciamento de abas
document.addEventListener('DOMContentLoaded', function() {
    // Obter todos os itens de navegação e abas
    const navItems = document.querySelectorAll('.profile-nav .nav-item');
    const tabs = document.querySelectorAll('.profile-tab');

    // Adicionar evento de clique para cada item de navegação
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remover classe active de todos os itens e abas
            navItems.forEach(navItem => navItem.classList.remove('active'));
            tabs.forEach(tab => tab.classList.remove('active'));

            // Adicionar classe active ao item clicado
            this.classList.add('active');

            // Mostrar a aba correspondente
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Botões de edição
    const editButtons = document.querySelectorAll('.btn-edit-section');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const formId = this.getAttribute('data-form');
            const form = document.getElementById(formId);
            const viewId = formId.replace('form', 'view');
            const view = document.getElementById(viewId);

            view.style.display = 'none';
            form.style.display = 'block';
            this.style.display = 'none';
        });
    });

    // Botões de cancelar edição
    document.getElementById('cancel-company-edit').addEventListener('click', function() {
        document.getElementById('company-form').style.display = 'none';
        document.getElementById('company-view').style.display = 'block';
        document.querySelector('[data-form="company-form"]').style.display = 'flex';
    });

    document.getElementById('cancel-user-edit').addEventListener('click', function() {
        document.getElementById('user-form').style.display = 'none';
        document.getElementById('user-view').style.display = 'block';
        document.querySelector('[data-form="user-form"]').style.display = 'flex';
    });

    // Fechar alertas
    const alertCloseButtons = document.querySelectorAll('.alert-close-btn');

    alertCloseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        });
    });

    // Formato de entrada para campos especiais
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            if (value.length > 5) {
                value = value.slice(0, 5) + '-' + value.slice(5);
            }
            e.target.value = value;
        });
    }

    const cnpjInput = document.getElementById('cnpj');
    if (cnpjInput) {
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 14) value = value.slice(0, 14);
            if (value.length > 12) {
                value = value.slice(0, 2) + '.' + value.slice(2, 5) + '.' + value.slice(5, 8) + '/' + value.slice(8, 12) + '-' + value.slice(12);
            } else if (value.length > 8) {
                value = value.slice(0, 2) + '.' + value.slice(2, 5) + '.' + value.slice(5, 8) + '/' + value.slice(8);
            } else if (value.length > 5) {
                value = value.slice(0, 2) + '.' + value.slice(2, 5) + '.' + value.slice(5);
            } else if (value.length > 2) {
                value = value.slice(0, 2) + '.' + value.slice(2);
            }
            e.target.value = value;
        });
    }

    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 10) { // Celular
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 7) + '-' + value.slice(7);
            } else if (value.length > 6) { // Fixo
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 6) + '-' + value.slice(6);
            } else if (value.length > 2) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            } else if (value.length > 0) {
                value = '(' + value;
            }
            e.target.value = value;
        });
    }
});
</script>

<?php include '../../includes/footer.php'; ?>