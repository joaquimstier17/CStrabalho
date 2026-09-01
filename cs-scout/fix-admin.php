<?php
require_once 'config/database.php';

try {
    $db = getDB();

    // Garantir que a tabela existe
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

    // Hash 100% testado para 'admin123'
    $hash = '$2y$10$vQ5jXlQKQYj3lQKQYj3lQOeQ5jXlQKQYj3lQKQYj3lQOeQ5jXlQKQy'; // NÃO, isso é fake

    // Gerar hash correto no PHP
    $hash = password_hash('admin123', PASSWORD_DEFAULT);

    // Verificar se admin existe
    $check = $db->query("SELECT id FROM usuarios WHERE username = 'admin'")->fetch();

    if ($check) {
        // Atualizar senha
        $db->prepare("UPDATE usuarios SET senha = ?, status = 'ativo', tipo = 'admin' WHERE username = 'admin'")
           ->execute([$hash]);
        echo "✅ Senha do admin atualizada para: <strong>admin123</strong><br>";
        echo "Hash gerado: " . substr($hash, 0, 30) . "...<br>";
    } else {
        // Criar admin
        $db->prepare("INSERT INTO usuarios (nome, username, email, senha, tipo, status) VALUES (?, ?, ?, ?, 'admin', 'ativo')")
           ->execute(['Administrador', 'admin', 'admin@csscout.com', $hash]);
        echo "✅ Usuário admin criado com senha: <strong>admin123</strong><br>";
    }

    // Testar o hash
    $stmt = $db->prepare("SELECT senha FROM usuarios WHERE username = 'admin'");
    $stmt->execute();
    $row = $stmt->fetch();
    $verify = password_verify('admin123', $row['senha']);
    echo "Teste password_verify: " . ($verify ? "✅ OK" : "❌ FALHOU") . "<br><br>";

    echo '<a href="login.php" style="color:#00b4d8;">Ir para Login</a>';

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>
