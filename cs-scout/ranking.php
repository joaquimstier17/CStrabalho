<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
checkAuth();

$db = getDB();

$criterio = $_GET['criterio'] ?? 'impact';
$allowed = ['impact', 'adr', 'kpr', 'win_rate', 'kast', 'survival', 'clutch_points'];
if (!in_array($criterio, $allowed)) $criterio = 'impact';

$labels = [
    'impact' => 'Impacto',
    'adr' => 'ADR',
    'kpr' => 'KPR',
    'win_rate' => 'Win Rate',
    'kast' => 'KAST',
    'survival' => 'Sobrevivência',
    'clutch_points' => 'Clutch'
];

$stmt = $db->query("SELECT * FROM jogadores ORDER BY $criterio DESC LIMIT 50");
$jogadores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Ranking</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <button class="hamburger" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>

    <aside class="sidebar">
        <div class="logo">CS-SCOUT</div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="jogadores.php">Jogadores</a>
            <a href="ranking.php" class="active">Ranking</a>
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
            <h2>Ranking Global</h2>
            <form method="get" style="display: flex; gap: 10px;">
                <select name="criterio" onchange="this.form.submit()" style="background: var(--panel-bg); border: 1px solid var(--panel-border); color: #fff; padding: 8px 14px; border-radius: 6px; font-family: inherit;">
                    <?php foreach ($labels as $key => $label): ?>
                    <option value="<?= $key ?>" <?= $criterio==$key?'selected':'' ?>>Ranking por <?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </header>

        <div class="table-container">
            <table class="scout-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Jogador</th>
                        <th>Time</th>
                        <th>Função</th>
                        <th><?= $labels[$criterio] ?></th>
                        <th>Impact</th>
                        <th>ADR</th>
                        <th>KPR</th>
                        <th>Win Rate</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jogadores as $i => $p): 
                        $rank = $i + 1;
                        $rankClass = $rank <= 3 ? "rank-$rank" : "rank-other";
                    ?>
                    <tr>
                        <td><span class="rank-badge <?= $rankClass ?>"><?= $rank ?></span></td>
                        <td><strong><?= htmlspecialchars($p['nick']) ?></strong></td>
                        <td><?= htmlspecialchars($p['time']) ?></td>
                        <td><?= htmlspecialchars($p['funcao']) ?></td>
                        <td style="color: var(--accent-gold); font-weight: 700;"><?= $p[$criterio] ?></td>
                        <td><?= $p['impact'] ?></td>
                        <td><?= $p['adr'] ?></td>
                        <td><?= $p['kpr'] ?></td>
                        <td><?= $p['win_rate'] ?>%</td>
                        <td><a href="player-dna.php?id=<?= $p['id'] ?>" class="btn btn-sm">DNA</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
