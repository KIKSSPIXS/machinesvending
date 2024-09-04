<?php
session_start();

// Verifica se o administrador está autenticado
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Função para ler os dados do arquivo
function getUsersFromFile($file) {
    return file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
}

// Função para salvar os dados no arquivo
function saveUsersToFile($file, $users) {
    file_put_contents($file, implode("\n", $users) . "\n");
}

// Função para atualizar o saldo
function updateBalance(&$users, $searchId, $balanceChange, $operation) {
    foreach ($users as &$line) {
        $data = explode(':', $line);
        if (count($data) >= 5 && $data[1] === $searchId) {
            $currentBalance = (float)$data[2];
            if ($operation === 'add') {
                $newBalance = $currentBalance + $balanceChange;
            } elseif ($operation === 'subtract') {
                $newBalance = $currentBalance - $balanceChange;
            }
            $data[2] = $newBalance; // Atualiza o saldo
            $line = implode(':', $data);
            return true;
        }
    }
    return false;
}

// Função para adicionar solicitação de compra
function addPurchaseRequest($file, $machineId, $userId, $newBalance) {
    $timestamp = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
    file_put_contents($file, "$machineId:$userId:$newBalance:$timestamp\n", FILE_APPEND);
}

// Função para ler solicitações de compras
function getPurchaseRequestsFromFile($file) {
    return file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];
}

$file = 'users.txt';
$purchasesFile = 'purchases.txt';
$users = getUsersFromFile($file);

if (isset($_POST['search'])) {
    $searchId = trim($_POST['search_id']);
    $users = array_filter($users, function($line) use ($searchId) {
        $data = explode(':', $line);
        return count($data) >= 5 && $data[1] === $searchId;
    });
}

if (isset($_POST['update_balance'])) {
    $searchId = trim($_POST['search_id']);
    $balanceChange = (float)trim($_POST['balance_change']);
    $operation = $_POST['operation'];

    if (updateBalance($users, $searchId, $balanceChange, $operation)) {
        saveUsersToFile($file, $users);
        $message = "Saldo atualizado com sucesso!";
    } else {
        $message = "Usuário não encontrado.";
    }
}

if (isset($_POST['confirm_purchase'])) {
    $machineId = trim($_POST['machine_id']);
    $userId = trim($_POST['user_id']);
    $purchaseAmount = (float)trim($_POST['purchase_amount']);
    
    $userBalance = null;
    foreach ($users as $line) {
        $data = explode(':', $line);
        if (count($data) >= 3 && $data[1] === $userId) {
            $userBalance = (float)$data[2];
            break;
        }
    }

    if ($userBalance !== null && $userBalance >= $purchaseAmount) {
        $newBalance = $userBalance - $purchaseAmount;
        if (updateBalance($users, $userId, $purchaseAmount, 'subtract')) {
            saveUsersToFile($file, $users);
            addPurchaseRequest($purchasesFile, $machineId, $userId, $newBalance);
            $message = "Compra confirmada e saldo atualizado!";
        } else {
            $message = "Erro ao atualizar saldo.";
        }
    } else {
        $message = "Saldo insuficiente.";
    }
}

$purchaseRequests = getPurchaseRequestsFromFile($purchasesFile);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header>
        <h1>Painel de Administração</h1>
        <nav>
            <a href="admin_logout.php">Sair</a>
        </nav>
    </header>
    <main>
        <h2>Pesquisar Usuários</h2>
        <form action="admin.php" method="POST">
            <input type="text" name="search_id" placeholder="ID do Usuário" required>
            <button type="submit" name="search">Pesquisar</button>
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
                <?php if (isset($users)): ?>
                    <?php foreach ($users as $line): ?>
                        <?php 
                        $data = explode(':', $line);
                        if (count($data) >= 5):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($data[0]) ?></td>
                                <td><?= htmlspecialchars($data[1]) ?></td>
                                <td><?= htmlspecialchars($data[2]) ?></td>
                                <td><?= htmlspecialchars($data[3]) ?></td>
                                <td><?= htmlspecialchars($data[4]) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>Alterar Saldo</h2>
        <form action="admin.php" method="POST">
            <div>
                <label for="search_id">ID do Usuário:</label>
                <input type="text" id="search_id" name="search_id" required>
            </div>
            <div>
                <label for="balance_change">Alteração de Saldo:</label>
                <input type="number" id="balance_change" name="balance_change" required>
            </div>
            <div>
                <label for="operation">Operação:</label>
                <select id="operation" name="operation">
                    <option value="add">Adicionar</option>
                    <option value="subtract">Subtrair</option>
                </select>
            </div>
            <button type="submit" name="update_balance">Atualizar Saldo</button>
        </form>

        <h2>Solicitações de Compras</h2>
        <table>
            <thead>
                <tr>
                    <th>Máquina</th>
                    <th>ID do Usuário</th>
                    <th>Saldo Atual</th>
                    <th>Data da Compra</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($purchaseRequests)): ?>
                    <?php foreach ($purchaseRequests as $request): ?>
                        <?php 
                        $data = explode(':', $request);
                        if (count($data) >= 4):
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($data[0]) ?></td>
                                <td><?= htmlspecialchars($data[1]) ?></td>
                                <td>R$ <?= htmlspecialchars($data[2]) ?></td>
                                <td><?= htmlspecialchars($data[3]) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (isset($message)): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
