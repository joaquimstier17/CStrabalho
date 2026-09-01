<?php
require_once 'config/database.php';
require_once 'includes/auth.php';
checkAuth();

$db = getDB();

$busca = $_GET['busca'] ?? '';
$funcao = $_GET['funcao'] ?? '';
$time = $_GET['time'] ?? '';
$order = $_GET['order'] ?? 'impact';

// Favoritos do usuário para marcar estrelas
$favStmt = $db->prepare("SELECT jogador_id FROM favoritos WHERE usuario_id = ?");
$favStmt->execute([$_SESSION['user_id']]);
$favoritos = array_column($favStmt->fetchAll(), 'jogador_id');

// Times únicos para filtro
$times = $db->query("SELECT DISTINCT time FROM jogadores ORDER BY time")->fetchAll(PDO::FETCH_COLUMN);

// Query principal
$sql = "SELECT * FROM jogadores WHERE 1=1";
$params = [];
if ($busca) { $sql .= " AND (nick LIKE ? OR nome LIKE ?)"; $params[] = "%$busca%"; $params[] = "%$busca%"; }
if ($funcao) { $sql .= " AND funcao = ?"; $params[] = $funcao; }
if ($time) { $sql .= " AND time = ?"; $params[] = $time; }
$allowedOrders = ['nick', 'impact', 'adr', 'kpr', 'win_rate', 'data_cadastro'];
if (!in_array($order, $allowedOrders)) $order = 'impact';
$sql .= " ORDER BY $order DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$jogadores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Jogadores</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <button class="hamburger" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰</button>

    <aside class="sidebar">
        <div class="logo">CS-SCOUT</div>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="jogadores.php" class="active">Jogadores</a>
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
            <h2>Jogadores</h2>
            <div class="search-box">
                <form method="get" style="display: flex; gap: 10px;">
                    <input type="text" name="busca" placeholder="Buscar jogador..." value="<?= htmlspecialchars($busca) ?>">
                    <button type="submit" class="btn">Buscar</button>
                </form>
            </div>
        </header>

        <div class="filters">
            <form method="get" style="display: flex; gap: 12px; flex-wrap: wrap; width: 100%;">
                <?php if ($busca): ?><input type="hidden" name="busca" value="<?= htmlspecialchars($busca) ?>"><?php endif; ?>
                <select name="funcao" onchange="this.form.submit()">
                    <option value="">Todas as Funções</option>
                    <option value="AWPer" <?= $funcao=='AWPer'?'selected':'' ?>>AWPer</option>
                    <option value="Rifler" <?= $funcao=='Rifler'?'selected':'' ?>>Rifler</option>
                    <option value="Entry Fragger" <?= $funcao=='Entry Fragger'?'selected':'' ?>>Entry Fragger</option>
                    <option value="Lurker" <?= $funcao=='Lurker'?'selected':'' ?>>Lurker</option>
                    <option value="IGL" <?= $funcao=='IGL'?'selected':'' ?>>IGL</option>
                    <option value="Support" <?= $funcao=='Support'?'selected':'' ?>>Support</option>
                </select>
                <select name="time" onchange="this.form.submit()">
                    <option value="">Todos os Times</option>
                    <?php foreach ($times as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>" <?= $time==$t?'selected':'' ?>><?= htmlspecialchars($t) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="order" onchange="this.form.submit()">
                    <option value="impact" <?= $order=='impact'?'selected':'' ?>>Ordenar por Impacto</option>
                    <option value="adr" <?= $order=='adr'?'selected':'' ?>>Ordenar por ADR</option>
                    <option value="kpr" <?= $order=='kpr'?'selected':'' ?>>Ordenar por KPR</option>
                    <option value="win_rate" <?= $order=='win_rate'?'selected':'' ?>>Ordenar por Win Rate</option>
                    <option value="nick" <?= $order=='nick'?'selected':'' ?>>Ordenar por Nick</option>
                </select>
                <a href="jogadores.php" class="btn btn-outline btn-sm">Limpar Filtros</a>
            </form>
        </div>

        <div class="cards-grid">
            <?php foreach ($jogadores as $p): 
                $dna = calculatePlayerDNA($p);
                $isFav = in_array($p['id'], $favoritos);
            ?>
            <div class="player-card">
                <div class="card-header">
                    <div class="avatar"><?= strtoupper(substr($p['nick'], 0, 1)) ?></div>
                    <div class="info">
                        <h3><?= htmlspecialchars($p['nick']) ?></h3>
                        <div class="meta"><?= htmlspecialchars($p['time']) ?> • <?= htmlspecialchars($p['funcao']) ?></div>
                        <div style="margin-top: 4px;"><span class="badge-class"><?= $dna['class'] ?></span></div>
                    </div>
                    <form method="post" action="favoritos.php" style="margin-left: auto;">
                        <input type="hidden" name="jogador_id" value="<?= $p['id'] ?>">
                        <input type="hidden" name="acao" value="<?= $isFav ? 'remover' : 'adicionar' ?>">
                        <input type="hidden" name="redirect" value="jogadores.php">
                        <button type="submit" class="fav-btn <?= $isFav ? 'active' : '' ?>" title="<?= $isFav ? 'Remover dos favoritos' : 'Adicionar aos favoritos' ?>">
                            <?= $isFav ? '★' : '☆' ?>
                        </button>
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

        <?php if (count($jogadores) === 0): ?>
        <div style="text-align: center; padding: 60px; color: var(--text-muted);">
            <p style="font-size: 1.2rem; margin-bottom: 10px;">Nenhum jogador encontrado.</p>
            <a href="jogadores.php" class="btn">Ver todos</a>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
