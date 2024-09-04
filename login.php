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
