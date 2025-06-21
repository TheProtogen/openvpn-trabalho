<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Navbar Verde</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    nav.navbar {
      background-color: #2e7d32; /* verde escuro */
      color: white;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      padding: 20px 40px;
      width: 100%;
    }

    .navbar-brand {
      font-size: 1.4rem;
      color: white;
      text-decoration: none;
      align-items: center;

      max-width: fit-content;
      margin-left: auto;
      margin-right: auto;
    }

    .navbar-toggler {
      display: none;
      font-size: 1.4rem;
      background: none;
      border: none;
      color: white;
      cursor: pointer;
    }

    .navbar-menu {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      width: 100%;
    }

    .navbar-left,
    .navbar-right {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
    }

    .navbar-left {
      flex-grow: 1;
      justify-content: center;
      gap: 20px;
    }

    .nav-link {
      color: white;
      text-decoration: none;
      padding: 10px;
      display: block;
      border-radius: 4px;
      transition: background 0.2s;
    }

    .nav-link:hover {
      background-color: #388e3c; /* verde médio */
    }

    @media (max-width: 768px) {
      .navbar-menu {
        flex-direction: column;
        display: none;
        width: 100%;
      }

      .navbar-menu.show {
        display: flex;
      }

      .navbar-left {
        flex-direction: column;
        align-items: flex-start;
        gap: 0;
        width: 100%;
      }

      .nav-link {
        width: 100%;
        padding: 12px 10px;
      }

      .navbar-toggler {
        display: inline-block;
      }
    }
  </style>
</head>
<body>

<nav class="navbar">
  <a class="navbar-brand">Painel administrador - VPN</a>
  <button class="navbar-toggler" onclick="document.querySelector('.navbar-menu').classList.toggle('show')">
    ☰
  </button>

  <div class="navbar-menu">
    <div class="navbar-left">
      <a class="nav-link" href="/views/logout.php">Logout</a>
      <a class="nav-link" href="/views/certificados.php">Certificados</a>
      <a class="nav-link" href="/views/adms.php">Administradores</a>
      <a class="nav-link" href="/views/cadastro.php">Cadastrar Usuário</a>
    </div>

    <div class="navbar-right">
	<!-- Scrapped -->
    </div>
  </div>
</nav>

</body>
</html>

