<?php
require_once '../includes/funcoes.php';

$jsonPath = "../storage/registros.json";
$mensagem = '';
$certificados = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['executar'])) {
        $id = gerarIdUnico();
        $cmd = "sudo /usr/bin/python3 /opt/vpn-cert-generator/gerar_certificado.py $id";
        $output = shell_exec($cmd);

        $origem = "/var/www/html/storage/{$id}_cert.zip";
        $destino = "../storage/{$id}_cert.zip";
        if (file_exists($origem) && !file_exists($destino)) {
            rename($origem, $destino);
        }

        $registro = [
            "id" => $id,
            "data" => date("Y-m-d H:i:s"),
            "validade" => date("Y-m-d", strtotime("+7 days"))
        ];

        $dadosAtuais = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
        $dadosAtuais[] = $registro;
        file_put_contents($jsonPath, json_encode($dadosAtuais, JSON_PRETTY_PRINT));

        $mensagem = "<p style='color: green;'>✅ Certificado gerado com ID: <strong>$id</strong></p><pre>$output</pre>";
    }

    if (isset($_POST['apagar'], $_POST['remover'])) {
        $dadosAtuais = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];

        foreach ($_POST['remover'] as $id) {
            shell_exec("sudo /usr/bin/python3 /opt/vpn-cert-generator/deletar_certificado.py $id");
            $dadosAtuais = array_filter($dadosAtuais, fn($item) => $item['id'] !== $id);
        }

        file_put_contents($jsonPath, json_encode(array_values($dadosAtuais), JSON_PRETTY_PRINT));
        $mensagem .= "<p style='color:red'>❌ Certificados selecionados foram removidos.</p>";
    }
}

// Carrega lista para exibição
if (file_exists($jsonPath)) {
    $certificados = json_decode(file_get_contents($jsonPath), true);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerar Certificado VPN</title>
</head>
<body style="font-family: sans-serif; padding: 20px;">

    <section>
        <h2>Gerar novo certificado VPN</h2>
        <form method="POST">
            <button type="submit" name="executar" value="1" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">
                Criar certificado
            </button>
        </form>
    </section>

    <section style="margin-top: 20px;">
        <?= $mensagem ?>
    </section>

    <hr>

    <section>
        <h3>Certificados existentes</h3>
        <?php if (!empty($certificados)): ?>
            <form method="POST">
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Remover</th>
                            <th>Download</th>
                            <th>ID</th>
                            <th>Data de criação</th>
                            <th>Validade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificados as $cert): ?>
                            <tr>
                                <td><input type="checkbox" name="remover[]" value="<?= htmlspecialchars($cert['id']) ?>"></td>
                                <td><a href="../baixar.php?id=<?= urlencode($cert['id']) ?>">Baixar</a></td>
                                <td><?= htmlspecialchars($cert['id']) ?></td>
                                <td><?= htmlspecialchars($cert['data']) ?></td>
                                <td><?= htmlspecialchars($cert['validade']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <button type="submit" name="apagar" value="1"
                        onclick="return confirm('Você realmente deseja excluir os certificados selecionados?')"
                        style="background-color: red; color: white; padding: 10px; border: none; cursor: pointer;">
                    Remover selecionados
                </button>
            </form>
        <?php else: ?>
            <p>Nenhum certificado gerado ainda.</p>
        <?php endif; ?>
    </section>

</body>
</html>
