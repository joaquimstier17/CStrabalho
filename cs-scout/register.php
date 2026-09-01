<?php
require_once 'config/database.php';

session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if (!$nome || !$username || !$email || !$senha) {
        $error = 'Preencha todos os campos obrigatórios.';
    } elseif ($senha !== $senha2) {
        $error = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 6) {
        $error = 'A senha deve ter pelo menos 6 caracteres.';
    } else {
        $db = getDB();

        // Verificar duplicidade
        $check = $db->prepare("SELECT id FROM usuarios WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $error = 'Usuário ou email já cadastrado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO usuarios (nome, username, email, senha, tipo) VALUES (?, ?, ?, ?, 'user')");
            $stmt->execute([$nome, $username, $email, $hash]);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Cadastro</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="logo-center">
            <h1>CS-SCOUT</h1>
            <p>Crie sua conta</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success">Cadastro realizado! Redirecionando...</div>
        <script>setTimeout(() => window.location = 'login.php?registered=1', 1500);</script>
        <?php else: ?>
        <div class="form-container">
            <form method="post">
                <div class="form-group">
                    <label>Nome Completo</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group">
                    <label>Usuário</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                    <small>Mínimo 6 caracteres</small>
                </div>
                <div class="form-group">
                    <label>Confirmar Senha</label>
                    <input type="password" name="senha2" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Cadastrar</button>
            </form>
        </div>

        <div class="login-links">
            Já tem conta? <a href="login.php">Faça login</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
