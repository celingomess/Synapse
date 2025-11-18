<?php
require __DIR__ . '/../vendor/autoload.php'; 

use GuzzleHttp\Client;

class Feedback {
    private $pdo;
    private $guzzle;

    public function __construct() {
        try {
            // Conexão PDO
            $this->pdo = new PDO('mysql:host=localhost;dbname=sistema_vaga', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // --- CORREÇÃO DE FUSO HORÁRIO (Fundamental para Analytics) ---
            // Força o MySQL a usar o horário do Brasil (-03:00)
            $this->pdo->exec("SET time_zone = '-03:00'");
            
            $this->guzzle = new Client(['timeout'  => 5.0]);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    // Lógica de Classificação (Smart Tagging)
    public function classificarMensagem($texto) {
        $texto = mb_strtolower($texto);
        
        if (str_contains($texto, 'erro') || str_contains($texto, 'bug') || str_contains($texto, 'falha')) {
            return 'Bug 🐛';
        }
        if (str_contains($texto, 'lento') || str_contains($texto, 'demora') || str_contains($texto, 'travando')) {
            return 'Performance 🚀';
        }
        if (str_contains($texto, 'obrigado') || str_contains($texto, 'excelente') || str_contains($texto, 'bom')|| str_contains($texto, 'parabéns')) {
            return 'Elogio ❤️';
        }
        if (str_contains($texto, 'como') || str_contains($texto, 'duvida') || str_contains($texto, '?')) {
            return 'Dúvida ❓';
        }
        
        return 'Geral 📝';
    }

    // Salvar no Banco
    public function salvar($mensagem) {
        // 1. AQUI ESTÁ O SEGREDO: Calcular a categoria antes de salvar
        $categoria = $this->classificarMensagem($mensagem);

        // 2. Atualizar o SQL para incluir a coluna ':cat'
        $sql = "INSERT INTO feedbacks (mensagem, categoria) VALUES (:msg, :cat)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'msg' => $mensagem,
            'cat' => $categoria // 3. Enviando a categoria calculada
        ]);
        
        return $this->pdo->lastInsertId();
    }

    // Enviar para IA (Webhook)
    public function enviarParaIA($mensagem) {
        // Substitua pela sua URL do webhook.site se precisar
        $webhookUrl = 'https://webhook.site/SUA-URL-AQUI'; 

        try {
            $response = $this->guzzle->request('POST', $webhookUrl, [
                'json' => ['texto_analise' => $mensagem]
            ]);
            return $response->getStatusCode() == 200;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Listar Últimos (Para o Dashboard Principal)
    public function listarUltimos() {
        $stmt = $this->pdo->query("SELECT * FROM feedbacks ORDER BY id DESC LIMIT 5");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getEstatisticas() {
        
        $total = $this->pdo->query("SELECT COUNT(*) FROM feedbacks")->fetchColumn();
        
        
        $porCategoria = $this->pdo->query("
            SELECT categoria, COUNT(*) as qtd 
            FROM feedbacks 
            GROUP BY categoria
            ORDER BY qtd DESC  -- <--- O SEGREDO ESTÁ AQUI
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        
        $hoje = $this->pdo->query("
            SELECT COUNT(*) FROM feedbacks 
            WHERE DATE(created_at) = CURDATE()
        ")->fetchColumn();

        return [
            'total' => $total,
            'hoje' => $hoje,
            'categorias' => $porCategoria
        ];
    }
}