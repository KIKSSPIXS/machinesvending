<?php
session_start();

// Caminho do arquivo de usuários
$file = 'users.txt';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Processa o login
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Verifica se o arquivo de usuários existe e lê os dados
        $users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

        // Verifica o login
        $loginSuccess = false;
        foreach ($users as $line) {
            // Divide a linha em partes
            list($fileUser, $fileId, $fileBalance, $filePass, $fileAffiliate) = explode(':', $line);

            // Verifica se o nome de usuário e senha coincidem
            if ($fileUser === $username && $filePass === $password) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $fileUser;
                $_SESSION['userId'] = $fileId;
                $_SESSION['balance'] = $fileBalance;
                header('Location: perfil.php'); // Redireciona para a página de perfil
                exit;
            }
        }
        $message = 'Nome de usuário ou senha inválidos.';

    } elseif (isset($_POST['register'])) {
        // Processa o cadastro
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $affiliateCode = trim($_POST['affiliate_code']);

        // Verifica se o arquivo de usuários existe e lê os dados
        $users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

        // Verifica se o nome de usuário já existe
        $userExists = false;
        foreach ($users as $line) {
            list($existingUsername) = explode(':', $line);
            if ($existingUsername === $username) {
                $userExists = true;
                break;
            }
        }

        if ($userExists) {
            $message = 'Usuário já existe. Por favor, escolha outro nome de usuário.';
        } else {
            // Gera um ID numérico aleatório e inicializa o saldo com 0
            $userId = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $initialBalance = 0;

            // Cria a linha de dados do usuário
            $userData = "$username:$userId:$initialBalance:$password:$affiliateCode";

            // Adiciona o novo usuário ao arquivo
            file_put_contents($file, $userData . PHP_EOL, FILE_APPEND);

            // Loga o usuário automaticamente após o cadastro
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['userId'] = $userId;
            $_SESSION['balance'] = $initialBalance;

            // Redireciona para a página de login
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login e Cadastro</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Bem-vindo ao Sistema</h1>
        </header>

        <div class="form-buttons">
            <button id="show-login">Login</button>
            <button id="show-register">Cadastrar</button>
        </div>

        <div id="login-form" class="form">
            <h2>Login</h2>
            <form action="login_process.php" method="POST">
                <div class="input-group">
                    <label for="login-username">Usuário:</label>
                    <input type="text" id="login-username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="login-password">Senha:</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" name="login">Entrar</button>
            </form>
            <?php if (isset($message)) : ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
        </div>

        <div id="register-form" class="form" style="display: none;">
            <h2>Cadastro</h2>
            <form action="login_process.php" method="POST">
                <div class="input-group">
                    <label for="register-username">Usuário:</label>
                    <input type="text" id="register-username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="register-password">Senha:</label>
                    <input type="password" id="register-password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="affiliate-code">Código de Afiliado:</label>
                    <input type="text" id="affiliate-code" name="affiliate_code">
                </div>
                <button type="submit" name="register">Cadastrar</button>
            </form>
            <?php if (isset($message)) : ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('show-login').addEventListener('click', function() {
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
        });

        document.getElementById('show-register').addEventListener('click', function() {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        });
    </script>
</body>
</html>
