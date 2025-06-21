<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/conexao.php';
require_once '../includes/funcoes.php';

$mensagem = '';
$certificados = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['executar'])) {
        $id = gerarIdUnico();
        $cmd = "sudo /usr/bin/python3 /opt/vpn-cert-generator/gerar_certificado.py $id";
        shell_exec($cmd);

        $origem = "/var/www/html/storage/{$id}_cert.zip";
        $destino = "../storage/{$id}_cert.zip";

        if (file_exists($origem) && !file_exists($destino)) {
            rename($origem, $destino);
        }

        $pdo->prepare("INSERT INTO certificados (id, data, validade) VALUES (?, ?, ?)")
            ->execute([
                $id,
                date("Y-m-d H:i:s"),
                date("Y-m-d", strtotime('+7 days'))
            ]);

        $mensagem = "<div class='message success'>Certificado gerado | ID: <strong>$id</strong></div>";
    }

    if (!empty($_POST['apagar']) && !empty($_POST['remover'])) {
        foreach ($_POST['remover'] as $id) {
            shell_exec("sudo /usr/bin/python3 /opt/vpn-cert-generator/deletar_certificado.py $id");
            $pdo->prepare("DELETE FROM certificados WHERE id = ?")->execute([$id]);
        }
        $mensagem .= "<div class='message danger'>Certificados removidos com sucesso.</div>";
    }
}

$stmt = $pdo->query("SELECT * FROM certificados ORDER BY data DESC");
$certificados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel - VPN</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e8f5e9;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        main {
            width: 90%;
            max-width: 900px;
            margin-top: 40px;
        }

        h2, h3 {
            text-align: center;
            color: #2e7d32;
        }

        .message {
            margin: 20px 0;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
        }

        .message.success {
            background-color: #c8e6c9;
            color: #256029;
        }

        .message.danger {
            background-color: #ffcdd2;
            color: #c62828;
        }

        form {
            text-align: center;
            margin-bottom: 30px;
        }

        button {
            background-color: #43a047;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #388e3c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            border: 1px solid #c8e6c9;
            text-align: center;
        }

        th {
            background-color: #a5d6a7;
            color: #1b5e20;
        }

        .btn-download {
            background-color: #66bb6a;
            color: white;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
        }

        .btn-download:hover {
            background-color: #558b2f;
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<main>

    <h2>Gerar novo certificado VPN</h2>

    <?= $mensagem ?>

    <form method="POST">
        <button type="submit" name="executar" value="1">Criar novo certificado</button>
    </form>

    <hr>

    <?php if ($certificados): ?>
        <form method="POST" onsubmit="return confirm('Confirmar exclusão dos certificados selecionados?')">
            <table>
                <thead>
                    <tr>
                        <th>Download</th>
                        <th>ID</th>
                        <th>Gerado em</th>
                        <th>Válido até</th>
			<th>Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($certificados as $cert): ?>
                        <tr>
			    <!-- scrapped
                            <td>
                                <input type="checkbox" name="remover[]" value="<?= htmlspecialchars($cert['id']) ?>">
                            </td>
			    -->
                            <td>
                                <a href="baixar.php?id=<?= urlencode($cert['id']) ?>" class="btn-download">Baixar</a>
                            </td>
                            <td><?= htmlspecialchars($cert['id']) ?></td>
                            <td><?= (new DateTime($cert['data']))->format('d/m/Y H:i') ?></td>
                            <td><?= (new DateTime($cert['validade']))->format('d/m/Y') ?></td>
			    <td>
			        <input type="checkbox" name="remover[]" value="<?= htmlspecialchars($cert['id']) ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top: 20px;">
                <button type="submit" name="apagar" value="1">Remover selecionados</button>
            </div>
        </form>
    <?php else: ?>
        <p style="text-align: center; color: #555;">Nenhum certificado encontrado.</p>
    <?php endif; ?>

</main>

</body>
</html>

