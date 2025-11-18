<?php
// api.php
require 'src/Feedback.php';

header('Content-Type: application/json');

try {
    // 1. Recebe o JSON do JavaScript
    $input = json_decode(file_get_contents('php://input'), true);
    $mensagem = $input['mensagem'] ?? '';

    if (empty($mensagem)) {
        throw new Exception("Mensagem vazia.");
    }

    $feedbackObj = new Feedback();
    
    // 2. Calcula a categoria (para devolver ao JS)
    // O método classificarMensagem precisa ser PUBLIC no Feedback.php
    $categoria = $feedbackObj->classificarMensagem($mensagem);
    
    // 3. Salva no Banco
    $id = $feedbackObj->salvar($mensagem);
    
    // 4. Envia para o n8n (Webhook)
    $enviado = $feedbackObj->enviarParaIA($mensagem);

    // 5. Responde para o Front-end com TUDO que ele precisa
    echo json_encode([
        'success' => true,
        'id' => $id,
        'enviado_ia' => $enviado,
        'categoria' => $categoria // <--- O JavaScript precisa disso aqui!
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}