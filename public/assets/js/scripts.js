// HEADER

document.querySelector('.menu-toggle').addEventListener('click', function() {
    document.querySelector('.nav-list').classList.toggle('active');
});


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

// POPUP DE FUNCIONÁRIO
function showEmployeePopup(employeeId, employeeName) {
    const popup = document.getElementById('employeePopup');
    const detailsContainer = document.getElementById('employeeDetails');
    const employeeData = document.getElementById('employeeData');
    const popupEmployeeName = document.getElementById('popupEmployeeName');
    
    // Configurar estado inicial
    detailsContainer.style.display = 'flex';
    employeeData.style.display = 'none';
    popupEmployeeName.textContent = employeeName || 'Informações do Funcionário';
    
    // Exibir o popup
    popup.style.display = 'flex';

    // Requisição AJAX
    fetch(`/ClienteCanella/public/get_employee_details.php?id=${employeeId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados do funcionário:', data);
            
            if (data.error) {
                detailsContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>${data.error}</p>
                    </div>
                `;
            } else {
                // Preencher os dados do cabeçalho
                document.getElementById('employeeHeaderName').textContent = data.nome || 'Nome não informado';
                document.getElementById('employeeHeaderCpf').textContent = formatarCPF(data.cpf);
                document.getElementById('employeeEsocialCode').textContent = data.CODIGO_ESOCIAL || 'Cód. não informado';
                
                // Preencher os dados pessoais
                document.getElementById('employeeName').textContent = data.nome || 'Não informado';
                document.getElementById('employeeCpf').textContent = formatarCPF(data.cpf);
                document.getElementById('employeeBirthdate').textContent = formatarData(data.data_nascimento);
                document.getElementById('employeeGender').textContent = formatarSexo(data.sexo);
                document.getElementById('employeeMaritalStatus').textContent = formatarEstadoCivil(data.estado_civil);
                document.getElementById('employeeNationality').textContent = data.nacionalidade || 'Brasileira';
                
                // Preencher os dados de contrato
                document.getElementById('employeeAdmission').textContent = formatarData(data.admissao);
                document.getElementById('employeePosition').textContent = data.cargos || 'Não informado';
                document.getElementById('employeeSalary').textContent = formatarSalario(data.salario);
                document.getElementById('employeeHours').textContent = data.horas_mes ? `${data.horas_mes}h` : 'Não informado';
                document.getElementById('employeeVacation').textContent = formatarData(data.venc_ferias);
                document.getElementById('employeeContractType').textContent = data.tipo_contrato || 'Não informado';
                
                // Preencher os dados de contato
                document.getElementById('employeePhone').textContent = formatarTelefone(data.fone);
                document.getElementById('employeeEmail').textContent = data.email || 'Não informado';
                document.getElementById('employeeAddress').textContent = data.endereco || 'Não informado';
                document.getElementById('employeeCityState').textContent = data.cidade && data.estado ? 
                    `${data.cidade}/${data.estado}` : 'Não informado';
                document.getElementById('employeeZipCode').textContent = formatarCEP(data.cep);
                
                // Preencher os dados de benefícios
                document.getElementById('employeeHealthPlan').textContent = simNao(data.plano_saude_optantes);
                document.getElementById('employeeUnion').textContent = simNao(data.contribuicao_sindical);
                document.getElementById('employeeFees').textContent = data.taxas_autorizadas || 'Nenhuma';
                document.getElementById('employeeTransport').textContent = simNao(data.vale_transporte);
                document.getElementById('employeeFood').textContent = simNao(data.vale_refeicao);
                
                // // Configurar botão de edição
                // const editButton = document.getElementById('editEmployeeBtn');
                // editButton.href = `editar_empregado.php?id=${employeeId}`;
                
                // Exibir os dados e ocultar o loader
                detailsContainer.style.display = 'none';
                employeeData.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            detailsContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Erro ao carregar informações. Tente novamente.</p>
                </div>
            `;
        });
}

function closeEmployeePopup() {
    const popup = document.getElementById('employeePopup');
    popup.style.display = 'none';
}

// Fechar o popup ao clicar fora dele
document.getElementById('employeePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEmployeePopup();
    }
});

// Funcionalidade de navegação por abas
document.addEventListener('DOMContentLoaded', function() {
    // Obter todas as abas e conteúdos de abas
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Adicionar evento de clique para cada botão de aba
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remover classe 'active' de todos os botões e conteúdos
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Adicionar classe 'active' ao botão clicado
            this.classList.add('active');
            
            // Mostrar o conteúdo correspondente
            const tabId = `tab-${this.getAttribute('data-tab')}`;
            document.getElementById(tabId).classList.add('active');
        });
    });
});


// // POPUP DE FUNCIONÁRIO
// document.addEventListener('DOMContentLoaded', function() {
//     const employeeLinks = document.querySelectorAll('.employee-link');
//     const employeeCard = document.getElementById('employee-card');
//     const employeeName = document.getElementById('employee-name');
//     const employeeRole = document.getElementById('employee-role');
//     const employeeEmail = document.getElementById('employee-email');

//     employeeLinks.forEach(link => {
//         link.addEventListener('click', function(e) {
//             e.preventDefault();
//             employeeName.textContent = this.getAttribute('data-name');
//             employeeRole.textContent = this.getAttribute('data-role');
//             employeeEmail.textContent = this.getAttribute('data-email');
//             employeeCard.style.display = 'block';
//         });
//     });
// });

// MAIN DAHSBOARD

// Elemento do modal
const deleteModal = document.getElementById('deleteModal');
const employeeName = document.getElementById('employeeName');
const cancelDelete = document.getElementById('cancelDelete');
const confirmDelete = document.getElementById('confirmDelete');

// // ID do funcionário a ser excluído
// let employeeIdToDelete = null;

// // Função para mostrar o modal de exclusão
// function showDeleteModal(id, name) {
//     employeeIdToDelete = id;
//     employeeName.textContent = name;
//     deleteModal.style.display = 'flex';
// }

// // Fechar o modal ao clicar em Cancelar
// cancelDelete.addEventListener('click', function() {
//     deleteModal.style.display = 'none';
//     employeeIdToDelete = null;
// });

// // Fechar o modal ao clicar fora dele
// window.addEventListener('click', function(event) {
//     if (event.target === deleteModal) {
//         deleteModal.style.display = 'none';
//         employeeIdToDelete = null;
//     }
// });

// Confirmar exclusão
// confirmDelete.addEventListener('click', function() {
//     if (employeeIdToDelete) {
//         // Enviar solicitação AJAX para excluir o funcionário
//         fetch(`excluir_empregado.php?id=${employeeIdToDelete}`, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-Requested-With': 'XMLHttpRequest'
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 // Recarregar a página para atualizar a lista e contadores
//                 window.location.reload();
//             } else {
//                 alert('Erro ao excluir funcionário: ' + data.message);
//             }
//         })
//         .catch(error => {
//             console.error('Erro:', error);
//             alert('Ocorreu um erro ao processar sua solicitação.');
//         })
//         .finally(() => {
//             deleteModal.style.display = 'none';
//             employeeIdToDelete = null;
//         });
//     }
// });

// Filtrar funcionários
document.getElementById('searchEmployee').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    
    // Se o termo de busca tiver pelo menos 2 caracteres ou estiver vazio
    if (searchTerm.length >= 2 || searchTerm.length === 0) {
        // Redirecionar para a primeira página com o termo de busca
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('search', searchTerm);
        currentUrl.searchParams.set('page', 1); // Volta para a primeira página ao buscar
        window.location.href = currentUrl.toString();
    }
});

// Inicializar o campo de busca com o valor atual da URL (se existir)
window.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    
    if (searchParam) {
        document.getElementById('searchEmployee').value = searchParam;
    }
});

// Botão de exportação
document.getElementById('exportButton').addEventListener('click', function(e) {
    e.preventDefault();
    alert('Funcionalidade de exportação será implementada em breve.');
});


