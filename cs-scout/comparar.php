<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
checkAuth();

$db = getDB();

$j1 = filter_input(INPUT_GET, 'j1', FILTER_VALIDATE_INT) ?? 0;
$j2 = filter_input(INPUT_GET, 'j2', FILTER_VALIDATE_INT) ?? 0;

// Se não veio j2, mostrar seleção
$allPlayers = $db->query("SELECT id, nick, nome, time, funcao FROM jogadores ORDER BY nick")->fetchAll();

$p1 = null; $p2 = null; $dna1 = null; $dna2 = null;

if ($j1) {
    $stmt = $db->prepare("SELECT * FROM jogadores WHERE id = ?");
    $stmt->execute([$j1]);
    $p1 = $stmt->fetch();
    if ($p1) $dna1 = calculatePlayerDNA($p1);
}
if ($j2) {
    $stmt = $db->prepare("SELECT * FROM jogadores WHERE id = ?");
    $stmt->execute([$j2]);
    $p2 = $stmt->fetch();
    if ($p2) $dna2 = calculatePlayerDNA($p2);
}

// Salvar comparação no histórico
if ($j1 && $j2 && $p1 && $p2) {
    $check = $db->prepare("SELECT id FROM comparacoes WHERE usuario_id = ? AND jogador_1 = ? AND jogador_2 = ?");
    $check->execute([$_SESSION['user_id'], $j1, $j2]);
    if (!$check->fetch()) {
        $ins = $db->prepare("INSERT INTO comparacoes (usuario_id, jogador_1, jogador_2) VALUES (?, ?, ?)");
        $ins->execute([$_SESSION['user_id'], $j1, $j2]);
    }
}

// Histórico de comparações
$histStmt = $db->prepare("SELECT c.*, j1.nick as nick1, j2.nick as nick2 FROM comparacoes c 
    JOIN jogadores j1 ON c.jogador_1 = j1.id 
    JOIN jogadores j2 ON c.jogador_2 = j2.id 
    WHERE c.usuario_id = ? ORDER BY c.data_comparacao DESC LIMIT 10");
$histStmt->execute([$_SESSION['user_id']]);
$historico = $histStmt->fetchAll();

$stats = ['win_rate' => 'Win Rate', 'round_win_rate' => 'Round Win Rate', 'kpr' => 'KPR', 
          'survival' => 'Survival', 'kast' => 'KAST', 'impact' => 'Impact', 'adr' => 'ADR', 'clutch_points' => 'Clutch'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Comparar Jogadores</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <button class="hamburger" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>

    <aside class="sidebar">
        <div class="logo">CS-SCOUT</div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="jogadores.php">Jogadores</a>
            <a href="ranking.php">Ranking</a>
            <a href="comparar.php" class="active">Comparar</a>
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
            <h2>Comparar Jogadores</h2>
        </header>

        <div class="stat-card" style="margin-bottom: 25px;">
            <form method="get" style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end;">
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label>Jogador 1</label>
                    <select name="j1" style="width: 100%;">
                        <option value="">Selecione...</option>
                        <?php foreach ($allPlayers as $jp): ?>
                        <option value="<?= $jp['id'] ?>" <?= $j1==$jp['id']?'selected':'' ?>><?= htmlspecialchars($jp['nick']) ?> (<?= htmlspecialchars($jp['time']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                    <label>Jogador 2</label>
                    <select name="j2" style="width: 100%;">
                        <option value="">Selecione...</option>
                        <?php foreach ($allPlayers as $jp): ?>
                        <option value="<?= $jp['id'] ?>" <?= $j2==$jp['id']?'selected':'' ?>><?= htmlspecialchars($jp['nick']) ?> (<?= htmlspecialchars($jp['time']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn">Comparar</button>
            </form>
        </div>

        <?php if ($p1 && $p2): ?>
        <div class="compare-container">
            <div class="compare-player">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div class="avatar" style="width: 64px; height: 64px; font-size: 1.6rem; margin: 0 auto 10px;"><?= strtoupper(substr($p1['nick'], 0, 1)) ?></div>
                    <h3><?= htmlspecialchars($p1['nick']) ?></h3>
                    <div style="color: var(--text-muted); font-size: 0.85rem;"><?= htmlspecialchars($p1['time']) ?> • <?= htmlspecialchars($p1['funcao']) ?></div>
                    <div style="margin-top: 8px;"><span class="badge-class"><?= $dna1['class'] ?></span></div>
                </div>

                <?php foreach ($stats as $key => $label): 
                    $v1 = (float)$p1[$key];
                    $v2 = (float)$p2[$key];
                    $max = max($v1, $v2) * 1.2;
                    if ($max == 0) $max = 1;
                    $pct1 = ($v1 / $max) * 100;
                ?>
                <div class="compare-bar">
                    <div class="bar-label"><span><?= $label ?></span><span style="color: var(--accent-blue); font-weight: 700;"><?= $v1 ?></span></div>
                    <div class="bar-track">
                        <div class="bar-fill-left" style="width: <?= $pct1 ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="player-dna.php?id=<?= $p1['id'] ?>" class="btn btn-sm">Ver DNA Completo</a>
                </div>
            </div>

            <div class="compare-player">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div class="avatar" style="width: 64px; height: 64px; font-size: 1.6rem; margin: 0 auto 10px; background: linear-gradient(135deg, var(--accent-blue), #0096c7);"><?= strtoupper(substr($p2['nick'], 0, 1)) ?></div>
                    <h3><?= htmlspecialchars($p2['nick']) ?></h3>
                    <div style="color: var(--text-muted); font-size: 0.85rem;"><?= htmlspecialchars($p2['time']) ?> • <?= htmlspecialchars($p2['funcao']) ?></div>
                    <div style="margin-top: 8px;"><span class="badge-class"><?= $dna2['class'] ?></span></div>
                </div>

                <?php foreach ($stats as $key => $label): 
                    $v1 = (float)$p1[$key];
                    $v2 = (float)$p2[$key];
                    $max = max($v1, $v2) * 1.2;
                    if ($max == 0) $max = 1;
                    $pct2 = ($v2 / $max) * 100;
                ?>
                <div class="compare-bar">
                    <div class="bar-label"><span><?= $label ?></span><span style="color: var(--accent-gold); font-weight: 700;"><?= $v2 ?></span></div>
                    <div class="bar-track">
                        <div class="bar-fill-right" style="width: <?= $pct2 ?>%; margin-left: auto;"></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="margin-top: 20px; text-align: center;">
                    <a href="player-dna.php?id=<?= $p2['id'] ?>" class="btn btn-sm">Ver DNA Completo</a>
                </div>
            </div>
        </div>
        <?php elseif ($j1 || $j2): ?>
        <div class="alert alert-info">Selecione dois jogadores válidos para comparar.</div>
        <?php else: ?>
        <div class="alert alert-info">Selecione dois jogadores acima para iniciar a comparação.</div>
        <?php endif; ?>

        <?php if (count($historico) > 0): ?>
        <div style="margin-top: 30px;">
            <h3 style="margin-bottom: 15px;">Histórico de Comparações</h3>
            <div class="table-container">
                <table class="scout-table">
                    <thead>
                        <tr><th>Jogador 1</th><th>Jogador 2</th><th>Data</th><th></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($historico as $h): ?>
                        <tr>
                            <td><?= htmlspecialchars($h['nick1']) ?></td>
                            <td><?= htmlspecialchars($h['nick2']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($h['data_comparacao'])) ?></td>
                            <td><a href="comparar.php?j1=<?= $h['jogador_1'] ?>&j2=<?= $h['jogador_2'] ?>" class="btn btn-sm">Ver</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
