<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/conexao.php';

$tituloPagina = "Lista de Administradores";
$mensagem = '';

// Ações via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'], $_POST['selecionados'])) {
    $idsSelecionados = $_POST['selecionados'];
    $acao = $_POST['acao'];

    if ($acao === 'desativar') {
        $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE email = ?");
        foreach ($idsSelecionados as $email) {
            $stmt->execute([$email]);
        }
        $mensagem = "Usuários desativados.";
    } elseif ($acao === 'ativar') {
        $stmt = $pdo->prepare("UPDATE usuarios SET ativo = 1 WHERE email = ?");
        foreach ($idsSelecionados as $email) {
            $stmt->execute([$email]);
        }
        $mensagem = "Usuários ativados.";
    } elseif ($acao === 'remover') {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE email = ?");
        foreach ($idsSelecionados as $email) {
            $stmt->execute([$email]);
        }
        $mensagem = "Usuários removidos.";
    }
}

// Buscar todos os administradores
$stmt = $pdo->query("SELECT email, nome, ativo, ultimo_login FROM usuarios ORDER BY nome");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $tituloPagina ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f4f6f8;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }

        h2 {
            max-width: fit-content;
	    margin-left: auto;
	    margin-right: auto;
            margin-bottom: 20px;
            font-weight: 500;
            color: #2e7d32;
        }

        .message {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 15px;
        }

        .message.success  { background-color: #d0f0d4; color: #1b5e20; }
        .message.warning  { background-color: #fff9c4; color: #795548; }
        .message.danger   { background-color: #ffcdd2; color: #b71c1c; }

        .action-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            max-width: fit-content;
            margin-bottom: 20px;
	    margin-left: auto;
            margin-right: auto;
        }

        .btn {
            padding: 10px 18px;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .btn-green       { background-color: #2e7d32; }
        .btn-green:hover { background-color: #27672b; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #2e7d32;
            color: white;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        tr:nth-child(even) {
            background-color: #f0f0f0;
        }

        .center {
            text-align: center;
        }

        input[type="checkbox"] {
            transform: scale(1.1);
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <h2>Administradores do Sistema</h2>

    <?php if ($mensagem): ?>
        <div class="message <?= str_contains($mensagem, 'remov') ? 'danger' : (str_contains($mensagem, 'desativ') ? 'warning' : 'success') ?>">
            <?= $mensagem ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="action-bar">
            <a href="cadastro.php" class="btn btn-green">Cadastrar Novo</a>
            <button type="submit" name="acao" value="ativar" class="btn btn-green">Ativar</button>
            <button type="submit" name="acao" value="desativar" class="btn btn-green">Desativar</button>
            <button type="submit" name="acao" value="remover" class="btn btn-green" onclick="return confirm('Tem certeza que deseja remover os usuários selecionados?')">Remover</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="center">Selecionar</th>
                    <th>Email</th>
                    <th>Nome</th>
                    <th>Status</th>
		    <th>Último login</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="5" class="center">Nenhum administrador cadastrado.</td></tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td class="center">
                                <input type="checkbox" name="selecionados[]" value="<?= htmlspecialchars($usuario['email']) ?>">
                            </td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td><?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?></td>
			    <td>
                    		<?= $usuario['ultimo_login'] 
                        	? (new DateTime($usuario['ultimo_login']))->format('d/m/Y H:i') 
                        	: '—' ?>
                	    </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>
</body>
</html>

