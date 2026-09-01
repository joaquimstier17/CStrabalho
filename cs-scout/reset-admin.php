<?php
require_once 'config/database.php';

try {
    $db = getDB();

    // Hash gerado no próprio servidor - 100% compatível
    $hash = password_hash('admin123', PASSWORD_DEFAULT);

    // Remove admin antigo se existir
    $db->prepare("DELETE FROM usuarios WHERE username = 'admin'")->execute();

    // Cria novo admin
    $db->prepare("INSERT INTO usuarios (nome, username, email, senha, tipo) VALUES (?, ?, ?, ?, 'admin')")
       ->execute(['Administrador', 'admin', 'admin@csscout.com', $hash]);

    echo "<h2 style='color:#22c55e; font-family:sans-serif;'>✅ Admin resetado com sucesso!</h2>";
    echo "<p style='font-family:sans-serif;'><strong>Usuário:</strong> admin<br><strong>Senha:</strong> admin123</p>";
    echo "<p style='font-family:sans-serif;'><a href='login.php' style='color:#00b4d8;'>Ir para Login</a></p>";
    echo "<p style='font-family:sans-serif; color:#666; font-size:12px;'>⚠️ Delete este arquivo (reset-admin.php) após usar.</p>";

} catch (Exception $e) {
    echo "<h2 style='color:#ef4444; font-family:sans-serif;'>❌ Erro: " . $e->getMessage() . "</h2>";
}
