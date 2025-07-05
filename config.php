<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Seu usuário do MySQL
define('DB_PASSWORD', '');     // Sua senha do MySQL
define('DB_NAME', 'gerenciador_produtos_db');

$conexao = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conexao === false){
    die("ERRO: Não foi possível conectar ao banco de dados. " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");

define('UPLOAD_PATH', 'produtos/imagens/');
?>