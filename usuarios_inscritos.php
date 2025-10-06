<?php
include 'conexao.php';

$email_logado = $_SESSION['email'] ?? '';

$stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email_logado);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$id_usuario = $user['id_usuario'] ?? 0;
$stmt->close();

$mensagem = '';
$result = null;

if (isset($_GET['id_peneira'])) {
    $id_peneira = (int) $_GET['id_peneira'];

    $stmt = $conn->prepare("
        SELECT u.nome_completo, u.email 
        FROM usuarios u
        JOIN participacoes p ON u.id_usuario = p.id_usuario
        WHERE p.id_peneira = ?
    ");
    $stmt->bind_param("i", $id_peneira);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $mensagem = "Nenhum usuário inscrito nesta peneira.";
    }

    $stmt->close();
} else {
    $mensagem = "ID de Peneira não informado.";
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
    <h2 class="mb-4 text-center text-primary">Minha Peneira</h2>

    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($mensagem); ?>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-person-circle me-2"></i>
                                <?= htmlspecialchars($row['nome_completo']); ?>
                            </h5>
                            <p class="card-text">
                                <i class="bi bi-envelope me-2"></i>
                                <?= htmlspecialchars($row['email']); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
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
