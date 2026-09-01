<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
checkAdmin();

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $ext = pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION);

    if (in_array(strtolower($ext), ['csv'])) {
        $handle = fopen($file, "r");
        $row = 0;
        $inserted = 0;
        $db = getDB();

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $row++;
            if ($row === 1) continue; // Pular Cabeçalho

            $stmt = $db->prepare("INSERT INTO jogadores (nick, nome, nacionalidade, time, funcao, win_rate, round_win_rate, loss_rate, kpr, survival, kast, impact, adr, clutch_points) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    win_rate=VALUES(win_rate), 
                    round_win_rate=VALUES(round_win_rate), 
                    loss_rate=VALUES(loss_rate), 
                    kpr=VALUES(kpr), 
                    survival=VALUES(survival), 
                    kast=VALUES(kast), 
                    impact=VALUES(impact), 
                    adr=VALUES(adr), 
                    clutch_points=VALUES(clutch_points)");

            $stmt->execute([
                $data[0] ?? '',  // Nick
                $data[1] ?? '',  // Nome
                $data[2] ?? 'Desconhecida',  // Nacionalidade
                $data[3] ?? 'Sem Time',  // Time
                $data[4] ?? 'Rifler',  // Função
                (float)($data[5] ?? 0), // Win Rate
                (float)($data[6] ?? 0), // Round Win Rate
                (float)($data[7] ?? 0), // Loss Rate
                (float)($data[8] ?? 0), // KPR
                (float)($data[9] ?? 0), // Survival
                (float)($data[10] ?? 0), // KAST
                (float)($data[11] ?? 0), // Impact
                (float)($data[12] ?? 0), // ADR
                (float)($data[13] ?? 0)  // Clutch
            ]);
            $inserted++;
        }
        fclose($handle);
        $mensagem = "<div class='alert alert-success'>Sucesso! $inserted jogadores importados/atualizados.</div>";
    } else {
        $mensagem = "<div class='alert alert-error'>Por favor, envie um arquivo .CSV válido.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT Admin | Importar Jogadores</title>
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
            <a href="importar.php" class="active">Importar CSV</a>
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
            <h2>Importação em Lote (.CSV)</h2>
            <a href="dashboard.php" class="btn btn-outline btn-sm">← Voltar</a>
        </header>

        <?= $mensagem ?>

        <div style="max-width: 600px;">
            <div class="stat-card">
                <h3 style="margin-bottom: 15px;">Formato do Arquivo CSV</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 15px;">
                    A primeira linha deve ser o cabeçalho. Use vírgula como separador.<br>
                    Colunas: <strong>nick, nome, nacionalidade, time, funcao, win_rate, round_win_rate, loss_rate, kpr, survival, kast, impact, adr, clutch_points</strong>
                </p>
                <div style="background: var(--bg-dark); padding: 12px; border-radius: 6px; font-family: monospace; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 20px; overflow-x: auto;">
                    nick;nome;nacionalidade;time;funcao;win_rate;round_win_rate;loss_rate;kpr;survival;kast;impact;adr;clutch_points<br>
                    s1mple;Oleksandr Kostyliev;Ucrânia;NAVI;AWPer;76;58;24;0.88;40;76;1.43;88.2;0.03
                </div>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Selecione o arquivo estatístico (.CSV):</label>
                        <input type="file" name="excel_file" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn">Confirmar Importação</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
