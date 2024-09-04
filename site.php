<?php
session_start();

// Verifica se o usuário está autenticado
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit;
}

// Caminho dos arquivos
$file = 'users.txt';
$logFile = 'purchases.txt';

// Lê os dados do arquivo de usuários
$users = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

// Verifica se uma compra foi feita
if (isset($_POST['machine_id']) && isset($_POST['price']) && isset($_POST['return_amount'])) {
    $machineId = trim($_POST['machine_id']);
    $price = floatval(trim($_POST['price']));
    $returnAmount = floatval(trim($_POST['return_amount']));
    $username = $_SESSION['username'];

    $updatedUsers = [];
    $purchaseLogged = false;

    foreach ($users as $line) {
        $data = explode(':', $line);
        if (count($data) === 5) {
            list($user, $userIdFromFile, $balance, $password, $affiliateCode) = $data;

            if ($user === $username) {
                if ($balance >= $price) {
                    $balance -= $price;
                    $updatedUsers[] = "$user:$userIdFromFile:$balance:$password:$affiliateCode";
                    
                    // Log da compra
                    $logEntry = "$machineId:$userIdFromFile:$balance:" . date('Y-m-d H:i:s') . "\n";
                    file_put_contents($logFile, $logEntry, FILE_APPEND);

                    $purchaseLogged = true;
                } else {
                    echo '<p>Saldo insuficiente para a compra.</p>';
                }
            } else {
                $updatedUsers[] = $line;
            }
        }
    }

    if ($purchaseLogged) {
        // Salva as mudanças no arquivo
        file_put_contents($file, implode("\n", $updatedUsers) . "\n");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela Inicial</title>
    <link rel="stylesheet" href="site.css">
</head>
<body>
    <header class="header">
        <div class="nav-icons">
            <a href="site.php"><img src="" alt="Início"></a>
            <a href="perfil.php"><img src="https://webstockreview.net/images/clipart-people-symbol-5.png" alt="Perfil"></a>
        </div>
    </header>

    <div class="content">
        <h2>Comprar Máquinas de Vendas Automáticas</h2>
        
        <!-- Máquina 1 -->
        <div class="item-box">
            <div class="machine-image">
                <img src="https://th.bing.com/th/id/R.2d694882c5847f87c0fc66db55fcb5e5?rik=JR59d1KLAhnJBQ&riu=http%3a%2f%2fportuguese.winnsen.com%2fphoto%2fps31206264-large_daily_products_crs_vending_machine_with_elevator_system_and_remote_control_platform.jpg&ehk=2%2fofuneMkz%2fgCan7dUj55IBM4%2bRt24ov8sOWnnKZtp8%3d&risl=&pid=ImgRaw&r=0" alt="Máquina 1">
            </div>
            <div class="item-info">
                <h3>Máquina 1</h3>
                <p>Valor: <span class="value">R$ 9</span></p>
                <p>Retorno: <span class="return">R$ 16</span></p>
                <p>Tempo: <span class="time">2 dias</span></p>
                <p>Compra: <span class="buy">1 vez</span></p>
                <form action="site.php" method="POST" onsubmit="return confirmPurchase('Máquina 1', 9, 16)">
                    <input type="hidden" name="machine_id" value="1">
                    <input type="hidden" name="price" value="9">
                    <input type="hidden" name="return_amount" value="16">
                    <button type="submit">Compre Agora</button>
                </form>
            </div>
        </div>
        
        <!-- Máquina 2 -->
        <div class="item-box">
            <div class="machine-image">
                <img src="https://th.bing.com/th/id/OIP.FPOXXLTWgO_G_rJjlkijmAHaHa?w=750&h=750&rs=1&pid=ImgDetMain" alt="Máquina 2">
            </div>
            <div class="item-info">
                <h3>Máquina 2</h3>
                <p>Valor: <span class="value">R$ 19</span></p>
                <p>Retorno: <span class="return">R$ 26</span></p>
                <p>Tempo: <span class="time">4 dias</span></p>
                <p>Compra: <span class="buy">1 vez</span></p>
                <form action="site.php" method="POST" onsubmit="return confirmPurchase('Máquina 2', 19, 26)">
                    <input type="hidden" name="machine_id" value="2">
                    <input type="hidden" name="price" value="19">
                    <input type="hidden" name="return_amount" value="26">
                    <button type="submit">Compre Agora</button>
                </form>
            </div>
        </div>
        
        <!-- Máquina 3 -->
        <div class="item-box">
            <div class="machine-image">
                <img src="https://th.bing.com/th/id/OIP.JPC2peIi1EQ1F1v72rN1OAHaHa?w=1500&h=1500&rs=1&pid=ImgDetMain" alt="Máquina 3">
            </div>
            <div class="item-info">
                <h3>Máquina 3</h3>
                <p>Valor: <span class="value">R$ 34</span></p>
                <p>Retorno: <span class="return">R$ 48</span></p>
                <p>Tempo: <span class="time">4 dias</span></p>
                <p>Compra: <span class="buy">1 vez</span></p>
                <form action="site.php" method="POST" onsubmit="return confirmPurchase('Máquina 3', 34, 48)">
                    <input type="hidden" name="machine_id" value="3">
                    <input type="hidden" name="price" value="34">
                    <input type="hidden" name="return_amount" value="48">
                    <button type="submit">Compre Agora</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmPurchase(machine, price, returnAmount) {
            return confirm(`Você deseja realmente comprar a ${machine} por R$ ${price}?`);
        }
    </script>
</body>
</html>
