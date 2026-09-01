<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
checkAuth();

$db = getDB();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jogador_id = filter_input(INPUT_POST, 'jogador_id', FILTER_VALIDATE_INT);
    $acao = $_POST['acao'] ?? '';
    $redirect = $_POST['redirect'] ?? 'favoritos.php';

    if ($jogador_id) {
        if ($acao === 'adicionar') {
            $stmt = $db->prepare("INSERT IGNORE INTO favoritos (usuario_id, jogador_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $jogador_id]);
        } elseif ($acao === 'remover') {
            $stmt = $db->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND jogador_id = ?");
            $stmt->execute([$_SESSION['user_id'], $jogador_id]);
        }
    }
    header("Location: $redirect");
    exit;
}

// Listar favoritos
$stmt = $db->prepare("SELECT j.* FROM jogadores j INNER JOIN favoritos f ON j.id = f.jogador_id WHERE f.usuario_id = ? ORDER BY f.data_adicionado DESC");
$stmt->execute([$_SESSION['user_id']]);
$favoritos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Favoritos</title>
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
            <a href="player-dna.php">Player DNA</a>
            <a href="favoritos.php" class="active">Favoritos</a>
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
            <h2>Meus Favoritos</h2>
            <span><?= count($favoritos) ?> jogador(es) favoritado(s)</span>
        </header>

        <?php if (count($favoritos) > 0): ?>
        <div class="cards-grid">
            <?php foreach ($favoritos as $p): 
                $dna = calculatePlayerDNA($p);
            ?>
            <div class="player-card">
                <div class="card-header">
                    <div class="avatar"><?= strtoupper(substr($p['nick'], 0, 1)) ?></div>
                    <div class="info">
                        <h3><?= htmlspecialchars($p['nick']) ?></h3>
                        <div class="meta"><?= htmlspecialchars($p['time']) ?> • <?= htmlspecialchars($p['funcao']) ?></div>
                        <div style="margin-top: 4px;"><span class="badge-class"><?= $dna['class'] ?></span></div>
                    </div>
                    <form method="post" style="margin-left: auto;">
                        <input type="hidden" name="jogador_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="acao" value="remover">
                        <button type="submit" class="fav-btn active" title="Remover dos favoritos">★</button>
                    </form>
                </div>
                <div class="stats-row">
                    <div class="stat"><div class="num"><?= $p['impact'] ?></div><div class="lbl">Impact</div></div>
                    <div class="stat"><div class="num"><?= $p['adr'] ?></div><div class="lbl">ADR</div></div>
                    <div class="stat"><div class="num"><?= $p['kpr'] ?></div><div class="lbl">KPR</div></div>
                    <div class="stat"><div class="num"><?= $p['win_rate'] ?>%</div><div class="lbl">WR</div></div>
                </div>
                <div class="actions">
                    <a href="player-dna.php?id=<?= $p['id'] ?>" class="btn btn-sm">Ver DNA</a>
                    <a href="comparar.php?j1=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Comparar</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 80px 20px;">
            <div style="font-size: 3rem; margin-bottom: 16px;">☆</div>
            <h3 style="margin-bottom: 10px;">Nenhum favorito ainda</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Adicione jogadores aos seus favoritos para acompanhá-los facilmente.</p>
            <a href="jogadores.php" class="btn">Explorar Jogadores</a>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
