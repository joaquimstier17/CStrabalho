CREATE DATABASE IF NOT EXISTS `cs_scout` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cs_scout`;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `senha` VARCHAR(255) NOT NULL,
  `tipo` ENUM('user', 'admin') DEFAULT 'user',
  `status` ENUM('ativo', 'bloqueado') DEFAULT 'ativo',
  `data_cadastro` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Jogadores
CREATE TABLE IF NOT EXISTS `jogadores` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Favoritos
CREATE TABLE IF NOT EXISTS `favoritos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `jogador_id` INT NOT NULL,
  `data_adicionado` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jogador_id`) REFERENCES `jogadores`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_player_unique` (`usuario_id`, `jogador_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Comparações
CREATE TABLE IF NOT EXISTS `comparacoes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NOT NULL,
  `jogador_1` INT NOT NULL,
  `jogador_2` INT NOT NULL,
  `data_comparacao` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jogador_1`) REFERENCES `jogadores`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jogador_2`) REFERENCES `jogadores`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Padrão (Senha: admin123)
INSERT INTO `usuarios` (`nome`, `username`, `email`, `senha`, `tipo`) VALUES
('Administrador', 'admin', 'admin@csscout.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Inserção dos 20 Jogadores Iniciais
INSERT INTO `jogadores` (`nick`, `nome`, `nacionalidade`, `time`, `funcao`, `win_rate`, `round_win_rate`, `loss_rate`, `kpr`, `survival`, `kast`, `impact`, `adr`, `clutch_points`) VALUES
('s1mple', 'Oleksandr Kostyliev', 'Ucrânia', 'Natus Vincere', 'AWPer', 76, 58, 24, 0.88, 40, 76.0, 1.43, 88.2, 0.03),
('sh1ro', 'Dmitry Sokolov', 'Rússia', 'Spirit', 'AWPer', 72, 57, 28, 0.76, 48, 76.0, 1.20, 78.0, 0.04),
('device', 'Nicolai Reedtz', 'Dinamarca', 'Astralis', 'AWPer', 75, 57, 25, 0.78, 41, 74.0, 1.25, 80.0, 0.03),
('b1t', 'Valeriy Vakhovskiy', 'Ucrânia', 'Natus Vincere', 'Rifler', 76, 58, 24, 0.72, 40, 72.0, 1.10, 74.0, 0.02),
('electroNic', 'Denis Sharipov', 'Rússia', 'Virtus.pro', 'Rifler', 75, 57, 25, 0.76, 39, 73.3, 1.20, 80.0, 0.03),
('ropz', 'Robin Kool', 'Estônia', 'FaZe Clan', 'Lurker', 72, 56, 28, 0.76, 42, 75.0, 1.20, 80.0, 0.03),
('rain', 'Håvard Nygaard', 'Noruega', 'FaZe Clan', 'Entry Fragger', 73, 55, 27, 0.75, 35, 71.0, 1.20, 82.0, 0.03),
('NiKo', 'Nikola Kovač', 'Bósnia e Herzegovina', 'G2 Esports', 'Rifler', 72, 56, 28, 0.78, 42, 73.0, 1.28, 84.1, 0.03),
('broky', 'Helvijs Saukants', 'Letônia', 'FaZe Clan', 'AWPer', 72, 56, 28, 0.75, 41, 73.0, 1.10, 75.0, 0.04),
('m0NESY', 'Ilya Osipov', 'Rússia', 'G2 Esports', 'AWPer', 71, 55, 29, 0.75, 41, 73.0, 1.20, 78.0, 0.03),
('EliGE', 'Jonathan Jablonowski', 'Estados Unidos', 'Complexity', 'Rifler', 68, 53, 32, 0.77, 36, 72.0, 1.20, 82.0, 0.03),
('blameF', 'Benjamin Bremer', 'Dinamarca', 'Fnatic', 'IGL', 57, 51, 43, 0.76, 38, 73.0, 1.26, 86.4, 0.04),
('Spinx', 'Lotan Giladi', 'Israel', 'Team Vitality', 'Rifler', 74, 57, 26, 0.75, 38, 75.1, 1.15, 80.0, 0.03),
('Ax1Le', 'Sergey Rykhtorov', 'Rússia', 'Cloud9', 'Rifler', 72, 56, 28, 0.78, 39, 73.0, 1.20, 82.0, 0.03),
('Twistzz', 'Russel Van Dulken', 'Canadá', 'Liquid', 'Rifler', 68, 54, 32, 0.73, 38, 73.0, 1.10, 77.0, 0.03),
('frozen', 'David Čerňanský', 'Eslováquia', 'FaZe Clan', 'Rifler', 70, 55, 30, 0.72, 42, 75.3, 1.10, 80.8, 0.03),
('YEKINDAR', 'Mareks Gaļinskis', 'Letônia', 'Liquid', 'Entry Fragger', 63, 52, 37, 0.74, 29, 68.0, 1.36, 84.0, 0.02),
('KRIMZ', 'Freddy Johansson', 'Suécia', 'Fnatic', 'Support', 72, 55, 28, 0.70, 37, 73.0, 1.05, 75.0, 0.03),
('TeSeS', 'René Madsen', 'Dinamarca', 'Heroic', 'Rifler', 68, 54, 32, 0.70, 37, 71.0, 1.05, 75.0, 0.03),
('stavn', 'Martin Lund', 'Dinamarca', 'Astralis', 'Rifler', 70, 55, 30, 0.72, 36, 72.0, 1.10, 78.0, 0.03);
