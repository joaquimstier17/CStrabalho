<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
checkAuth();

$db = getDB();

// Estatísticas
$totalJogadores = $db->query("SELECT COUNT(*) FROM jogadores")->fetchColumn();
$totalUsuarios = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalFavoritos = $db->query("SELECT COUNT(*) FROM favoritos WHERE usuario_id = " . $_SESSION['user_id'])->fetchColumn();

// Top 5 jogadores por impact
$topPlayers = $db->query("SELECT * FROM jogadores ORDER BY impact DESC LIMIT 5")->fetchAll();

// Jogadores recentes
$recentPlayers = $db->query("SELECT * FROM jogadores ORDER BY data_cadastro DESC LIMIT 5")->fetchAll();

// Favoritos do usuário
$favStmt = $db->prepare("SELECT j.* FROM jogadores j INNER JOIN favoritos f ON j.id = f.jogador_id WHERE f.usuario_id = ? LIMIT 5");
$favStmt->execute([$_SESSION['user_id']]);
$favoritos = $favStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <button class="hamburger" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>

    <aside class="sidebar">
        <div class="logo">CS-SCOUT</div>
        <nav>
            <a href="index.php" class="active">Dashboard</a>
            <a href="jogadores.php">Jogadores</a>
            <a href="ranking.php">Ranking</a>
            <a href="comparar.php">Comparar</a>
            <a href="player-dna.php">Player DNA</a>
            <a href="favoritos.php">Favoritos</a>
            <?php if (isAdmin()): ?>
            <div class="nav-section">Admin</div>
            <a href="admin/dashboard.php">Painel Admin</a>
            <a href="admin/importar.php">Importar CSV</a>
            <a href="admin/jogadores.php">Gerenciar Jogadores</a>
            <?php endif; ?>
        </nav>
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['user_nome'] ?? 'Usuário') ?></strong>
            <span class="badge-<?= $_SESSION['user_tipo'] ?>"><?= strtoupper($_SESSION['user_tipo']) ?></span>
            <a href="logout.php">Sair</a>
        </div>
    </aside>

    <main class="content">
        <header class="top-bar">
            <h2>Dashboard</h2>
            <span>Bem-vindo de volta, <?= htmlspecialchars($_SESSION['user_nome'] ?? '') ?>!</span>
        </header>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 25px;">
            <div class="stat-card" style="min-width: 200px;">
                <div class="label">Total de Jogadores</div>
                <div class="val"><?= $totalJogadores ?></div>
            </div>
            <div class="stat-card" style="min-width: 200px;">
                <div class="label">Usuários Cadastrados</div>
                <div class="val"><?= $totalUsuarios ?></div>
            </div>
            <div class="stat-card" style="min-width: 200px;">
                <div class="label">Seus Favoritos</div>
                <div class="val"><?= $totalFavoritos ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
            <div class="table-container">
                <div style="padding: 16px; border-bottom: 1px solid var(--panel-border); font-weight: 700; font-size: 0.95rem;">
                    🔥 Top 5 por Impacto
                </div>
                <table class="scout-table">
                    <thead>
                        <tr><th>Jogador</th><th>Time</th><th>Impact</th><th>ADR</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topPlayers as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['nick']) ?></strong></td>
                            <td><?= htmlspecialchars($p['time']) ?></td>
                            <td style="color: var(--accent-gold); font-weight: 700;"><?= $p['impact'] ?></td>
                            <td><?= $p['adr'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-container">
                <div style="padding: 16px; border-bottom: 1px solid var(--panel-border); font-weight: 700; font-size: 0.95rem;">
                    ⭐ Seus Favoritos
                </div>
                <?php if (count($favoritos) > 0): ?>
                <table class="scout-table">
                    <thead>
                        <tr><th>Jogador</th><th>Time</th><th>Função</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($favoritos as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['nick']) ?></strong></td>
                            <td><?= htmlspecialchars($p['time']) ?></td>
                            <td><?= htmlspecialchars($p['funcao']) ?></td>
                            <td><a href="player-dna.php?id=<?= $p['id'] ?>" class="btn btn-sm">Ver DNA</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="padding: 30px; text-align: center; color: var(--text-muted);">
                    Você ainda não tem favoritos. <a href="jogadores.php" style="color: var(--accent-blue);">Explorar jogadores</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <div class="table-container">
                <div style="padding: 16px; border-bottom: 1px solid var(--panel-border); font-weight: 700; font-size: 0.95rem;">
                    🆕 Jogadores Recentes
                </div>
                <table class="scout-table">
                    <thead>
                        <tr><th>Jogador</th><th>Nome</th><th>Nacionalidade</th><th>Time</th><th>Função</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPlayers as $p): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['nick']) ?></strong></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td><?= htmlspecialchars($p['nacionalidade']) ?></td>
                            <td><?= htmlspecialchars($p['time']) ?></td>
                            <td><?= htmlspecialchars($p['funcao']) ?></td>
                            <td><a href="player-dna.php?id=<?= $p['id'] ?>" class="btn btn-sm">Ver DNA</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
