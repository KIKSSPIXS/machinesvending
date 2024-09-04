<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $affiliateCode = trim($_POST['affiliate_code']);

    // Caminho do arquivo de usuários
    $file = 'users.txt';

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
        // Gera um ID aleatório e inicializa o saldo com 0
        $userId = uniqid();
        $initialBalance = 0;

        // Cria a linha de dados do usuário
        $userData = "$username:$password:$initialBalance:$userId:$affiliateCode";

        // Adiciona o novo usuário ao arquivo
        file_put_contents($file, $userData . PHP_EOL, FILE_APPEND);

        // Redireciona para a página de login
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="royal.css">
</head>
<body>
    <div class="register-container">
        <h2>Cadastro</h2>
        <form action="register.php" method="POST">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="affiliate_code">Código de Afiliado (Opcional):</label>
            <input type="text" id="affiliate_code" name="affiliate_code">
            
            <button type="submit">Cadastrar</button>
        </form>
        <?php if (isset($message)) : ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
