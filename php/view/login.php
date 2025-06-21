<?php
session_start();
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

    if (strlen($email) < 3 || strlen($email) > 30 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe o email corretamente.';
    } elseif (empty($senha)) {
        $erro = 'Informe a senha.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['ativo'] && password_verify($senha, $usuario['senha'])) {

	    $stmtUpdate = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?");
    	    $stmtUpdate->execute([$usuario['id']]);

            $_SESSION['usuario'] = [
                'email' => $usuario['email'],
                'nome' => $usuario['nome']
            ];
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>VPN Manager - Acesso</title>
</head>
<body style="margin: 0; padding: 0; height: 100vh; display: flex; justify-content: center; align-items: center; background: #ececec; font-family: sans-serif;">

    <section style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); min-width: 300px;">
        <h2 style="margin-bottom: 20px; text-align: center;">Acesso ao Painel VPN</h2>

        <?php if ($erro): ?>
            <div style="background: #ffe6e6; color: #a10000; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <fieldset style="border: none; padding: 0;">
                <div style="margin-bottom: 15px;">
                    <label for="email">E-mail:</label><br>
                    <input type="email" name="email" id="email" maxlength="30" required style="width: 100%; padding: 8px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="senha">Senha:</label><br>
                    <input type="password" name="senha" id="senha" required style="width: 100%; padding: 8px;">
                </div>

                <button type="submit" style="width: 100%; padding: 10px; background: #333; color: white; border: none; cursor: pointer;">
                    Entrar
                </button>
            </fieldset>
        </form>
    </section>

</body>
</html>
