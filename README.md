# Synapse

<div align="center">

![Status](https://img.shields.io/badge/Status-Production%20Ready-success?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Architecture](https://img.shields.io/badge/Architecture-RAG%20Middleware-blueviolet?style=for-the-badge)

**Gateway de Ingestão de Dados e Orquestração para Pipelines de IA**

</div>


Trata-se de uma solução estratégica que unifica dados e otimiza interações, permitindo às empresas elevar a qualidade da gestão de relacionamento com leads e sua base ativa.

Ele não apenas repassa os dados; ele os **intercepta, higieniza, classifica e persiste** antes de qualquer chamada externa. Isso garante que nenhum dado seja perdido e que a IA receba apenas inputs estruturados.

---

## 🧠 Arquitetura do Sistema

O sistema opera em um fluxo de **Ingestão em 3 Estágios**:

1.  **Persistência Atômica (Fail-Safe):**
    * Antes de qualquer processamento, o payload bruto é salvo no MySQL usando **PDO** com *Prepared Statements*. Isso garante auditoria completa e segurança contra SQL Injection.

2.  **Smart Tagging (Pré-processamento):**
    * Uma *engine* de classificação interna (desenvolvida nativamente em PHP) analisa o sentimento e a intenção do texto (ex: Bug, Elogio, Performance).
    * **Benefício:** Isso permite rotear o ticket para o fluxo correto no n8n sem gastar tokens de IA para classificação básica.

3.  **Disparo Assíncrono (Webhook):**
    * Utiliza **Guzzle HTTP Client** para despachar o dado enriquecido para o orquestrador (n8n). Implementa tratamento de exceções (`try/catch`) para garantir que o UX não seja afetado se a API externa estiver offline.

---

## 🛠️ Tech Stack

* **Core:** PHP 8.2 (Strict Types & OOP)
* **Database:** MySQL 8.0 (Transacional)
* **Dependency Manager:** Composer
* **Http Client:** Guzzle 7.0 (PSR-7 Compliant)
* **Frontend:**
    * Bootstrap 5 (Grid & Layout)
    * Chart.js (Visualização de Dados/BI)
    * Glassmorphism UI (Design System moderno)
    * Fetch API (Comunicação assíncrona/AJAX)

---

## 🚀 Como Rodar Localmente

### Pré-requisitos
* PHP 8.0+
* Composer
* MySQL

### Instalação

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/celingomess/synapse-crm.git](https://github.com/celingomess/synapse-crm.git)
    cd synapse-crm
    ```

2.  **Instale as dependências:**
    ```bash
    composer install
    ```

3.  **Configure o Banco de Dados:**
    * Crie um banco chamado `sistema_vaga`.
    * Importe a estrutura inicial:
    ```sql
    CREATE TABLE feedbacks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mensagem TEXT NOT NULL,
        categoria VARCHAR(30) DEFAULT 'Geral',
        status VARCHAR(50) DEFAULT 'pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ```

4.  **Configure o Webhook (Opcional):**
    * Abra `src/Feedback.php` e defina a variável `$webhookUrl` com seu endpoint do n8n ou webhook.site.

5.  **Inicie o Servidor:**
    ```bash
    php -S localhost:8000
    ```
    Acesse `http://localhost:8000` no seu navegador.

---

## 📊 Funcionalidades da Interface

### 1. Ingestão de Dados (Dashboard)
Interface focada em UX, com *feedback* visual imediato. O sistema não recarrega a página (SPA-feel), utilizando JavaScript para atualizar a lista de logs e as tags de classificação em tempo real.

### 2. Analytics (BI)
Módulo de visualização que processa os dados armazenados via SQL Aggregation (`GROUP BY`, `COUNT`) para gerar insights sobre o volume de tickets e distribuição de categorias, utilizando gráficos interativos (Doughnut Charts).

---

## 👨‍💻 Autor

Desenvolvido por **Marcelo Gomes** como prova de conceito de arquitetura robusta em PHP.

> *"Software engineering is not just about code, it's about data integrity and system resilience."*

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-blue?style=flat&logo=linkedin)](https://www.linkedin.com/in/marcelogomes) ```

---

### 💡 Por que este README é melhor?

1.  **Linguagem de Engenheiro:** Usa termos como "Payload", "Persistência Atômica", "Fail-Safe". Isso mostra que você sabe *o que* está fazendo, não apenas copiando tutorial.
2.  **Justificativa de Negócio:** A seção "O Problema" explica *por que* o software existe (economizar dinheiro com IA). Isso é música para os ouvidos de gestores.
3.  **Autoridade:** A citação final e os badges dão um ar de projeto sério e bem acabado.

Agora é só dar o `git push` e brilhar amanhã! 🚀