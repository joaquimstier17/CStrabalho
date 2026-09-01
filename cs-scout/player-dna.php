<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
checkAuth();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? 1;
$stmt = getDB()->prepare("SELECT * FROM jogadores WHERE id = ?");
$stmt->execute([$id]);
$player = $stmt->fetch();

if (!$player) { die("Jogador não encontrado."); }

$dna = calculatePlayerDNA($player);

// Jogadores para dropdown
$allPlayers = getDB()->query("SELECT id, nick FROM jogadores ORDER BY nick")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Player DNA - <?= htmlspecialchars($player['nick']) ?></title>
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
            <a href="comparar.php">Comparar</a>
            <a href="player-dna.php" class="active">Player DNA</a>
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
            <div>
                <h2>Player DNA: <?= htmlspecialchars($player['nick']) ?></h2>
                <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 4px;">
                    <?= htmlspecialchars($player['nome']) ?> • <?= htmlspecialchars($player['time']) ?> • <?= htmlspecialchars($player['funcao']) ?>
                </div>
            </div>
            <span class="badge-class"><?= $dna['class'] ?></span>
        </header>

        <div style="margin-bottom: 20px;">
            <form method="get" style="display: flex; gap: 10px; align-items: center;">
                <label style="color: var(--text-muted); font-size: 0.9rem;">Selecionar jogador:</label>
                <select name="id" onchange="this.form.submit()" style="background: var(--panel-bg); border: 1px solid var(--panel-border); color: #fff; padding: 8px 14px; border-radius: 6px; font-family: inherit;">
                    <?php foreach ($allPlayers as $jp): ?>
                    <option value="<?= $jp['id'] ?>" <?= $jp['id'] == $id ? 'selected' : '' ?>><?= htmlspecialchars($jp['nick']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div class="dna-section">
            <div class="stat-card dna-canvas-wrap">
                <canvas id="dnaCanvas" width="400" height="400"></canvas>
            </div>

            <div class="stat-card dna-stats">
                <h3 style="margin-bottom: 10px; font-size: 1.1rem;">Atributos Calculados (0 - 100)</h3>

                <div class="dna-stat-item">
                    <span class="stat-name">🎯 AIM</span>
                    <span class="stat-value"><?= $dna['aim'] ?></span>
                </div>
                <div class="progress-bar"><div class="fill" style="width: <?= $dna['aim'] ?>%"></div></div>

                <div class="dna-stat-item">
                    <span class="stat-name">💥 IMPACTO</span>
                    <span class="stat-value"><?= $dna['impact'] ?></span>
                </div>
                <div class="progress-bar"><div class="fill" style="width: <?= $dna['impact'] ?>%"></div></div>

                <div class="dna-stat-item">
                    <span class="stat-name">📊 CONSISTÊNCIA</span>
                    <span class="stat-value"><?= $dna['consistencia'] ?></span>
                </div>
                <div class="progress-bar"><div class="fill" style="width: <?= $dna['consistencia'] ?>%"></div></div>

                <div class="dna-stat-item">
                    <span class="stat-name">🛡️ SOBREVIVÊNCIA</span>
                    <span class="stat-value"><?= $dna['sobrevivencia'] ?></span>
                </div>
                <div class="progress-bar"><div class="fill" style="width: <?= $dna['sobrevivencia'] ?>%"></div></div>

                <div class="dna-stat-item">
                    <span class="stat-name">🔥 CLUTCH</span>
                    <span class="stat-value"><?= $dna['clutch'] ?></span>
                </div>
                <div class="progress-bar"><div class="fill" style="width: <?= $dna['clutch'] ?>%"></div></div>

                <div class="dna-stat-item">
                    <span class="stat-name">⚡ FIREPOWER</span>
                    <span class="stat-value"><?= $dna['firepower'] ?></span>
                </div>
                <div class="progress-bar"><div class="fill" style="width: <?= $dna['firepower'] ?>%"></div></div>

                <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--panel-border);">
                    <h4 style="margin-bottom: 10px;">Estatísticas Brutas</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.85rem;">
                        <div>Win Rate: <strong><?= $player['win_rate'] ?>%</strong></div>
                        <div>Round WR: <strong><?= $player['round_win_rate'] ?>%</strong></div>
                        <div>KPR: <strong><?= $player['kpr'] ?></strong></div>
                        <div>ADR: <strong><?= $player['adr'] ?></strong></div>
                        <div>KAST: <strong><?= $player['kast'] ?>%</strong></div>
                        <div>Survival: <strong><?= $player['survival'] ?>%</strong></div>
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <a href="comparar.php?j1=<?= $player['id'] ?>" class="btn">Comparar com outro jogador</a>
                </div>
            </div>
        </div>
    </main>

<script>
const dnaData = {
    "AIM": <?= $dna['aim'] ?>,
    "IMPACT": <?= $dna['impact'] ?>,
    "CONSISTÊNCIA": <?= $dna['consistencia'] ?>,
    "SOBREVIVÊNCIA": <?= $dna['sobrevivencia'] ?>,
    "CLUTCH": <?= $dna['clutch'] ?>,
    "FIREPOWER": <?= $dna['firepower'] ?>
};

function drawRadarChart(canvasId, data) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const centerX = width / 2;
    const centerY = height / 2;
    const radius = 130;

    const keys = Object.keys(data);
    const totalAxes = keys.length;
    const angleStep = (Math.PI * 2) / totalAxes;

    // Ajustar para telas retina
    const dpr = window.devicePixelRatio || 1;
    canvas.width = width * dpr;
    canvas.height = height * dpr;
    canvas.style.width = width + 'px';
    canvas.style.height = height + 'px';
    ctx.scale(dpr, dpr);

    ctx.clearRect(0, 0, width, height);

    // Desenhar Web/Grades do Radar
    ctx.strokeStyle = '#2d2d35';
    ctx.lineWidth = 1;
    for (let level = 1; level <= 5; level++) {
        const r = (radius / 5) * level;
        ctx.beginPath();
        for (let i = 0; i < totalAxes; i++) {
            const angle = i * angleStep - Math.PI / 2;
            const x = centerX + r * Math.cos(angle);
            const y = centerY + r * Math.sin(angle);
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        }
        ctx.closePath();
        ctx.stroke();
    }

    // Eixos e Rótulos
    ctx.fillStyle = '#94a3b8';
    ctx.font = 'bold 11px Inter, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    for (let i = 0; i < totalAxes; i++) {
        const angle = i * angleStep - Math.PI / 2;
        const x = centerX + radius * Math.cos(angle);
        const y = centerY + radius * Math.sin(angle);

        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(x, y);
        ctx.strokeStyle = '#2d2d35';
        ctx.stroke();

        const labelX = centerX + (radius + 28) * Math.cos(angle);
        const labelY = centerY + (radius + 18) * Math.sin(angle);
        ctx.fillStyle = '#e5b849';
        ctx.font = 'bold 11px Inter, sans-serif';
        ctx.fillText(keys[i], labelX, labelY - 6);
        ctx.fillStyle = '#94a3b8';
        ctx.font = '10px Inter, sans-serif';
        ctx.fillText("(" + data[keys[i]] + ")", labelX, labelY + 6);
    }

    // Desenhar a Área do DNA
    ctx.beginPath();
    ctx.fillStyle = 'rgba(229, 184, 73, 0.25)';
    ctx.strokeStyle = '#e5b849';
    ctx.lineWidth = 2.5;

    for (let i = 0; i < totalAxes; i++) {
        const val = data[keys[i]];
        const r = (radius * val) / 100;
        const angle = i * angleStep - Math.PI / 2;
        const x = centerX + r * Math.cos(angle);
        const y = centerY + r * Math.sin(angle);
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    }
    ctx.closePath();
    ctx.fill();
    ctx.stroke();

    // Pontos nos vértices
    for (let i = 0; i < totalAxes; i++) {
        const val = data[keys[i]];
        const r = (radius * val) / 100;
        const angle = i * angleStep - Math.PI / 2;
        const x = centerX + r * Math.cos(angle);
        const y = centerY + r * Math.sin(angle);
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, Math.PI * 2);
        ctx.fillStyle = '#e5b849';
        ctx.fill();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    drawRadarChart('dnaCanvas', dnaData);
});
</script>
</body>
</html>
