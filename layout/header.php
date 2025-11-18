<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synapse | Enterprise Gateway</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root { --sidebar-width: 260px; --bg-dark: #0f172a; --card-bg: #1e293b; --border-color: #334155; --accent: #6366f1; --success: #10b981; }
        body { background-color: var(--bg-dark); font-family: 'Inter', sans-serif; color: #94a3b8; overflow-x: hidden; }
        .sidebar { width: var(--sidebar-width); position: fixed; top: 0; bottom: 0; left: 0; background: #111827; border-right: 1px solid var(--border-color); padding: 20px; display: flex; flex-direction: column; z-index: 1000; }
        .brand { font-size: 1.2rem; font-weight: 700; color: #fff; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px; }
        .nav-item { padding: 10px 15px; border-radius: 8px; color: #94a3b8; text-decoration: none; margin-bottom: 5px; display: block; transition: 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .nav-item.active { background: var(--accent); color: #fff; }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }
        .card-custom { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .topbar { display: flex; justify-content: space-between; margin-bottom: 30px; align-items: center; }
        .avatar { width: 35px; height: 35px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        
        .text-accent { color: var(--accent); }
        .form-control { background: #0f172a; border: 1px solid var(--border-color); color: #fff; }
        .form-control:focus { background: #0f172a; border-color: var(--accent); color: #fff; box-shadow: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="bi bi-grid-1x2-fill text-primary"></i> Synapse CRM</div>
    
    <small class="text-uppercase text-muted mb-2" style="font-size: 0.7rem; font-weight: 700;">Menu Principal</small>
    <a href="index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-ticket-perforated me-2"></i> Ingestão
    </a>
    <a href="analytics.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">
        <i class="bi bi-graph-up me-2"></i> Analytics
    </a>
    
    <div class="mt-auto">
        <div class="p-3 rounded bg-dark bg-opacity-50 border border-secondary border-opacity-25">
            <small class="text-muted d-block mb-1">Status do Sistema</small>
            <div class="d-flex align-items-center gap-2">
                <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                <span class="text-white small">API Operacional</span>
            </div>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div>
            <h4 class="text-white mb-1">
                <?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'Dashboard Gerencial' : 'FeedBacks' ?>
            </h4>
            <small class="text-muted">Ambiente Seguro • PHP 8.2</small>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <div class="text-white small fw-bold">Marcelo Gomes</div>
                <div class="small text-muted" style="font-size: 0.7rem;">Full Stack Dev</div>
            </div>
            <div class="avatar">MG</div>
        </div>
    </div>