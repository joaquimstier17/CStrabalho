<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function checkAdmin() {
    checkAuth();
    if ($_SESSION['user_tipo'] !== 'admin') {
        header('Location: index.php?error=acesso_negado');
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['user_tipo']) && $_SESSION['user_tipo'] === 'admin';
}

// Algoritmo Matemático de Cálculo do Player DNA & Player Class
function calculatePlayerDNA(array $p): array {
    // Normalizações de 0 a 100 baseadas em limites de esports pro
    $kpr_n = min(100, max(0, (($p['kpr'] - 0.5) / 0.5) * 100));
    $adr_n = min(100, max(0, (($p['adr'] - 50) / 45) * 100));
    $impact_n = min(100, max(0, (($p['impact'] - 0.8) / 0.7) * 100));
    $kast_n = min(100, max(0, (($p['kast'] - 60) / 25) * 100));
    $surv_n = min(100, max(0, (($p['survival'] - 25) / 30) * 100));
    $clutch_n = min(100, max(0, (($p['clutch_points'] - 0.01) / 0.04) * 100));
    $wr_n = min(100, max(0, (($p['win_rate'] - 50) / 35) * 100));

    $aim = round(($kpr_n * 0.45) + ($adr_n * 0.35) + ($impact_n * 0.20));
    $impact = round(($impact_n * 0.50) + ($wr_n * 0.30) + ($kpr_n * 0.20));
    $consistencia = round(($kast_n * 0.60) + ($p['round_win_rate'] * 0.20) + ($wr_n * 0.20));
    $sobrevivencia = round(($surv_n * 0.70) + ($kast_n * 0.30));
    $clutch = round(($clutch_n * 0.70) + ($surv_n * 0.30));
    $firepower = round(($kpr_n * 0.40) + ($adr_n * 0.40) + ($impact_n * 0.20));

    // Determinar Player Class
    $class = "ALL-ROUNDER";
    if ($impact >= 85 && $firepower >= 85) $class = "AWP MASTER";
    elseif ($impact >= 80 && $aim >= 80) $class = "ENTRY FRAGGER";
    elseif ($aim >= 80 && $consistencia >= 80) $class = "RIFLE MACHINE";
    elseif ($clutch >= 80 && $sobrevivencia >= 75) $class = "CLUTCH KING";
    elseif ($sobrevivencia >= 80 && $consistencia >= 80) $class = "SURVIVOR";

    return [
        'aim' => $aim,
        'impact' => $impact,
        'consistencia' => $consistencia,
        'sobrevivencia' => $sobrevivencia,
        'clutch' => $clutch,
        'firepower' => $firepower,
        'class' => $class
    ];
}
