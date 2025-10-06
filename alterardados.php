<?php 
include 'conexao.php';

$email_logado = $_SESSION['email'] ?? '';

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email_logado);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$mensagem = '';
$tipo_mensagem = '';

if (isset($_POST['editar'])) {
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);
    $altura = $_POST['altura'];
    $peso = $_POST['peso'];
    $nova_senha = $_POST['senha'];

    if (!empty($nova_senha)) {
        $senha_hash = password_hash($nova_senha, PASSWORD_BCRYPT);
        $sql = "UPDATE usuarios SET telefone = ?, altura = ?, peso = ?, senha = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdds", $telefone, $altura, $peso, $senha_hash, $email_logado);
    } else {
        $sql = "UPDATE usuarios SET telefone = ?, altura = ?, peso = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssds", $telefone, $altura, $peso, $email_logado);
    }

    if ($stmt->execute()) {
        $mensagem = "Dados atualizados com sucesso!";
        $tipo_mensagem = "sucesso";
        $user['telefone'] = $telefone;
        $user['altura'] = $altura;
        $user['peso'] = $peso;
    } else {
        $mensagem = "Erro ao atualizar dados: " . $stmt->error;
        $tipo_mensagem = "erro";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Petops</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="cssgeral.css">
</head>

<body class="bg-light d-flex flex-column min-vh-100"> 
<?php include 'navbar.php'; ?>

<main class="container my-5">
    <div class="card shadow p-4 mx-auto login-container">
        <h2 class="text-center mb-4">Atualizar Dados</h2>

        <?php if ($mensagem): ?>
            <div class="<?= $tipo_mensagem === 'sucesso' ? 'sucesso' : 'erro'; ?>">
                <?= htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <label>Telefone:</label>
            <input type="text" id="telefone" name="telefone" maxlength="15" 
                value="<?= htmlspecialchars(preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $user['telefone'])); ?>" required>

            <label>Altura (m):</label>
            <input type="text" name="altura" value="<?= htmlspecialchars($user['altura']); ?>" required>

            <label>Peso (kg):</label>
            <input type="text" name="peso" value="<?= htmlspecialchars($user['peso']); ?>" required>

            <label>Nova Senha (opcional):</label>
            <input type="password" name="senha" placeholder="Digite nova senha">

            <button class="btn btn-primary" type="submit" name="editar">Atualizar Dados</button>
        </form>
    </div>
</main>

<?php include 'rodape.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Máscara de telefone
    function aplicarMascara(input) {
        input.addEventListener('input', function () {
            let valor = input.value.replace(/\D/g, '');
            if (valor.length > 11) valor = valor.slice(0, 11);
            valor = valor.replace(/(\d{2})(\d)/, '($1) $2')
                         .replace(/(\d{5})(\d{1,4})$/, '$1-$2');
            input.value = valor;
        });
    }

    aplicarMascara(document.getElementById("telefone"));

    // Modo claro/escuro
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
