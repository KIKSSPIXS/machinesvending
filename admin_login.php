<?php
session_start();

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Verifica credenciais do administrador
    if ($username === 'lipe' && $password === 'lipetop') {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $message = 'Credenciais inválidas.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="royal.css">
</head>
<body>
    <div class="container">
        <h1>Login do Administrador</h1>
        <form action="admin_login.php" method="POST">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="login">Entrar</button>
        </form>
        <?php if (isset($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
