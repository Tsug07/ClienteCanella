document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.menu-toggle');
    const navList = document.querySelector('.nav-list');
    const header = document.querySelector('.main-header');

    if (!menuToggle || !navList) {
        console.error('Erro: Elementos .menu-toggle ou .nav-list não encontrados no DOM');
        return;
    }

    // Garantir que o menu comece fechado
    navList.classList.remove('active');
    menuToggle.setAttribute('aria-expanded', 'false');
    console.log('Estado inicial ajustado. Classe active presente:', navList.classList.contains('active'));

    // Controle do header ao scrollar
    let lastScroll = 0;
    const headerHeight = header.offsetHeight;

    window.addEventListener('scroll', function () {
        const currentScroll = window.pageYOffset;

        if (currentScroll > headerHeight) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        if (currentScroll > lastScroll && currentScroll > headerHeight) {
            header.classList.add('hide');
        } else {
            header.classList.remove('hide');
        }

        if (currentScroll <= 0) {
            header.classList.remove('hide');
        }

        lastScroll = currentScroll;
    });

    // Menu lateral
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        navList.classList.toggle('active');
        console.log('Menu toggle clicked. Expanded:', !isExpanded);
    });

    // Fechar o menu ao clicar em um link no mobile
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                menuToggle.setAttribute('aria-expanded', 'false');
                navList.classList.remove('active');
            }
        });
    });

    // Fechar o menu ao clicar fora dele
    document.addEventListener('click', function (event) {
        if (window.innerWidth <= 768 && !navList.contains(event.target) && !menuToggle.contains(event.target)) {
            menuToggle.setAttribute('aria-expanded', 'false');
            navList.classList.remove('active');
        }
    });
});

//Feedback
document.addEventListener('DOMContentLoaded', function () {
    console.log("✅ DOM carregado!");
    document.querySelectorAll('.feedback-module').forEach(module => {
        const wrapper = module.querySelector('.feedback-wrapper');
        const feedbackBtns = wrapper.querySelectorAll('.feedback-btn');
        const commentSection = wrapper.querySelector('.feedback-comment');
        const submitBtn = wrapper.querySelector('.submit-feedback');
        const cancelBtn = wrapper.querySelector('.cancel-feedback');
        const feedbackMain = wrapper.querySelector('.feedback-main');
        const feedbackSuccess = wrapper.querySelector('.feedback-success');
        const feedbackText = wrapper.querySelector('.feedback-text');
        let selectedResponse = null;
        const pageName = module.getAttribute('data-page'); // Pega a página dinamicamente

        // Evento de clique nos botões "Sim" e "Não"
        feedbackBtns.forEach(button => {
            button.addEventListener('click', function () {
                feedbackBtns.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                selectedResponse = this.getAttribute('data-response');
                commentSection.style.display = 'block';
                feedbackText.focus();
            });
        });

        // Cancelar feedback
        cancelBtn.addEventListener('click', function () {
            commentSection.style.display = 'none';
            feedbackBtns.forEach(btn => btn.classList.remove('active'));
            feedbackText.value = '';
            selectedResponse = null;
        });

        // Enviar feedback
        submitBtn.addEventListener('click', function () {
            if (!selectedResponse) {
                alert('Por favor, selecione uma opção antes de enviar.');
                return;
            }

            const comment = feedbackText.value.trim();
            submitFeedback(selectedResponse, comment, pageName, feedbackMain, feedbackSuccess, feedbackText, feedbackBtns, commentSection);
        });
    });
});

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
                // document.getElementById('employeeNationality').textContent = data.nacionalidade || 'Brasileira';
                document.getElementById('employeeNationality').textContent = 'Brasileira';
                document.getElementById('employeePCD').textContent = simNao(data.POSSUI_DEFICIENCIA) || 'Não informado';

                // Preencher os dados de contrato
                document.getElementById('employeeAdmission').textContent = formatarData(data.admissao);
                document.getElementById('employeePosition').textContent = data.cargos || 'Não informado';
                document.getElementById('employeeSalary').textContent = formatarSalario(data.salario);
                document.getElementById('employeeHours').textContent = data.horas_mes ? `${data.horas_mes}h` : 'Não informado';
                document.getElementById('employeeVacation').textContent = formatarData(data.venc_ferias);
                document.getElementById('employeeContractType').textContent = formatarContrato(data.vinculo) || 'Não informado';

                // Preencher os dados de contato
                document.getElementById('employeePhone').textContent = formatarTelefone(data.fone);
                document.getElementById('employeeEmail').textContent = data.email || 'Não informado';
                document.getElementById('employeeAddress').textContent = data.endereco || 'Não informado';
                document.getElementById('employeeCityState').textContent = data.cidade && data.estado ?
                    `${data.cidade}/${data.estado}` : 'Não informado';
                document.getElementById('employeeZipCode').textContent = formatarCEP(data.cep);

                // Preencher os dados de benefícios
                document.getElementById('employeeHealthPlan').textContent = simNao(data.plano_saude_optantes);
                document.getElementById('employeeUnion').textContent = formatarSimNao(data.contribuicao_sindical);
                document.getElementById('employeeFGTS').textContent = formatarSimNao(data.opta_fgts) || 'Nenhuma';
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
document.getElementById('employeePopup').addEventListener('click', function (e) {
    if (e.target === this) {
        closeEmployeePopup();
    }
});

// Funcionalidade de navegação por abas
document.addEventListener('DOMContentLoaded', function () {
    // Obter todas as abas e conteúdos de abas
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    // Adicionar evento de clique para cada botão de aba
    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
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



// Filtrar funcionários
// Filtrar funcionários com AJAX
document.getElementById('searchEmployee').addEventListener('keyup', function () {
    const searchTerm = this.value.toLowerCase();
    const employeeTableBody = document.getElementById('employeeTableBody');
    const paginationContainer = document.querySelector('.pagination-container');

    // Só faz a requisição se o termo tiver 2 ou mais caracteres ou estiver vazio
    if (searchTerm.length >= 2 || searchTerm.length === 0) {
        // Aplicar efeito de saída
        employeeTableBody.classList.add('fade-out');
        paginationContainer.classList.add('fade-out');

        // Pequeno atraso para garantir que a animação de saída ocorra antes do carregamento
        setTimeout(() => {
            employeeTableBody.innerHTML = `
                <tr>
                    <td colspan="3" style="text-align: center; padding: 20px;">
                        <div class="loader" style="margin: 0 auto;"></div>
                        Carregando...
                    </td>
                </tr>
            `;
            paginationContainer.innerHTML = '';

            // Fazer a requisição AJAX
            fetch(`/ClienteCanella/public/get_employees.php?search=${encodeURIComponent(searchTerm)}&page=1`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Atualizar a tabela com transição suave
                    updateEmployeeTable(data);
                })
                .catch(error => {
                    console.error('Erro ao buscar funcionários:', error);
                    employeeTableBody.innerHTML = `
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 20px;">
                            Erro ao carregar funcionários. Tente novamente.
                        </td>
                    </tr>
                `;
                    paginationContainer.innerHTML = '';
                    // Reaplicar fade-in mesmo em caso de erro
                    employeeTableBody.classList.remove('fade-out');
                    paginationContainer.classList.remove('fade-out');
                    employeeTableBody.classList.add('fade-in');
                    paginationContainer.classList.add('fade-in');
                });
        }, 300); // Tempo igual à duração da transição (0.3s)
    }
});

// Função para atualizar a tabela e a paginação
function updateEmployeeTable(data) {
    const employeeTableBody = document.getElementById('employeeTableBody');
    const paginationContainer = document.querySelector('.pagination-container');

    // Atualizar a tabela
    if (data.employees.length > 0) {
        employeeTableBody.innerHTML = data.employees.map(employee => `
            <tr>
                <td>${employee.nome}</td>
                <td>${employee.cpf}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view-btn" title="Visualizar" 
                            onclick="showEmployeePopup(${employee.i_empregados}, '${employee.nome}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    } else {
        employeeTableBody.innerHTML = `
            <tr>
                <td colspan="3" style="text-align: center; padding: 20px;">
                    Nenhum funcionário encontrado.
                </td>
            </tr>
        `;
    }

    // Atualizar a paginação
    paginationContainer.innerHTML = `
        <div class="pagination-info">
            Mostrando ${data.startIndex + 1} a ${Math.min(data.endIndex + 1, data.totalEmployees)} de ${data.totalEmployees} funcionários
        </div>
        <div class="pagination">
            <a href="#" class="pagination-btn ${data.currentPage === 1 ? 'disabled' : ''}" 
               onclick="changePage(1, '${data.searchTerm}')" title="Primeira página">
                <i class="fas fa-angle-double-left"></i>
            </a>
            <a href="#" class="pagination-btn ${data.currentPage === 1 ? 'disabled' : ''}" 
               onclick="changePage(${data.currentPage - 1}, '${data.searchTerm}')" title="Página anterior">
                <i class="fas fa-angle-left"></i>
            </a>
            ${generatePaginationButtons(data.currentPage, data.totalPages, data.searchTerm)}
            <a href="#" class="pagination-btn ${data.currentPage === data.totalPages ? 'disabled' : ''}" 
               onclick="changePage(${data.currentPage + 1}, '${data.searchTerm}')" title="Próxima página">
                <i class="fas fa-angle-right"></i>
            </a>
            <a href="#" class="pagination-btn ${data.currentPage === data.totalPages ? 'disabled' : ''}" 
               onclick="changePage(${data.totalPages}, '${data.searchTerm}')" title="Última página">
                <i class="fas fa-angle-double-right"></i>
            </a>
        </div>
    `;

    // Aplicar efeito de entrada
    setTimeout(() => {
        employeeTableBody.classList.remove('fade-out');
        paginationContainer.classList.remove('fade-out');
        employeeTableBody.classList.add('fade-in');
        paginationContainer.classList.add('fade-in');
    }, 50); // Pequeno atraso para garantir que o DOM esteja atualizado antes da animação
}

// Função para gerar os botões de paginação
function generatePaginationButtons(currentPage, totalPages, searchTerm) {
    const maxButtons = 5;
    let startPage = Math.max(1, Math.min(currentPage - 2, totalPages - maxButtons + 1));
    let endPage = Math.min(startPage + maxButtons - 1, totalPages);
    let buttons = '';

    for (let i = startPage; i <= endPage; i++) {
        buttons += `
            <a href="#" class="pagination-btn ${currentPage === i ? 'active' : ''}" 
               onclick="changePage(${i}, '${searchTerm}')">${i}</a>
        `;
    }
    return buttons;
}

// Função para mudar de página (ajustada para transição suave)
function changePage(page, searchTerm) {
    const employeeTableBody = document.getElementById('employeeTableBody');
    const paginationContainer = document.querySelector('.pagination-container');

    // Aplicar efeito de saída
    employeeTableBody.classList.add('fade-out');
    paginationContainer.classList.add('fade-out');

    setTimeout(() => {
        fetch(`/ClienteCanella/public/get_employees.php?search=${encodeURIComponent(searchTerm)}&page=${page}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => updateEmployeeTable(data))
            .catch(error => console.error('Erro ao mudar de página:', error));
    }, 300); // Tempo igual à duração da transição
}



// Função para envio de feedback
function submitFeedback(response, comment, page, feedbackMain, feedbackSuccess, feedbackText, feedbackBtns, commentSection) {
    fetch('/ClienteCanella/public/feedback.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `response=${encodeURIComponent(response)}&comment=${encodeURIComponent(comment)}&pagina=${encodeURIComponent(page)}`
    })
        .then(response => response.ok ? response.text() : Promise.reject(response))
        .then(() => {
            feedbackMain.style.display = 'none';
            feedbackSuccess.style.display = 'block';
            setTimeout(() => {
                feedbackText.value = '';
                feedbackBtns.forEach(btn => btn.classList.remove('active'));
                commentSection.style.display = 'none';
                feedbackSuccess.style.display = 'none';
                feedbackMain.style.display = 'block';
            }, 2000);
        })
        .catch(error => {
            alert('Erro ao enviar o feedback. Tente novamente.');
            console.error('Erro no envio do feedback:', error);
        });
}




