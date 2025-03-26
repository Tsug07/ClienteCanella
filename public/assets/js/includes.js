document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navList = document.querySelector('.nav-list');
    const header = document.querySelector('.main-header');
    
    // Controle do header ao scrollar
    let lastScroll = 0;
    const headerHeight = header.offsetHeight;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // Adiciona classe scrolled quando passar da altura do header
        if (currentScroll > headerHeight) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        // Esconde o header ao scrollar para baixo
        if (currentScroll > lastScroll && currentScroll > headerHeight) {
            // Scroll para baixo
            header.classList.add('hide');
        } else {
            // Scroll para cima
            header.classList.remove('hide');
        }
        
        // Mostra o header quando chegar no topo
        if (currentScroll <= 0) {
            header.classList.remove('hide');
        }
        
        lastScroll = currentScroll;
    });

    // Menu mobile (código anterior mantido)
    menuToggle.addEventListener('click', function() {
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !isExpanded);
        navList.classList.toggle('active');
    });
    
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                menuToggle.setAttribute('aria-expanded', 'false');
                navList.classList.remove('active');
            }
        });
    });
});

//Feedback
document.addEventListener('DOMContentLoaded', function() {
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
            button.addEventListener('click', function() {
                feedbackBtns.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                selectedResponse = this.getAttribute('data-response');
                commentSection.style.display = 'block';
                feedbackText.focus();
            });
        });

        // Cancelar feedback
        cancelBtn.addEventListener('click', function() {
            commentSection.style.display = 'none';
            feedbackBtns.forEach(btn => btn.classList.remove('active'));
            feedbackText.value = '';
            selectedResponse = null;
        });

        // Enviar feedback
        submitBtn.addEventListener('click', function() {
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




