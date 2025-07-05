<?php
require_once 'config.php';

$nome = $email = $senha = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["nome"])) || empty(trim($_POST["email"])) || empty(trim($_POST["senha"]))) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        
        if ($stmt = mysqli_prepare($conexao, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $erro = "Este e-mail já está em uso.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }
            mysqli_stmt_close($stmt);
        }

        $nome = trim($_POST["nome"]);
        $senha = trim($_POST["senha"]);

        if (empty($erro)) {
            $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
            
            if ($stmt = mysqli_prepare($conexao, $sql)) {
                mysqli_stmt_bind_param($stmt, "sss", $param_nome, $param_email, $param_senha);
                
                $param_nome = $nome;
                $param_email = $email;
                $param_senha = password_hash($senha, PASSWORD_DEFAULT);
                
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['flash_message'] = [
                        'text' => 'Registro realizado com sucesso! Agora você pode fazer o login.',
                        'type' => 'success'
                    ];
                    
                    header("location: login.php");
                    exit();

                } else {
                    echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    mysqli_close($conexao);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Registrar</h2>
        <p>Por favor, preencha este formulário para criar uma conta.</p>
        <?php if(!empty($erro)){ echo '<div class="alert alert-danger">' . $erro . '</div>'; } ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo $nome; ?>">
            </div>    
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha">
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Registrar">
            </div>
            <p>Já tem uma conta? <a href="login.php">Faça login aqui</a>.</p>
        </form>
    </div>    
</body>
</html>