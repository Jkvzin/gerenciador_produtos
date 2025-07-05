<?php
session_start();

$mensagem = "Você saiu do sistema com sucesso!";

$_SESSION = array();

session_destroy();

session_start();

$_SESSION['flash_message'] = [
    'text' => $mensagem,
    'type' => 'info'
];

header("location: login.php");
exit;
?>