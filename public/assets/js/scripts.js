

// Função para formatar o CPF (ex.: 11186627760 -> 111.866.277-60)
function formatarCPF(cpf) {
    if (!cpf) return 'Não informado';
    cpf = cpf.toString().padStart(11, '0'); // Garante 11 dígitos
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

// Função para formatar data (ex.: 2012-03-01 00:00:00 -> 01/03/2012)
function formatarData(data) {
    if (!data) return 'Não informado';
    const [dataParte] = data.split(' '); // Pega apenas a parte da data, ignorando a hora
    const [ano, mes, dia] = dataParte.split('-');
    return `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${ano}`;
}

// Função para formatar salário (ex.: 920 -> 920,00)
function formatarSalario(salario) {
    if (!salario) return 'Não informado';
    return `R$ ${parseFloat(salario).toFixed(2).replace('.', ',')}`;
}

// Função para formatar telefone
function formatarTelefone(telefone) {
    if (!telefone) return 'Não informado';
    
    // Garante que telefone seja uma string
    telefone = String(telefone);

    // Remove caracteres não numéricos
    const numero = telefone.replace(/\D/g, '');
    
    // Aplica formatação baseada no comprimento
    if (numero.length === 11) {
        // Celular: (99) 99999-9999
        return numero.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (numero.length === 10) {
        // Fixo: (99) 9999-9999
        return numero.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        return telefone; // Retorna original se não reconhecer o padrão
    }
}

// Função para formatar CEP
function formatarCEP(cep) {
    if (!cep) return 'Não informado';

    // Converte para string, remove caracteres não numéricos e garante 8 dígitos
    const numero = cep.toString().replace(/\D/g, '').padStart(8, '0');

    // Verifica se o número tem 8 dígitos e aplica a formatação
    return numero.length === 8 ? numero.replace(/(\d{5})(\d{3})/, '$1-$2') : 'Não informado';
}

// Função para converter valores booleanos ou 0/1 para Sim/Não
function simNao(valor) {
    if (valor === null || valor === undefined) return 'Não informado';
    
    if (typeof valor === 'boolean') {
        return valor ? 'Sim' : 'Não';
    }
    
    if (valor === 1 || valor === '1' || valor === 'true' || valor === 'sim' || valor === 'yes') {
        return 'Sim';
    }
    
    return 'Não';
}

function formatarSimNao(valor) {
    if (valor === "S"){
        return 'Sim';
    } else {
        return 'Não';
    }
}

// Função para formatar contrato
function formatarContrato(contrato) {
    if (contrato == 1) {
        return 'Celetista'; // Contrato CLT padrão
    } else if (contrato == 50) {
        return 'Estagiário'; // Contrato de estágio
    } else if (contrato == 11) {
        return 'Celetista'; // Hipótese: Trabalhador rural vinculado a pessoa jurídica
    } else if (contrato == 13) {
        return 'Celetista'; // Hipótese: Trabalhador rural vinculado a pessoa física
    } else if (contrato == 99) {
        return 'Celetista Tempo Parcial'; // Contrato CLT com jornada parcial
    } else if (contrato == 56) {
        return 'Celetista Intermitente'; // Contrato CLT intermitente
    } else if (contrato == 53) {
        return 'Aprendiz'; // Contrato de aprendiz pela CLT
    } else {
        return 'Desconhecido'; // Caso o código não esteja mapeado
    }
}
// Função para obter o sexo por extenso
function formatarSexo(sexo) {
    if (!sexo) return 'Não informado';
    
    if (sexo.toLowerCase() === 'm' || sexo.toLowerCase() === 'masculino') {
        return 'Masculino';
    }
    
    if (sexo.toLowerCase() === 'f' || sexo.toLowerCase() === 'feminino') {
        return 'Feminino';
    }
    
    return sexo; // Retorna o valor original se não reconhecer
}

// Função para formatar estado civil
function formatarEstadoCivil(estado) {
    if (!estado) return 'Não informado';
    
    const estados = {
        's': 'Solteiro(a)',
        'c': 'Casado(a)',
        'd': 'Divorciado(a)',
        'v': 'Viúvo(a)',
        'u': 'União Estável',
        'solteiro': 'Solteiro(a)',
        'casado': 'Casado(a)',
        'divorciado': 'Divorciado(a)',
        'viuvo': 'Viúvo(a)',
        'uniao': 'União Estável'
    };
    
    return estados[estado.toLowerCase()] || estado;
}


// Botão de exportação
document.getElementById('exportButton').addEventListener('click', function(e) {
    e.preventDefault();
    alert('Funcionalidade de exportação será implementada em breve.');
});

