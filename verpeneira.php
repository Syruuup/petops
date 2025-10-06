<?php
include 'conexao.php';

$email_logado = $_SESSION['email'];
$result = $conn->query("SELECT id_usuario, nome_completo FROM usuarios WHERE email='$email_logado'");
$user = $result->fetch_assoc();
$id_usuario = $user['id_usuario'];

if (isset($_GET['id'])) {
    $id_peneira = $_GET['id'];

    $stmt = $conn->prepare("SELECT p.id_peneira, p.nome_peneira, p.descricao, p.data, p.local, p.cidade, p.modalidade, p.imagem, u.nome_completo AS criador
                            FROM peneiras p
                            JOIN usuarios u ON p.id_usuario = u.id_usuario
                            WHERE p.id_peneira = ?");
    $stmt->bind_param("i", $id_peneira);
    $stmt->execute();
    $result = $stmt->get_result();
    $peneira = $result->fetch_assoc();

    if (!$peneira) {
        echo "<div class='erro'>Peneira não encontrada!</div>";
        exit;
    }

    $stmt_check = $conn->prepare("SELECT * FROM participacoes WHERE id_usuario = ? AND id_peneira = ?");
    $stmt_check->bind_param("ii", $id_usuario, $id_peneira);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    $is_registered = $check_result->num_rows > 0;

    $stmt_check->close();
    $stmt->close();
} else {
    echo "<div class='erro'>ID da peneira não fornecido!</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Peneira</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="cssgeral.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100"> 
<?php include 'navbar.php'; ?>

<main class="container my-2">
    <div class="card shadow p-2 mx-auto">
        <img src="<?= htmlspecialchars($peneira['imagem']); ?>" class="img-fluid rounded mb-4" style="max-height: 300px; object-fit: cover;" alt="Imagem da Peneira">
        <h2 class="mb-3"><?= htmlspecialchars($peneira['nome_peneira']); ?></h2>
        <p><strong>Modalidade:</strong> <?= htmlspecialchars($peneira['modalidade']); ?></p>
        <p><strong>Criador:</strong> <?= htmlspecialchars($peneira['criador']); ?></p>
        <p><strong>Data:</strong> <?= date("d/m/Y H:i", strtotime($peneira['data'])); ?></p>
        <p><strong>Cidade:</strong> <?= htmlspecialchars($peneira['cidade']); ?></p>
        <p><strong>Local:</strong> <?= htmlspecialchars($peneira['local']); ?></p>
        <p><strong>Descrição:</strong><br><?= nl2br(htmlspecialchars($peneira['descricao'])); ?></p>

        <?php if ($is_registered): ?>
            <button class="btn btn-outline-primary mt-3" disabled>Você já está cadastrado nesta peneira</button>
        <?php else: ?>
            <form method="POST" class="mt-3">
                <input type="hidden" name="id_peneira" value="<?= $id_peneira; ?>">
                <input type="hidden" name="id_usuario" value="<?= $id_usuario; ?>">
                <button type="submit" class="btn btn-primary">Cadastrar-se na Peneira</button>
            </form>
        <?php endif; ?>
    </div>
    <br>
    <br><br>
</main>

<?php include 'rodape.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_peneira']) && isset($_POST['id_usuario'])) {
        $id_peneira = $_POST['id_peneira'];
        $id_usuario = $_POST['id_usuario'];

        $stmt_check = $conn->prepare("SELECT * FROM participacoes WHERE id_usuario = ? AND id_peneira = ?");
        $stmt_check->bind_param("ii", $id_usuario, $id_peneira);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Você já está cadastrado nesta peneira!');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO participacoes (id_usuario, id_peneira) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_usuario, $id_peneira);

            if ($stmt->execute()) {
                echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href = 'home.php';</script>";
            } else {
                echo "<script>alert('Erro ao cadastrar: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        }

        $stmt_check->close();
    }
}
$conn->close();
?>

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
