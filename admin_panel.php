<?php
session_start();
$file = 'users.txt';

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] || $_SESSION['username'] !== 'lipe') {
    header('Location: royal.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['search_id'])) {
        $searchId = trim($_POST['search_id']);
        $users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
        $foundUsers = array_filter($users, function($line) use ($searchId) {
            list($username, $password, $id) = explode(':', $line);
            return $id == $searchId;
        });
    } elseif (isset($_POST['update_balance'])) {
        $updateId = trim($_POST['update_id']);
        $newBalance = trim($_POST['new_balance']);
        $users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
        $updatedUsers = [];
        foreach ($users as $line) {
            list($username, $password, $id, $balance) = explode(':', $line);
            if ($id == $updateId) {
                $line = "{$username}:{$password}:{$id}:{$newBalance}\n";
            }
            $updatedUsers[] = $line;
        }
        file_put_contents($file, implode('', $updatedUsers));
    }
}

$users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
    <link rel="stylesheet" href="admin_panel.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Painel de Administração</h1>
            <a href="logout.php" class="logout">Sair</a>
        </header>
        
        <form action="admin_panel.php" method="POST">
            <label for="search_id">Pesquisar por ID:</label>
            <input type="text" id="search_id" name="search_id">
            <button type="submit">Pesquisar</button>
        </form>
        
        <h2>Usuários Registrados</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>ID</th>
                    <th>Saldo</th>
                    <th>Senha</th>
                    <th>Afiliado por</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($foundUsers)) {
                    foreach ($foundUsers as $line) {
                        list($username, $password, $id, $balance, $affiliate) = explode(':', $line);
                        echo "<tr>
                            <td>{$username}</td>
                            <td>{$id}</td>
                            <td>R$ {$balance}</td>
                            <td>{$password}</td>
                            <td>{$affiliate}</td>
                        </tr>";
                    }
                } else {
                    foreach ($users as $line) {
                        list($username, $password, $id, $balance, $affiliate) = explode(':', $line);
                        echo "<tr>
                            <td>{$username}</td>
                            <td>{$id}</td>
                            <td>R$ {$balance}</td>
                            <td>{$password}</td>
                            <td>{$affiliate}</td>
                        </tr>";
                    }
                } ?>
            </tbody>
        </table>

        <h2>Alterar Saldo</h2>
        <form action="admin_panel.php" method="POST">
            <label for="update_id">ID do Usuário:</label>
            <input type="text" id="update_id" name="update_id" required>
            <label for="new_balance">Novo Saldo:</label>
            <input type="text" id="new_balance" name="new_balance" required>
            <button type="submit" name="update_balance">Atualizar Saldo</button>
        </form>
    </div>
</body>
</html>
