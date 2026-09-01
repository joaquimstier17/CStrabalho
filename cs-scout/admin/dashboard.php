<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAdmin();

$db = getDB();

$stats = [
    'jogadores' => $db->query("SELECT COUNT(*) FROM jogadores")->fetchColumn(),
    'usuarios' => $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn(),
    'favoritos' => $db->query("SELECT COUNT(*) FROM favoritos")->fetchColumn(),
    'comparacoes' => $db->query("SELECT COUNT(*) FROM comparacoes")->fetchColumn(),
];

// Últimos usuários
$ultimosUsuarios = $db->query("SELECT * FROM usuarios ORDER BY data_cadastro DESC LIMIT 10")->fetchAll();

// Últimas comparações
$ultimasComp = $db->query("SELECT c.*, u.nome as usuario, j1.nick as nick1, j2.nick as nick2 FROM comparacoes c 
    JOIN usuarios u ON c.usuario_id = u.id 
    JOIN jogadores j1 ON c.jogador_1 = j1.id 
    JOIN jogadores j2 ON c.jogador_2 = j2.id 
    ORDER BY c.data_comparacao DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Painel Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <button class="hamburger" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>

    <aside class="sidebar">
        <div class="logo">CS-SCOUT</div>
        <nav>
            <a href="../index.php">Dashboard</a>
            <a href="../jogadores.php">Jogadores</a>
            <a href="../ranking.php">Ranking</a>
            <a href="../comparar.php">Comparar</a>
            <a href="../player-dna.php">Player DNA</a>
            <a href="../favoritos.php">Favoritos</a>
            <div class="nav-section">Admin</div>
            <a href="dashboard.php" class="active">Painel Admin</a>
            <a href="importar.php">Importar CSV</a>
            <a href="jogadores.php">Gerenciar Jogadores</a>
        </nav>
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['user_nome'] ?? 'Admin') ?></strong>
            <span class="badge-admin">ADMIN</span>
            <a href="../logout.php">Sair</a>
        </div>
    </aside>

    <main class="content">
        <header class="top-bar">
            <h2>Painel Administrativo</h2>
        </header>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 25px;">
            <div class="stat-card" style="min-width: 180px;">
                <div class="label">Jogadores</div>
                <div class="val"><?= $stats['jogadores'] ?></div>
            </div>
            <div class="stat-card" style="min-width: 180px;">
                <div class="label">Usuários</div>
                <div class="val"><?= $stats['usuarios'] ?></div>
            </div>
            <div class="stat-card" style="min-width: 180px;">
                <div class="label">Favoritos</div>
                <div class="val"><?= $stats['favoritos'] ?></div>
            </div>
            <div class="stat-card" style="min-width: 180px;">
                <div class="label">Comparações</div>
                <div class="val"><?= $stats['comparacoes'] ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <div class="table-container">
                <div style="padding: 16px; border-bottom: 1px solid var(--panel-border); font-weight: 700;">
                    👥 Últimos Usuários Cadastrados
                </div>
                <table class="scout-table">
                    <thead>
                        <tr><th>Nome</th><th>Usuário</th><th>Tipo</th><th>Status</th><th>Cadastro</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimosUsuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['username']) ?></td>
                            <td><span class="badge-<?= $u['tipo'] ?>"><?= strtoupper($u['tipo']) ?></span></td>
                            <td><?= ucfirst($u['status']) ?></td>
                            <td><?= date('d/m/Y', strtotime($u['data_cadastro'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <div style="padding: 16px; border-bottom: 1px solid var(--panel-border); font-weight: 700;">
                    ⚔️ Últimas Comparações
                </div>
                <table class="scout-table">
                    <thead>
                        <tr><th>Usuário</th><th>Jogador 1</th><th>Jogador 2</th><th>Data</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ultimasComp as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['usuario']) ?></td>
                            <td><?= htmlspecialchars($c['nick1']) ?></td>
                            <td><?= htmlspecialchars($c['nick2']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($c['data_comparacao'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top: 25px; display: flex; gap: 15px;">
            <a href="importar.php" class="btn">📥 Importar Jogadores (CSV)</a>
            <a href="jogadores.php" class="btn btn-outline">📝 Gerenciar Jogadores</a>
        </div>
    </main>
</body>
</html>
