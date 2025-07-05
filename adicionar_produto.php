<?php
require_once 'verifica_login.php';
require_once 'config.php';

$nome = $preco = $quantidade = $descricao = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $preco = trim($_POST['preco']);
    $quantidade = trim($_POST['quantidade']);
    $descricao = trim($_POST['descricao']);
    $imagem_nome = "";

    if (empty($nome) || empty($preco) || empty($quantidade)) {
        $erro = "Nome, Preço e Quantidade são campos obrigatórios.";
    }

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['imagem']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $erro = "Formato de arquivo não permitido. Apenas JPG, JPEG e PNG.";
        }

        $max_size = 2 * 1024 * 1024; // 2MB
        if ($_FILES['imagem']['size'] > $max_size) {
            $erro = "O arquivo excede o tamanho máximo de 2MB.";
        }

        if (empty($erro)) {
            $file_extension = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
            $imagem_nome = uniqid() . '.' . $file_extension;
            $target_file = UPLOAD_PATH . $imagem_nome;

            if (!move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                $erro = "Houve um erro ao fazer o upload da imagem.";
                $imagem_nome = "";
            }
        }
    }

    if (empty($erro)) {
        $sql = "INSERT INTO produtos (nome, preco, quantidade, descricao, imagem) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conexao, $sql)) {
            mysqli_stmt_bind_param($stmt, "sdiss", $nome, $preco, $quantidade, $descricao, $imagem_nome);
            
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                $erro = "Algo deu errado. Por favor, tente novamente.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conexao);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Produto</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Adicionar Produto</h2>
        <?php if(!empty($erro)){ echo '<div class="alert alert-danger">' . $erro . '</div>'; } ?>
        <form action="adicionar_produto.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>Preço</label>
                <input type="number" step="0.01" name="preco" required>
            </div>
            <div class="form-group">
                <label>Quantidade</label>
                <input type="number" name="quantidade" required>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao"></textarea>
            </div>
            <div class="form-group">
                <label>Imagem (JPG, PNG - Máx 2MB)</label>
                <input type="file" name="imagem" accept=".jpg,.jpeg,.png">
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Adicionar">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>