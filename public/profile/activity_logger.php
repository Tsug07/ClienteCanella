<?php
/**
 * Registra uma atividade do usuário no sistema
 * 
 * @param int $user_id ID do usuário que realizou a atividade
 * @param string $tipo_atividade Tipo de atividade (login, perfil_atualizado, etc)
 * @param string $descricao Descrição detalhada da atividade
 * @param object $conn Conexão com o banco de dados
 * @return bool Retorna true se o registro foi bem-sucedido, false caso contrário
 */
function registrar_atividade($user_id, $tipo_atividade, $descricao, $conn) {
    // Obtém o IP do usuário
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Obtém o user agent (navegador)
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    // Prepara a consulta SQL
    $stmt = $conn->prepare("INSERT INTO log_atividades (user_id, tipo_atividade, descricao, ip, user_agent) VALUES (?, ?, ?, ?, ?)");
    
    // Verifica se houve erro na preparação
    if (!$stmt) {
        return false;
    }
    
    // Faz o bind dos parâmetros
    $stmt->bind_param("issss", $user_id, $tipo_atividade, $descricao, $ip, $user_agent);
    
    // Executa a consulta
    $result = $stmt->execute();
    
    // Fecha o statement
    $stmt->close();
    
    return $result;
}

/**
 * Formata o user agent para exibição mais amigável
 * 
 * @param string $user_agent String contendo o user agent
 * @return string Navegador formatado
 */
function formatar_navegador($user_agent) {
    if (strpos($user_agent, 'Chrome') && strpos($user_agent, 'Edge') === false && strpos($user_agent, 'Edg') === false) {
        return 'Chrome';
    } elseif (strpos($user_agent, 'Firefox')) {
        return 'Firefox';
    } elseif (strpos($user_agent, 'Safari') && strpos($user_agent, 'Chrome') === false) {
        return 'Safari';
    } elseif (strpos($user_agent, 'Edge') || strpos($user_agent, 'Edg')) {
        return 'Edge';
    } elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/')) {
        return 'Internet Explorer';
    } elseif (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR')) {
        return 'Opera';
    } else {
        return 'Navegador desconhecido';
    }
}

/**
 * Obtém as atividades recentes de um usuário
 * 
 * @param int $user_id ID do usuário
 * @param int $limit Número máximo de atividades a serem retornadas
 * @param object $conn Conexão com o banco de dados
 * @return array Array contendo as atividades recentes
 */
function obter_atividades_recentes($user_id, $conn, $limit = 5) {
    $stmt = $conn->prepare("
        SELECT id, tipo_atividade, descricao, ip, user_agent, data_hora 
        FROM log_atividades 
        WHERE user_id = ? 
        ORDER BY data_hora DESC 
        LIMIT ?
    ");
    
    if (!$stmt) {
        return [];
    }
    
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $atividades = [];
    
    while ($row = $result->fetch_assoc()) {
        $atividades[] = $row;
    }
    
    $stmt->close();
    
    return $atividades;
}

/**
 * Obtém o ícone para cada tipo de atividade
 * 
 * @param string $tipo_atividade Tipo de atividade
 * @return string Classe do ícone Font Awesome
 */
function obter_icone_atividade($tipo_atividade) {
    $icones = [
        'login' => 'fas fa-sign-in-alt',
        'logout' => 'fas fa-sign-out-alt',
        'perfil_atualizado' => 'fas fa-user-edit',
        'empresa_atualizada' => 'fas fa-building',
        'senha_alterada' => 'fas fa-key',
        'token_renovado' => 'fas fa-key',
        'relatorio_exportado' => 'fas fa-file-export',
        'funcionario_adicionado' => 'fas fa-user-plus',
        'funcionario_atualizado' => 'fas fa-user-edit',
        'funcionario_removido' => 'fas fa-user-minus',
        'documento_enviado' => 'fas fa-file-upload',
        'documento_baixado' => 'fas fa-file-download'
    ];
    
    return isset($icones[$tipo_atividade]) ? $icones[$tipo_atividade] : 'fas fa-history';
}

/**
 * Formata o título da atividade para exibição
 * 
 * @param string $tipo_atividade Tipo de atividade
 * @return string Título formatado
 */
function formatar_titulo_atividade($tipo_atividade) {
    $titulos = [
        'login' => 'Login no sistema',
        'logout' => 'Logout do sistema',
        'perfil_atualizado' => 'Perfil atualizado',
        'empresa_atualizada' => 'Dados da empresa atualizados',
        'senha_alterada' => 'Senha alterada',
        'token_renovado' => 'Token renovado',
        'relatorio_exportado' => 'Relatório exportado',
        'funcionario_adicionado' => 'Funcionário adicionado',
        'funcionario_atualizado' => 'Funcionário atualizado',
        'funcionario_removido' => 'Funcionário removido',
        'documento_enviado' => 'Documento enviado',
        'documento_baixado' => 'Documento baixado'
    ];
    
    return isset($titulos[$tipo_atividade]) ? $titulos[$tipo_atividade] : ucfirst(str_replace('_', ' ', $tipo_atividade));
}
?>