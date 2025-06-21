<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funcoes.php';
require_once __DIR__ . '/../includes/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmarSenha'] ?? '';

    if (strlen($email) < 3 || strlen($email) > 30 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um email válido com até 30 caracteres.';
    } elseif (empty($nome)) {
        $erro = 'Informe o nome completo.';
    } elseif (empty($senha) || empty($confirmarSenha)) {
        $erro = 'Informe a senha e a confirmação de senha.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'A senha e a confirmação de senha não coincidem.';
    } elseif (
        strlen($senha) < 8 ||
        !preg_match('/[A-Za-z]/', $senha) ||
        !preg_match('/\d/', $senha) ||
        !preg_match('/[!@#$%&*\-_\+=]/', $senha)
    ) {
        $erro = 'A senha deve ter no mínimo 8 caracteres, incluir pelo menos uma letra, um número e um caractere especial.';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $existe = $stmt->fetchColumn();

        if ($existe) {
            $erro = 'Já existe um usuário com esse e-mail.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (email, nome, senha, ativo) VALUES (?, ?, ?, 1)");
            if ($stmt->execute([$email, $nome, $hash])) {
                $sucesso = 'Usuário cadastrado com sucesso.';
            } else {
                $erro = 'Erro ao cadastrar usuário.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <style>
        body {
            background: #f4f6f8;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .container {
            background: #fff;
            max-width: 600px;
            margin: auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-control {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .form-text {
            font-size: 13px;
            color: #666;
            margin-top: -12px;
            margin-bottom: 10px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            background: #2e7d32;
            color: white;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #27672b;
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <h2>Cadastro de Usuário</h2>

    <?php if ($erro): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
    <?php elseif ($sucesso): ?>
        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <div class="form-control">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" maxlength="30" required>
            </div>
            <div class="form-control">
                <label for="nome">Nome completo:</label>
                <input type="text" name="nome" id="nome" minlength="3" required>
            </div>
        </div>

        <div class="form-group">
            <div class="form-control">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" minlength="8" required>
            </div>
            <div class="form-control">
                <label for="confirmarSenha">Confirme a senha:</label>
                <input type="password" name="confirmarSenha" id="confirmarSenha" minlength="8" required>
            </div>
        </div>

        <div class="form-text">
            Por favor digite uma senha forte, usando letras maiúsculas, números e caractéres especiais.
        </div>

        <button type="submit" class="btn">Cadastrar</button>
    </form>
</div>

</body>
</html>

