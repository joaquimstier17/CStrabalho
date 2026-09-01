<?php
require_once 'config/database.php';

$db = getDB();

$errors = [];
$success = [];

// Criar tabela usuarios
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `usuarios` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `nome` VARCHAR(100) NOT NULL,
      `username` VARCHAR(50) NOT NULL UNIQUE,
      `email` VARCHAR(100) NOT NULL UNIQUE,
      `senha` VARCHAR(255) NOT NULL,
      `tipo` ENUM('user', 'admin') DEFAULT 'user',
      `status` ENUM('ativo', 'bloqueado') DEFAULT 'ativo',
      `data_cadastro` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $success[] = "Tabela 'usuarios' OK.";
} catch (PDOException $e) {
    $errors[] = "Erro usuarios: " . $e->getMessage();
}

// Criar tabela jogadores
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `jogadores` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `nome` VARCHAR(100) NOT NULL,
      `nick` VARCHAR(50) NOT NULL UNIQUE,
      `nacionalidade` VARCHAR(50) DEFAULT 'Desconhecida',
      `time` VARCHAR(50) DEFAULT 'Sem Time',
      `funcao` VARCHAR(50) DEFAULT 'Rifler',
      `imagem` VARCHAR(255) DEFAULT 'default.jpg',
      `win_rate` FLOAT NOT NULL DEFAULT 0,
      `round_win_rate` FLOAT NOT NULL DEFAULT 0,
      `loss_rate` FLOAT NOT NULL DEFAULT 0,
      `kpr` FLOAT NOT NULL DEFAULT 0,
      `survival` FLOAT NOT NULL DEFAULT 0,
      `kast` FLOAT NOT NULL DEFAULT 0,
      `impact` FLOAT NOT NULL DEFAULT 0,
      `adr` FLOAT NOT NULL DEFAULT 0,
      `clutch_points` FLOAT NOT NULL DEFAULT 0,
      `data_cadastro` DATETIME DEFAULT CURRENT_TIMESTAMP,
      `data_atualizacao` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $success[] = "Tabela 'jogadores' OK.";
} catch (PDOException $e) {
    $errors[] = "Erro jogadores: " . $e->getMessage();
}

// Criar tabela favoritos
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `favoritos` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `usuario_id` INT NOT NULL,
      `jogador_id` INT NOT NULL,
      `data_adicionado` DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`jogador_id`) REFERENCES `jogadores`(`id`) ON DELETE CASCADE,
      UNIQUE KEY `user_player_unique` (`usuario_id`, `jogador_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $success[] = "Tabela 'favoritos' OK.";
} catch (PDOException $e) {
    $errors[] = "Erro favoritos: " . $e->getMessage();
}

// Criar tabela comparacoes
try {
    $db->exec("CREATE TABLE IF NOT EXISTS `comparacoes` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `usuario_id` INT NOT NULL,
      `jogador_1` INT NOT NULL,
      `jogador_2` INT NOT NULL,
      `data_comparacao` DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`jogador_1`) REFERENCES `jogadores`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`jogador_2`) REFERENCES `jogadores`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $success[] = "Tabela 'comparacoes' OK.";
} catch (PDOException $e) {
    $errors[] = "Erro comparacoes: " . $e->getMessage();
}

// Inserir ou ATUALIZAR admin padrão (garante que a senha SEMPRE seja admin123)
try {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);

    // Tenta inserir; se já existe, atualiza a senha
    $stmt = $db->prepare("INSERT INTO usuarios (nome, username, email, senha, tipo, status) 
        VALUES (?, ?, ?, ?, 'admin', 'ativo')
        ON DUPLICATE KEY UPDATE 
        senha = VALUES(senha),
        nome = VALUES(nome),
        tipo = VALUES(tipo),
        status = VALUES(status)");
    $stmt->execute(['Administrador', 'admin', 'admin@csscout.com', $hash]);

    $success[] = "Usuário admin criado/atualizado (senha: admin123).";
} catch (PDOException $e) {
    $errors[] = "Erro admin: " . $e->getMessage();
}

// Inserir 20 jogadores iniciais
$players = [
    ['s1mple','Oleksandr Kostyliev','Ucrânia','Natus Vincere','AWPer',76,58,24,0.88,40,76.0,1.43,88.2,0.03],
    ['sh1ro','Dmitry Sokolov','Rússia','Spirit','AWPer',72,57,28,0.76,48,76.0,1.20,78.0,0.04],
    ['device','Nicolai Reedtz','Dinamarca','Astralis','AWPer',75,57,25,0.78,41,74.0,1.25,80.0,0.03],
    ['b1t','Valeriy Vakhovskiy','Ucrânia','Natus Vincere','Rifler',76,58,24,0.72,40,72.0,1.10,74.0,0.02],
    ['electroNic','Denis Sharipov','Rússia','Virtus.pro','Rifler',75,57,25,0.76,39,73.3,1.20,80.0,0.03],
    ['ropz','Robin Kool','Estônia','FaZe Clan','Lurker',72,56,28,0.76,42,75.0,1.20,80.0,0.03],
    ['rain','Håvard Nygaard','Noruega','FaZe Clan','Entry Fragger',73,55,27,0.75,35,71.0,1.20,82.0,0.03],
    ['NiKo','Nikola Kovač','Bósnia e Herzegovina','G2 Esports','Rifler',72,56,28,0.78,42,73.0,1.28,84.1,0.03],
    ['broky','Helvijs Saukants','Letônia','FaZe Clan','AWPer',72,56,28,0.75,41,73.0,1.10,75.0,0.04],
    ['m0NESY','Ilya Osipov','Rússia','G2 Esports','AWPer',71,55,29,0.75,41,73.0,1.20,78.0,0.03],
    ['EliGE','Jonathan Jablonowski','Estados Unidos','Complexity','Rifler',68,53,32,0.77,36,72.0,1.20,82.0,0.03],
    ['blameF','Benjamin Bremer','Dinamarca','Fnatic','IGL',57,51,43,0.76,38,73.0,1.26,86.4,0.04],
    ['Spinx','Lotan Giladi','Israel','Team Vitality','Rifler',74,57,26,0.75,38,75.1,1.15,80.0,0.03],
    ['Ax1Le','Sergey Rykhtorov','Rússia','Cloud9','Rifler',72,56,28,0.78,39,73.0,1.20,82.0,0.03],
    ['Twistzz','Russel Van Dulken','Canadá','Liquid','Rifler',68,54,32,0.73,38,73.0,1.10,77.0,0.03],
    ['frozen','David Čerňanský','Eslováquia','FaZe Clan','Rifler',70,55,30,0.72,42,75.3,1.10,80.8,0.03],
    ['YEKINDAR','Mareks Gaļinskis','Letônia','Liquid','Entry Fragger',63,52,37,0.74,29,68.0,1.36,84.0,0.02],
    ['KRIMZ','Freddy Johansson','Suécia','Fnatic','Support',72,55,28,0.70,37,73.0,1.05,75.0,0.03],
    ['TeSeS','René Madsen','Dinamarca','Heroic','Rifler',68,54,32,0.70,37,71.0,1.05,75.0,0.03],
    ['stavn','Martin Lund','Dinamarca','Astralis','Rifler',70,55,30,0.72,36,72.0,1.10,78.0,0.03]
];

$inserted = 0;
try {
    $stmt = $db->prepare("INSERT IGNORE INTO jogadores (nick, nome, nacionalidade, time, funcao, win_rate, round_win_rate, loss_rate, kpr, survival, kast, impact, adr, clutch_points) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    foreach ($players as $p) {
        $stmt->execute($p);
        if ($stmt->rowCount() > 0) $inserted++;
    }
    $success[] = "$inserted jogadores inseridos.";
} catch (PDOException $e) {
    $errors[] = "Erro jogadores: " . $e->getMessage();
}

$ok = count($errors) === 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CS-SCOUT | Instalação</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #17171b; }
        .install-box { width: 100%; max-width: 600px; padding: 40px; }
        .install-box h1 { color: #e5b849; text-align: center; margin-bottom: 30px; letter-spacing: 2px; }
        .log { background: #1f1f24; border: 1px solid #2d2d35; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .log-item { padding: 8px 0; border-bottom: 1px solid #2d2d35; font-size: 0.9rem; }
        .log-item:last-child { border-bottom: none; }
        .ok { color: #22c55e; }
        .err { color: #ef4444; }
        .btn-center { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="install-box">
        <h1>⚡ CS-SCOUT — Instalação</h1>

        <div class="log">
            <?php if ($ok): ?>
                <div class="log-item ok">✅ Instalação concluída!</div>
                <?php foreach ($success as $s): ?>
                <div class="log-item ok">✓ <?= htmlspecialchars($s) ?></div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="log-item err">❌ Erros:</div>
                <?php foreach ($errors as $e): ?>
                <div class="log-item err">✗ <?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($ok): ?>
        <div class="btn-center">
            <a href="login.php" class="btn" style="padding: 12px 30px; font-size: 1rem;">Ir para o Login</a>
        </div>
        <div style="text-align: center; margin-top: 15px; color: #94a3b8; font-size: 0.85rem;">
            <p><strong>Admin:</strong> admin / admin123</p>
            <p style="margin-top: 8px;">⚠️ Delete o arquivo <code>install.php</code> depois.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
