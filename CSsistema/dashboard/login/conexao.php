<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sistema_login';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    echo "Erro na conexão: " . $mysqli->connect_error;
} else {
    echo "Conexão realizada com sucesso!";
}
?>
