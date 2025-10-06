<?php
session_start();

$conn = new mysqli("localhost", "root", "", "bdpeneira");
if ($conn->connect_error) die("Erro: " . $conn->connect_error);

$msg = "";
$msg_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";

    if ($email && $senha) {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = ?");
        $stmt->bind_param("ss", $email, $senha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['email'] = $email;
            header("Location: home.php");
            exit();
        } else {
            $msg = "E-mail ou senha incorretos!";
            $msg_class = "erro";
        }

        $stmt->close();
    } else {
        $msg = "Preencha todos os campos!";
        $msg_class = "erro";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Petops</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="cssgeral.css" />
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
<header class="d-flex align-items-center justify-content-between px-3 py-2 text-white">
  <div><img src="img/logotest.png" alt="Logo Petops" height="50" /></div>
  <div class="d-flex align-items-center gap-3">
      <a class="btn btn-outline-light btn-sm" href="index.html">Home</a>
      <button class="btn btn-outline-light" onclick="toggleTheme()" title="Alternar tema">
          <i data-lucide="moon-star"></i>
      </button>
  </div>
</header>

  <main class="flex-fill">
    <div class="login-container">
      <h2 class="text-center mb-4">Login</h2>
      <?php if ($msg): ?>
        <p class="<?= htmlspecialchars($msg_class) ?> text-center"><?= htmlspecialchars($msg) ?></p>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="email" class="form-label">E-mail</label>
          <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        </div>

        <div class="mb-4">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha" required/>
        </div>

        <button type="submit" class="btn btn-primary">Entrar</button>
      </form>

      <div class="text-center mt-3">
        <a href="cadastro.php" class="text-decoration-none">Ainda não tem conta? Cadastre-se</a>
      </div>
    </div>
  </main>

  <?php include 'rodape.php'; ?>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleTheme() {
      const body = document.body;
      const dark = body.classList.contains('bg-dark');
      body.classList.toggle('bg-dark', !dark);
      body.classList.toggle('bg-light', dark);
    }

    lucide.createIcons();
  </script>
</body>
</html>
