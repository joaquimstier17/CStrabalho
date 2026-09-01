<?php
require_once 'config/database.php';

session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($username && $senha) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ? AND status = 'ativo'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_tipo'] = $user['tipo'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuário ou senha incorretos.';
        }
    } else {
        $error = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CS-SCOUT | Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="logo-center">
            <h1>CS-SCOUT</h1>
            <p>Esport Intelligence & Player Scouting</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">Cadastro realizado com sucesso! Faça login.</div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post">
                <div class="form-group">
                    <label>Usuário</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Entrar</button>
            </form>
        </div>

        <div class="login-links">
            Não tem conta? <a href="register.php">Cadastre-se</a>
        </div>
    </div>
</body>
</html>
