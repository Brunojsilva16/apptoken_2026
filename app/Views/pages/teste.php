<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-ticket-alt"></i> Novo Atendimento</h4>
        </div>
        <div class="card-body">
            <form action="<?= URL_BASE ?>/salvar-token" method="POST">
                
                <input type="hidden" name="id_paciente" id="id_paciente" required>

                <!-- SEÇÃO 1: IDENTIFICAÇÃO -->
                <h5 class="text-primary mb-3 border-bottom pb-2">1. Identificação</h5>
                
                <div class="row">
                    <!-- Campo de Busca de Paciente -->
                    <div class="col-md-6 mb-3 position-relative">
                        <label for="paciente_busca" class="form-label fw-bold">Buscar Paciente <small class="text-muted">(Nome ou CPF)</small></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="paciente_busca" name="paciente_nome" placeholder="Digite para buscar..." autocomplete="off" required>
                        </div>
                        
                        <!-- Lista Flutuante -->
                        <div id="lista_sugestoes" class="list-group position-absolute w-100 shadow" style="z-index: 1050; display: none; top: 100%; max-height: 300px; overflow-y: auto;"></div>
                        <small class="text-muted" id="status_busca">Digite pelo menos 3 caracteres.</small>
                    </div>

                    <!-- Profissional -->
                    <div class="col-md-6 mb-3">
                        <label for="profissional" class="form-label fw-bold">Profissional</label>
                        <select class="form-select" name="id_prof" required>
                            <option value="">Selecione...</option>
                            <?php if(isset($profissionais) && is_array($profissionais)): ?>
                                <?php foreach($profissionais as $prof): ?>
                                    <option value="<?= $prof['id_prof'] ?>"><?= $prof['nome'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Campos Automáticos (Somente Leitura) -->
                <div class="row bg-light p-3 rounded mb-4 border">
                    <div class="col-md-3 mb-2">
                        <label class="form-label small text-muted">CPF</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="display_cpf" readonly>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label small text-muted">Telefone</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="display_telefone" readonly>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label small text-muted">Nome Responsável</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="display_responsavel" readonly>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label small text-muted">Resp. Financeiro</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="display_financeiro" readonly>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check-circle"></i> Gerar Token</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBusca = document.getElementById('paciente_busca');
    // const inputId = document.getElementById('id_paciente');
    const listaSugestoes = document.getElementById('lista_sugestoes');
    const statusBusca = document.getElementById('status_busca');
    
    // Referências aos novos campos de exibição
    const displayCpf = document.getElementById('display_cpf');
    const displayTelefone = document.getElementById('display_telefone');
    const displayResponsavel = document.getElementById('display_responsavel');
    const displayFinanceiro = document.getElementById('display_financeiro');

    let timeout = null;

    inputBusca.addEventListener('input', function() {
        const termo = this.value.trim();

        clearTimeout(timeout);

        if (termo.length < 3) {
            listaSugestoes.style.display = 'none';
            statusBusca.innerText = "Digite pelo menos 3 caracteres.";
            statusBusca.className = "text-muted";
            // Limpa campos se limpar a busca
            if(termo.length === 0) limparCampos();
            return;
        }

        statusBusca.innerText = "Buscando...";
        
        timeout = setTimeout(async () => {
            const baseUrl = '<?= rtrim(URL_BASE, '/') ?>';
            const searchUrl = `${baseUrl}/api/pacientes/busca?term=${encodeURIComponent(termo)}`;

            try {
                const response = await fetch(searchUrl, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'include'
                });

                if (response.status === 401) throw new Error('Sessão expirada.');
                if (!response.ok) throw new Error('Erro na comunicação.');

                const data = await response.json();

                listaSugestoes.innerHTML = '';
                
                if (data.error) {
                    statusBusca.innerText = data.error;
                    statusBusca.className = "text-danger";
                    return;
                }

                if (data.length > 0) {
                    listaSugestoes.style.display = 'block';
                    statusBusca.innerText = `${data.length} pacientes encontrados.`;
                    statusBusca.className = "text-success";
                    
                    data.forEach(paciente => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.classList.add('list-group-item', 'list-group-item-action', 'py-2');
                        
                        item.innerHTML = `
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user me-3 text-secondary fs-4"></i>
                                <div class="text-start">
                                    <div class="fw-bold text-dark">${paciente.nome}</div>
                                    <div class="small text-muted"><i class="far fa-id-card me-1"></i>${paciente.cpf}</div>
                                </div>
                            </div>
                        `;
                        
                        item.addEventListener('click', function() {
                            // Preenche ID e Nome Busca
                            inputBusca.value = paciente.nome;
                            // inputId.value = paciente.id;
                            
                            // PREENCHE OS NOVOS CAMPOS AUTOMATICAMENTE
                            displayCpf.value = paciente.cpf;
                            displayTelefone.value = paciente.telefone || 'Não informado';
                            displayResponsavel.value = paciente.nome_responsavel || 'N/A';
                            displayFinanceiro.value = paciente.responsavel_financeiro || 'N/A';

                            listaSugestoes.style.display = 'none';
                            statusBusca.innerText = "Paciente selecionado.";
                            statusBusca.className = "text-primary fw-bold";
                        });
                        
                        listaSugestoes.appendChild(item);
                    });
                } else {
                    listaSugestoes.style.display = 'none';
                    statusBusca.innerText = "Nenhum paciente encontrado.";
                    statusBusca.className = "text-warning";
                }

            } catch (err) {
                console.error('Erro:', err);
                statusBusca.innerText = err.message;
                statusBusca.className = "text-danger";
            }
        }, 300);
    });

    function limparCampos() {
        // inputId.value = '';
        displayCpf.value = '';
        displayTelefone.value = '';
        displayResponsavel.value = '';
        displayFinanceiro.value = '';
    }

    // document.addEventListener('click', function(e) {
    //     if (!inputBusca.contains(e.target) && !listaSugestoes.contains(e.target)) {
    //         listaSugestoes.style.display = 'none';
    //     }
    // });
});
</script>