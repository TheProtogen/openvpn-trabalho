<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Dashboard VPN Manager</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e8f5e9; /* Verde claro */
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .dashboard {
      background-color: #ffffff;
      padding: 32px 48px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      text-align: center;
      width: 100%;
      max-width: 400px;
    }

    .dashboard h1 {
      margin-bottom: 24px;
      color: #2e7d32; /* Verde escuro */
      font-size: 24px;
    }

    .button-container {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .btn {
      background-color: #43a047; /* Verde médio */
      color: white;
      text-decoration: none;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      transition: background-color 0.25s ease;
    }

    .btn:hover {
      background-color: #388e3c; /* Verde mais escuro no hover */
    }
  </style>
</head>
<body>

<div class="dashboard">
  <h1>Painel de Administração</h1>
  <div class="button-container">
    <a href="certificados.php" class="btn">Gerenciar Certificados</a>
    <a href="adms.php" class="btn">Gerenciar Usuários</a>
  </div>
</div>

</body>
</html>

