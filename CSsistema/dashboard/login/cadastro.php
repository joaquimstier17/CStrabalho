<?php
// cadastro.php - Tela e processamento de cadastro
require_once 'conexao.php';

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $perfil = $_POST['perfil'] ?? 'comum';

    // Validação de segurança do perfil
    if (!in_array($perfil, ['comum', 'admin'])) {
        $perfil = 'comum';
    }

    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = 'Preencha todos os campos.';
        $tipoMensagem = 'erro';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'E-mail inválido.';
        $tipoMensagem = 'erro';
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Prepara a instrução SQL com MySQLi usando interrogações (?)
        $stmt = $mysqli->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");

        if ($stmt) {
            // 'ssss' indica 4 parâmetros do tipo String (s = string)
            $stmt->bind_param("ssss", $nome, $email, $senhaHash, $perfil);

            if ($stmt->execute()) {
                $mensagem = 'Cadastro realizado com sucesso! Você já pode entrar.';
                $tipoMensagem = 'sucesso';
            } else {
                // Código de erro 1062 no MySQL representa chave duplicada (e-mail)
                if ($stmt->errno === 1062) {
                    $mensagem = 'Este e-mail já está cadastrado.';
                } else {
                    $mensagem = 'Erro ao cadastrar usuário.';
                }
                $tipoMensagem = 'erro';
            }
            $stmt->close();
        } else {
            $mensagem = 'Erro no banco de dados ao preparar o cadastro.';
            $tipoMensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Sistema de Login</title>
</head>
<body>
<div>
    <h2>Criar Conta</h2>
    <?php if ($mensagem): ?>
        <p><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>
    <form method="POST" action="cadastro.php">
        <div>
            <label>Nome Completo</label>
            <input type="text" name="nome" required>
        </div>
        <div>
            <label>E-mail</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>Tipo de Conta</label>
            <select name="perfil" required>
                <option value="comum">Usuário Comum</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <div>
            <label>Senha</label>
            <input type="password" name="senha" required minlength="6">
        </div>
        <button type="submit">Cadastrar</button>
    </form>
    <p>
        Já tem uma conta? <a href="index.php">Faça Login</a>
    </p>
</div>
</body>
</html>