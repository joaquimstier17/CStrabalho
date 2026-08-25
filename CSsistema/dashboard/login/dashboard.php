<?php
// dashboard.php - Área restrita (Protegida)
session_start();

// Verifica se a sessão está ativa
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$nome = $_SESSION['usuario_nome'] ?? 'Usuário';
$id = $_SESSION['usuario_id'] ?? '';
$email = $_SESSION['usuario_email'] ?? '';
$perfil = $_SESSION['usuario_perfil'] ?? 'comum';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Principal - Área Restrita</title>
</head>
<body>
<div>
    <h1>Bem-vindo(a), <?= htmlspecialchars($nome) ?>!</h1>
    <p>Você está acessando uma área restrita e autenticada com sucesso.</p>
    
    <ul>
        <li><strong>ID do Usuário:</strong> <?= htmlspecialchars($id) ?></li>
        <li><strong>E-mail:</strong> <?= htmlspecialchars($email) ?></li>
        <li><strong>Tipo de Conta:</strong> <?= htmlspecialchars(ucfirst($perfil)) ?></li>
    </ul>

    <?php if ($perfil === 'admin'): ?>
        <div>
            <h3>Painel de Controle do Administrador</h3>
            <p>Você possui privilégios de administrador no sistema.</p>
        </div>
    <?php endif; ?>

    <p>
        <a href="logout.php">Sair / Logout</a>
    </p>
</div>
</body>
</html>