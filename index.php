<?php
require 'src/Feedback.php';
$feedbackObj = new Feedback();
$lista = $feedbackObj->listarUltimos();

// 1. Carrega o Topo e a Sidebar
include 'layout/header.php'; 
?>

<div class="row">
    
    <div class="col-lg-7">
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between mb-4">
                <h6 class="text-white mb-0">Novo Ticket de Suporte</h6>
                <span class="badge bg-dark border border-secondary">v2.4.1</span>
            </div>
            
            <form id="formFeedback">
                <div class="mb-3">
                    <label class="form-label small">Descrição do Problema (Raw Data)</label>
                    <textarea id="mensagem" class="form-control" rows="5" placeholder="Cole aqui o relato do cliente para processamento..." required></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="btnEnviar" style="background-color: var(--accent); border:none;">
                        <i class="bi bi-magic me-2"></i> Analisar com IA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-custom p-4 h-100">
            <h6 class="text-white mb-4">Histórico de Dados</h6>
            
            <div id="listaFeedbacks">
                <?php foreach ($lista as $item): ?>
                    <?php 
                            $cat = $item['categoria'] ?? 'Geral';
                            // Lógica de cores para as tags
                            $badgeColor = match($cat) {
                                'Bug 🐛' => 'text-danger border-danger',
                                'Performance 🚀' => 'text-warning border-warning',
                                'Elogio ❤️' => 'text-info border-info',
                                'Dúvida ❓' => 'text-primary border-primary',
                                default => 'text-secondary border-secondary'
                            };
                    ?>
                    <div class="log-item animate__animated animate__fadeIn">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="badge bg-transparent border <?= $badgeColor ?> bg-opacity-10"><?= $cat ?></span>
                            <span class="small font-monospace opacity-50">#ID-<?= $item['id'] ?></span>
                        </div>
                        <p class="mb-1 text-light small text-truncate"><?= htmlspecialchars($item['mensagem']) ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted" style="font-size: 0.7rem;">
                                <i class="bi bi-clock"></i> <?= date('H:i', strtotime($item['created_at'])) ?>
                            </small>
                            <small class="text-success" style="font-size: 0.7rem;">
                                <i class="bi bi-check2-all"></i> Webhook OK
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>

<script>
    // Configuração do SweetAlert
    const swalDark = Swal.mixin({ 
        background: '#1e293b', 
        color: '#f8fafc', 
        confirmButtonColor: '#6366f1', 
        iconColor: '#10b981' 
    });

    document.getElementById('formFeedback').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const campoMsg = document.getElementById('mensagem');
        const btn = document.getElementById('btnEnviar');
        const texto = campoMsg.value;
        const originalBtnContent = btn.innerHTML;

        // Feedback visual de carregamento
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processando...';

        try {
            // Envia para o Backend (api.php)
            const response = await fetch('api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mensagem: texto })
            });

            const result = await response.json();

            if (result.success) {
                // Sucesso!
                swalDark.fire({
                    icon: 'success', 
                    title: 'Sincronizado', 
                    toast: true, 
                    position: 'top-end', 
                    showConfirmButton: false, 
                    timer: 2000
                });

                // Adiciona na lista sem reload (para parecer instantâneo)
                // Define a cor da badge via JS (igual ao PHP)
                const cat = result.categoria;
                let badgeClass = 'text-secondary border-secondary';
                if (cat.includes('Bug')) badgeClass = 'text-danger border-danger';
                if (cat.includes('Performance')) badgeClass = 'text-warning border-warning';
                if (cat.includes('Elogio')) badgeClass = 'text-info border-info';
                if (cat.includes('Dúvida')) badgeClass = 'text-primary border-primary';
                
                const lista = document.getElementById('listaFeedbacks');
                const timeNow = new Date().toLocaleTimeString('pt-BR', { hour12: false, hour: '2-digit', minute:'2-digit' });

                const novoItem = `
                    <div class="log-item animate__animated animate__fadeInDown">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="badge bg-transparent border ${badgeClass} bg-opacity-10">${cat}</span>
                            <span class="small font-monospace opacity-50">#ID-${result.id}</span>
                        </div>
                        <p class="mb-1 text-light small text-truncate">${texto}</p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted" style="font-size: 0.7rem;">
                                <i class="bi bi-clock"></i> ${timeNow}
                            </small>
                            <small class="text-success" style="font-size: 0.7rem;">
                                <i class="bi bi-check2-all"></i> Webhook OK
                            </small>
                        </div>
                    </div>`;
                
                lista.insertAdjacentHTML('afterbegin', novoItem);
                campoMsg.value = ''; 

            } else {
                throw new Error(result.error);
            }

        } catch (error) {
            swalDark.fire({ icon: 'error', title: 'Erro', text: 'Falha na comunicação.' });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnContent;
        }
    });
</script>