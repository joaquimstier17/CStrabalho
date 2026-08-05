-- =============================================================================
-- SCRIPT DDL - BANCO DE DADOS MONEYBALL CS:GO (CONFORME DIAGRAMA)
-- =============================================================================

-- 1. TABELA: usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil ENUM('ADMIN', 'TECNICO', 'ANALISTA') NOT NULL DEFAULT 'TECNICO',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABELA: times
CREATE TABLE times (
    id_time INT AUTO_INCREMENT PRIMARY KEY,
    nome_time VARCHAR(100) NOT NULL,
    tag VARCHAR(10) NOT NULL,
    logo_url VARCHAR(255)
);

-- 3. TABELA: campeonatos
CREATE TABLE campeonatos (
    id_campeonato INT AUTO_INCREMENT PRIMARY KEY,
    nome_campeonato VARCHAR(100) NOT NULL,
    tier ENUM('TIER_1', 'TIER_2', 'TIER_3') NOT NULL DEFAULT 'TIER_1',
    ambiente ENUM('LAN', 'ONLINE') NOT NULL DEFAULT 'LAN',
    data_inicio DATE NOT NULL,
    data_fim DATE
);

-- 4. TABELA: jogadores
CREATE TABLE jogadores (
    id_jogador INT AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(50) NOT NULL UNIQUE,
    nome_completo VARCHAR(150),
    nacionalidade VARCHAR(50),
    funcao_principal ENUM('AWPER', 'ENTRY', 'SUPPORT', 'IGL', 'LURKER') NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    id_time INT NULL,
    
    CONSTRAINT fk_jogadores_time 
        FOREIGN KEY (id_time) 
        REFERENCES times(id_time) 
        ON DELETE SET NULL
);

-- 5. TABELA: partidas
CREATE TABLE partidas (
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    id_campeonato INT NOT NULL,
    id_time_vencedor INT NOT NULL,
    id_time_perdedor INT NOT NULL,
    placar VARCHAR(20) NOT NULL,
    fase VARCHAR(50) NOT NULL,
    data_partida DATETIME NOT NULL,
    
    CONSTRAINT fk_partidas_campeonato 
        FOREIGN KEY (id_campeonato) 
        REFERENCES campeonatos(id_campeonato) 
        ON DELETE CASCADE,
        
    CONSTRAINT fk_partidas_vencedor 
        FOREIGN KEY (id_time_vencedor) 
        REFERENCES times(id_time) 
        ON DELETE RESTRICT,
        
    CONSTRAINT fk_partidas_perdedor 
        FOREIGN KEY (id_time_perdedor) 
        REFERENCES times(id_time) 
        ON DELETE RESTRICT
);

-- 6. TABELA: estatisticas_partida
CREATE TABLE estatisticas_partida (
    id_estatistica INT AUTO_INCREMENT PRIMARY KEY,
    id_partida INT NOT NULL,
    id_jogador INT NOT NULL,
    resultado ENUM('VITORIA', 'DERROTA') NOT NULL,
    kills INT NOT NULL DEFAULT 0,
    deaths INT NOT NULL DEFAULT 0,
    assists INT NOT NULL DEFAULT 0,
    adr DECIMAL(4,1) NOT NULL DEFAULT 0.0,
    kast_percent DECIMAL(4,1) NOT NULL DEFAULT 0.0,
    rating_hltv DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    clutches_vencidos INT NOT NULL DEFAULT 0,
    first_kills INT NOT NULL DEFAULT 0,
    moneyball_score DECIMAL(4,2) NOT NULL DEFAULT 0.00,
    
    CONSTRAINT fk_estatisticas_partida 
        FOREIGN KEY (id_partida) 
        REFERENCES partidas(id_partida) 
        ON DELETE CASCADE,
        
    CONSTRAINT fk_estatisticas_jogador 
        FOREIGN KEY (id_jogador) 
        REFERENCES jogadores(id_jogador) 
        ON DELETE CASCADE
);

-- 7. TABELA: relatorios_salvos
CREATE TABLE relatorios_salvos (
    id_relatorio INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    nome_relatorio VARCHAR(100) NOT NULL,
    descricao TEXT,
    id_jogador INT,
    id_time INT,
    id_partida INT,
    jogadores_selecionados_json TEXT,
    times_selecionados_json TEXT,
    partidas_analisadas_json TEXT,
    filtros_aplicados_json TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_relatorios_usuario 
        FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(id_usuario) 
        ON DELETE CASCADE,
        
    CONSTRAINT fk_relatorios_jogador 
        FOREIGN KEY (id_jogador) 
        REFERENCES jogadores(id_jogador) 
        ON DELETE SET NULL,
        
    CONSTRAINT fk_relatorios_time 
        FOREIGN KEY (id_time) 
        REFERENCES times(id_time) 
        ON DELETE SET NULL,
        
    CONSTRAINT fk_relatorios_partida 
        FOREIGN KEY (id_partida) 
        REFERENCES partidas(id_partida) 
        ON DELETE SET NULL
);