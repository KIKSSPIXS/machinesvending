<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit;
}

// Caminho do arquivo de usuários
$file = 'users.txt';

// Lê os dados do arquivo
$users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

// Encontra o usuário logado
$currentUser = null;
foreach ($users as $line) {
    $parts = explode(':', $line);
    if (count($parts) === 5) { // Verifica se há exatamente 5 partes
        list($username, $userId, $balance, $password, $affiliateCode) = $parts;
        if ($username === $_SESSION['username']) {
            $currentUser = [
                'username' => $username,
                'userId' => $userId,
                'balance' => $balance,
                'affiliateCode' => $affiliateCode
            ];
            break;
        }
    }
}

// Se o usuário não for encontrado, redireciona para o login
if ($currentUser === null) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="site.css">
</head>
<body>
    <div class="header">
        <div class="nav-icons">
            <a href="site.php"><img src="casa.png" alt="Início"></a>
            <a href="perfil.php"><img src="perfil.png" alt="Perfil"></a>
            <a href="meu_time.php"><img src="team.png" alt="Meu Time"></a>
        </div>
    </div>

    <div class="content">
        <h2>Meu Perfil</h2>
        <p>Saldo: <span id="saldo">R$ <?= htmlspecialchars($currentUser['balance']) ?></span></p>
        <p>ID: <span id="userId"><?= htmlspecialchars($currentUser['userId']) ?></span></p>
    </div>
</body>
</html>
