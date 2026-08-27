<?php
// index.php - Tela e processamento de Login
session_start();
require_once 'conexao.php';

// Se já estiver logado, redireciona para a área restrita
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = 'Informe o e-mail e a senha.';
    } else {
        // Busca o usuário pelo e-mail utilizando MySQLi
        $stmt = $mysqli->prepare("SELECT id, nome, email, senha, perfil FROM usuarios WHERE email = ?");
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $usuario = $resultado->fetch_assoc();
            $stmt->close();

            // Verifica a senha usando password_verify()
            if ($usuario && password_verify($senha, $usuario['senha'])) {
                // Prevenção contra Session Fixation
                session_regenerate_id(true);

                // Armazena dados essenciais na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_perfil'] = $usuario['perfil'];

                header('Location: dashboard.php');
                exit;
            } else {
                // Mensagem genérica por segurança
                $erro = 'E-mail ou senha incorretos.';
            }
        } else {
            $erro = 'Erro no banco de dados ao realizar o login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Login</title>
</head>
<body>
<div>
    <h2>Entrar no Sistema</h2>
    <?php if ($erro): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>
    <form method="POST" action="index.php">
        <div>
            <label>E-mail</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Senha</label>
            <input type="password" name="senha" required>
        </div>
        <button type="submit">Entrar</button>
    </form>
    <p>
        Não tem uma conta? <a href="cadastro.php">Cadastre-se</a>
    </p>
</div>
</body>
</html>