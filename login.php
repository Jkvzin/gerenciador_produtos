<?php
require_once 'config.php';

$email = $senha = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"])) || empty(trim($_POST["senha"]))) {
        $erro = "Por favor, insira o e-mail e a senha.";
    } else {
        $email = trim($_POST["email"]);
        $senha = trim($_POST["senha"]);
    }
    
    if (empty($erro)) {
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ?";
        
        if ($stmt = mysqli_prepare($conexao, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {                    
                    mysqli_stmt_bind_result($stmt, $id, $nome, $db_email, $hashed_senha);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($senha, $hashed_senha)) {
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nome"] = $nome;                            
                            
                            header("location: index.php");
                        } else {
                            $erro = "A senha que você inseriu não é válida.";
                        }
                    }
                } else {
                    $erro = "Nenhuma conta encontrada com esse e-mail.";
                }
            } else {
                echo "Ops! Algo deu errado. Por favor, tente novamente mais tarde.";
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
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>

        <?php 
        if(isset($_SESSION['flash_message'])){
            $message = $_SESSION['flash_message']['text'];
            $type = $_SESSION['flash_message']['type'];
            
            echo '<div class="alert alert-' . htmlspecialchars($type) . '">' . htmlspecialchars($message) . '</div>';
            
            unset($_SESSION['flash_message']);
        }
        ?>
        
        <p>Por favor, preencha suas credenciais para fazer login.</p>
        <?php if(!empty($erro)){ echo '<div class="alert alert-danger">' . $erro . '</div>'; } ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
            </div>    
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha">
            </div>
            <div class="form-group">
                <input type="submit" class="btn" value="Entrar">
            </div>
            <p>Não tem uma conta? <a href="registrar.php">Registre-se agora</a>.</p>
        </form>
    </div>    
</body>
</html>