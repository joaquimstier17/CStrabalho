<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAdmin();

$db = getDB();

$mensagem = '';

// Excluir jogador
if (isset($_GET['excluir'])) {
    $id = filter_input(INPUT_GET, 'excluir', FILTER_VALIDATE_INT);
    if ($id) {
        $db->prepare("DELETE FROM jogadores WHERE id = ?")->execute([$id]);
        $mensagem = "<div class='alert alert-success'>Jogador excluído com sucesso.</div>";
    }
}

// Adicionar/Editar jogador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $dados = [
        $_POST['nick'] ?? '',
        $_POST['nome'] ?? '',
        $_POST['nacionalidade'] ?? 'Desconhecida',
        $_POST['time'] ?? 'Sem Time',
        $_POST['funcao'] ?? 'Rifler',
        (float)($_POST['win_rate'] ?? 0),
        (float)($_POST['round_win_rate'] ?? 0),
        (float)($_POST['loss_rate'] ?? 0),
        (float)($_POST['kpr'] ?? 0),
        (float)($_POST['survival'] ?? 0),
        (float)($_POST['kast'] ?? 0),
        (float)($_POST['impact'] ?? 0),
        (float)($_POST['adr'] ?? 0),
        (float)($_POST['clutch_points'] ?? 0),
    ];

    if ($id) {
        $stmt = $db->prepare("UPDATE jogadores SET nick=?, nome=?, nacionalidade=?, time=?, funcao=?, win_rate=?, round_win_rate=?, loss_rate=?, kpr=?, survival=?, kast=?, impact=?, adr=?, clutch_points=? WHERE id=?");
        $dados[] = $id;
        $stmt->execute($dados);
        $mensagem = "<div class='alert alert-success'>Jogador atualizado com sucesso.</div>";
    } else {
        $stmt = $db->prepare("INSERT INTO jogadores (nick, nome, nacionalidade, time, funcao, win_rate, round_win_rate, loss_rate, kpr, survival, kast, impact, adr, clutch_points) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($dados);
        $mensagem = "<div class='alert alert-success'>Jogador cadastrado com sucesso.</div>";
    }
}

$busca = $_GET['busca'] ?? '';
$sql = "SELECT * FROM jogadores WHERE 1=1";
$params = [];
if ($busca) { $sql .= " AND (nick LIKE ? OR nome LIKE ?)"; $params[] = "%$busca%"; $params[] = "%$busca%"; }
$sql .= " ORDER BY id DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$jogadores = $stmt->fetchAll();

// Edição
$editando = null;
if (isset($_GET['editar'])) {
    $editId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
    if ($editId) {
        $editStmt = $db->prepare("SELECT * FROM jogadores WHERE id = ?");
        $editStmt->execute([$editId]);
        $editando = $editStmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT Admin | Gerenciar Jogadores</title>
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
            <a href="dashboard.php">Painel Admin</a>
            <a href="importar.php">Importar CSV</a>
            <a href="jogadores.php" class="active">Gerenciar Jogadores</a>
        </nav>
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['user_nome'] ?? 'Admin') ?></strong>
            <span class="badge-admin">ADMIN</span>
            <a href="../logout.php">Sair</a>
        </div>
    </aside>

    <main class="content">
        <header class="top-bar">
            <h2>Gerenciar Jogadores</h2>
            <div class="search-box">
                <form method="get" style="display: flex; gap: 10px;">
                    <input type="text" name="busca" placeholder="Buscar jogador..." value="<?= htmlspecialchars($busca) ?>">
                    <button type="submit" class="btn">Buscar</button>
                </form>
            </div>
        </header>

        <?= $mensagem ?>

        <div style="margin-bottom: 25px;">
            <button onclick="document.getElementById('form-jogador').style.display='block'; document.getElementById('form-jogador').scrollIntoView();" class="btn">
                + Novo Jogador
            </button>
            <a href="importar.php" class="btn btn-outline">Importar CSV</a>
        </div>

        <div id="form-jogador" class="stat-card" style="margin-bottom: 25px; display: <?= $editando ? 'block' : 'none' ?>;">
            <h3 style="margin-bottom: 20px;"><?= $editando ? 'Editar Jogador' : 'Novo Jogador' ?></h3>
            <form method="post" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px;">
                <?php if ($editando): ?><input type="hidden" name="id" value="<?= $editando['id'] ?>"><?php endif; ?>

                <div class="form-group">
                    <label>Nick *</label>
                    <input type="text" name="nick" value="<?= htmlspecialchars($editando['nick'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Nome Real</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editando['nome'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Nacionalidade</label>
                    <input type="text" name="nacionalidade" value="<?= htmlspecialchars($editando['nacionalidade'] ?? 'Desconhecida') ?>">
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="text" name="time" value="<?= htmlspecialchars($editando['time'] ?? 'Sem Time') ?>">
                </div>
                <div class="form-group">
                    <label>Função</label>
                    <select name="funcao">
                        <option value="Rifler" <?= ($editando['funcao'] ?? '') == 'Rifler' ? 'selected' : '' ?>>Rifler</option>
                        <option value="AWPer" <?= ($editando['funcao'] ?? '') == 'AWPer' ? 'selected' : '' ?>>AWPer</option>
                        <option value="Entry Fragger" <?= ($editando['funcao'] ?? '') == 'Entry Fragger' ? 'selected' : '' ?>>Entry Fragger</option>
                        <option value="Lurker" <?= ($editando['funcao'] ?? '') == 'Lurker' ? 'selected' : '' ?>>Lurker</option>
                        <option value="IGL" <?= ($editando['funcao'] ?? '') == 'IGL' ? 'selected' : '' ?>>IGL</option>
                        <option value="Support" <?= ($editando['funcao'] ?? '') == 'Support' ? 'selected' : '' ?>>Support</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Win Rate (%)</label>
                    <input type="number" step="0.1" name="win_rate" value="<?= $editando['win_rate'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>Round Win Rate (%)</label>
                    <input type="number" step="0.1" name="round_win_rate" value="<?= $editando['round_win_rate'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>Loss Rate (%)</label>
                    <input type="number" step="0.1" name="loss_rate" value="<?= $editando['loss_rate'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>KPR</label>
                    <input type="number" step="0.01" name="kpr" value="<?= $editando['kpr'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>Survival (%)</label>
                    <input type="number" step="0.1" name="survival" value="<?= $editando['survival'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>KAST (%)</label>
                    <input type="number" step="0.1" name="kast" value="<?= $editando['kast'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>Impact</label>
                    <input type="number" step="0.01" name="impact" value="<?= $editando['impact'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>ADR</label>
                    <input type="number" step="0.1" name="adr" value="<?= $editando['adr'] ?? 0 ?>">
                </div>
                <div class="form-group">
                    <label>Clutch Points</label>
                    <input type="number" step="0.01" name="clutch_points" value="<?= $editando['clutch_points'] ?? 0 ?>">
                </div>

                <div style="grid-column: 1 / -1; display: flex; gap: 10px;">
                    <button type="submit" class="btn"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                    <?php if ($editando): ?>
                    <a href="jogadores.php" class="btn btn-outline">Cancelar</a>
                    <?php else: ?>
                    <button type="button" onclick="document.getElementById('form-jogador').style.display='none';" class="btn btn-outline">Cancelar</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="scout-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nick</th>
                        <th>Nome</th>
                        <th>Time</th>
                        <th>Função</th>
                        <th>Impact</th>
                        <th>ADR</th>
                        <th>KPR</th>
                        <th>WR</th>
                        <th style="width: 120px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jogadores as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><strong><?= htmlspecialchars($p['nick']) ?></strong></td>
                        <td><?= htmlspecialchars($p['nome']) ?></td>
                        <td><?= htmlspecialchars($p['time']) ?></td>
                        <td><?= htmlspecialchars($p['funcao']) ?></td>
                        <td><?= $p['impact'] ?></td>
                        <td><?= $p['adr'] ?></td>
                        <td><?= $p['kpr'] ?></td>
                        <td><?= $p['win_rate'] ?>%</td>
                        <td>
                            <a href="?editar=<?= $p['id'] ?>" class="btn btn-sm" style="padding: 4px 10px;">Editar</a>
                            <a href="?excluir=<?= $p['id'] ?>" class="btn btn-danger btn-sm" style="padding: 4px 10px;" onclick="return confirm('Tem certeza que deseja excluir este jogador?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
