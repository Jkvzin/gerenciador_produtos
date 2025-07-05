<?php
require_once 'verifica_login.php';
require_once 'config.php';

$nome = $preco = $quantidade = $descricao = $imagem_atual = "";
$id = 0;
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nome = trim($_POST['nome']);
    $preco = trim($_POST['preco']);
    $quantidade = trim($_POST['quantidade']);
    $descricao = trim($_POST['descricao']);
    $imagem_atual = $_POST['imagem_atual'];
    $imagem_nome = $imagem_atual;

    if (empty($nome) || empty($preco) || empty($quantidade)) {
        $erro = "Nome, Preço e Quantidade são obrigatórios.";
    }

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = $_FILES['imagem']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $erro = "Formato de arquivo não permitido.";
        }
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($_FILES['imagem']['size'] > $max_size) {
            $erro = "O arquivo excede o tamanho máximo de 2MB.";
        }

        if (empty($erro)) {
            $file_extension = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
            $imagem_nome = uniqid() . '.' . $file_extension;
            $target_file = UPLOAD_PATH . $imagem_nome;

            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                if (!empty($imagem_atual) && file_exists(UPLOAD_PATH . $imagem_atual)) {
                    unlink(UPLOAD_PATH . $imagem_atual);
                }
            } else {
                $erro = "Erro no upload da nova imagem.";
                $imagem_nome = $imagem_atual;
            }
        }
    }

    if (empty($erro)) {
        $sql = "UPDATE produtos SET nome=?, preco=?, quantidade=?, descricao=?, imagem=? WHERE id=?";
        if ($stmt = mysqli_prepare($conexao, $sql)) {
            mysqli_stmt_bind_param($stmt, "sdissi", $nome, $preco, $quantidade, $descricao, $imagem_nome, $id);
            if (mysqli_stmt_execute($stmt)) {
                header("location: index.php");
                exit();
            } else {
                $erro = "Erro ao atualizar o produto.";
            }
            mysqli_stmt_close($stmt);
        }
    }
} else {
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id = trim($_GET["id"]);
        $sql = "SELECT * FROM produtos WHERE id = ?";
        if ($stmt = mysqli_prepare($conexao, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_assoc($result);
                    $nome = $row['nome'];
                    $preco = $row['preco'];
                    $quantidade = $row['quantidade'];
                    $descricao = $row['descricao'];
                    $imagem_atual = $row['imagem'];
                } else {
                    header("location: index.php");
                    exit();
                }
            } else {
                echo "Ops! Algo deu errado.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        header("location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Editar Produto</h2>
        <?php if(!empty($erro)){ echo '<div class="alert alert-danger">' . $erro . '</div>'; } ?>
        <form action="editar_produto.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="imagem_atual" value="<?php echo $imagem_atual; ?>">
            
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo $nome; ?>" required>
            </div>
            <div class="form-group">
                <label>Preço</label>
                <input type="number" step="0.01" name="preco" value="<?php echo $preco; ?>" required>
            </div>
            <div class="form-group">
                <label>Quantidade</label>
                <input type="number" name="quantidade" value="<?php echo $quantidade; ?>" required>
            </div>
            <div class="form-group">
                <label>Descrição</label>
                <textarea name="descricao"><?php echo $descricao; ?></textarea>
            </div>
            <div class="form-group">
                <label>Imagem Atual</label><br>
                <?php if (!empty($imagem_atual) && file_exists(UPLOAD_PATH . $imagem_atual)): ?>
                    <img src="<?php echo UPLOAD_PATH . $imagem_atual; ?>" width="150"><br>
                <?php else: ?>
                    <p>Nenhuma imagem cadastrada.</p>
                <?php endif; ?>
                <label>Trocar Imagem (JPG, PNG - Máx 2MB)</label>
                <input type="file" name="imagem" accept=".jpg,.jpeg,.png">
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Salvar Alterações">
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>