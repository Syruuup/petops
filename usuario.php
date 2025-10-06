<?php  
include 'conexao.php';

$email_logado = $_SESSION['email'] ?? '';

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email_logado);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (isset($_POST['excluir_usuario'])) {
    $conn->query("DELETE FROM participacoes WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email_logado')");
    $conn->query("DELETE FROM peneiras WHERE id_usuario = (SELECT id_usuario FROM usuarios WHERE email = '$email_logado')");
    $conn->query("DELETE FROM usuarios WHERE email = '$email_logado'");
    $conn->close();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_POST['sair_conta'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

function formatar_cpf($cpf) {
    return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cpf);
}

function formatar_telefone($telefone) {
    return preg_replace("/(\d{2})(\d{5})(\d{4})/", "(\$1) \$2-\$3", $telefone);
}

$cpf_formatado = formatar_cpf($user['cpf'] ?? '');
$telefone_formatado = formatar_telefone($user['telefone'] ?? '');
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="cssgeral.css">
</head>

<body class="bg-light d-flex flex-column min-vh-100"> 
<?php include 'navbar.php'; ?>

<main class="container my-5">
    <div class="card shadow p-4 mx-auto login-container">
        <h2 class="text-center mb-4">Dados do Usuário</h2>
        
        <p><strong>Nome:</strong> <?= htmlspecialchars($user['nome_completo']); ?></p>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($user['email']); ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($telefone_formatado); ?></p>
        <p><strong>CPF:</strong> <?= htmlspecialchars($cpf_formatado); ?></p>
        <p><strong>Data de Nascimento:</strong> <?= htmlspecialchars($user['data_nascimento']); ?></p>
        <p><strong>Altura:</strong> <?= htmlspecialchars($user['altura']); ?> m</p>
        <p><strong>Peso:</strong> <?= htmlspecialchars($user['peso']); ?> kg</p>

        <div class="d-flex flex-column gap-2 mt-4">
            <form method="post">
                <a href="alterardados.php" class="btn btn-primary">Atualizar Dados</a>
                <button type="submit" name="sair_conta" class="btn btn-primary" onclick="return confirm('Tem certeza que deseja sair sua conta?')">Sair da Conta</button>
                <button type="submit" name="excluir_usuario" class="btn btn-primary" onclick="return confirm('Tem certeza que deseja excluir sua conta?')">Excluir Conta</button>
            </form>
        </div>
</main>

<?php include 'rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');

    const setTheme = (theme) => {
        body.classList.toggle('bg-dark', theme === 'dark');
        body.classList.toggle('bg-light', theme === 'light');
        themeToggle.innerHTML = theme === 'dark'
            ? '<i class="bi bi-sun-fill"></i>'
            : '<i class="bi bi-moon-stars-fill"></i>';
        localStorage.setItem('theme', theme);
    };

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    themeToggle.addEventListener('click', () => {
        const newTheme = body.classList.contains('bg-light') ? 'dark' : 'light';
        setTheme(newTheme);
    });
</script>

</body>
</html>
