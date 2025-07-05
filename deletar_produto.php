<?php
require_once 'verifica_login.php';
require_once 'config.php';

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql_select_image = "SELECT imagem FROM produtos WHERE id = ?";
    if ($stmt_select = mysqli_prepare($conexao, $sql_select_image)) {
        mysqli_stmt_bind_param($stmt_select, "i", $id);
        mysqli_stmt_execute($stmt_select);
        $result = mysqli_stmt_get_result($stmt_select);
        if ($row = mysqli_fetch_assoc($result)) {
            $imagem_a_deletar = $row['imagem'];
        }
        mysqli_stmt_close($stmt_select);
    }
    
    $sql = "DELETE FROM produtos WHERE id = ?";
    if ($stmt = mysqli_prepare($conexao, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            if (!empty($imagem_a_deletar) && file_exists(UPLOAD_PATH . $imagem_a_deletar)) {
                unlink(UPLOAD_PATH . $imagem_a_deletar);
            }
            header("location: index.php");
            exit();
        } else {
            echo "Ops! Algo deu errado ao deletar. Por favor, tente novamente.";
        }
        mysqli_stmt_close($stmt);
    }
} else {
    header("location: index.php");
    exit();
}
mysqli_close($conexao);
?>