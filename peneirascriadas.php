<?php
include 'conexao.php';

$email_logado = $_SESSION['email'] ?? '';
if (!$email_logado) {
    header('Location: login.php');
    exit;
}

// Buscar dados do usuário (id e nome)
$stmt = $conn->prepare("SELECT id_usuario, nome_completo FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email_logado);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "Usuário não encontrado.";
    exit;
}

$id_usuario = $user['id_usuario'];

// Buscar peneiras criadas pelo usuário
$stmt = $conn->prepare("SELECT id_peneira, nome_peneira, local, data, modalidade, imagem, cidade FROM peneiras WHERE id_usuario = ? ORDER BY data DESC");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$peneiras = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Petops</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="cssgeral.css" />
</head>
<body class="bg-light d-flex flex-column min-vh-100"> 
<?php include 'navbar.php'; ?>

<main class="container my-5">
    <h2 class="mb-4 text-center text-primary">Minhas Peneiras Criadas</h2>

    <?php if ($peneiras->num_rows === 0): ?>
        <div class="alert alert-info text-center">Você não tem peneiras cadastradas!</div>
    <?php else: ?>

    <div class="row g-4">
        <?php while ($p = $peneiras->fetch_assoc()): ?>
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card shadow h-100">
                    <div class="position-relative">
                        <?php if ($p['imagem']): ?>
                            <img src="<?= htmlspecialchars($p['imagem']) ?>" alt="Imagem da peneira" class="w-100 rounded-top" style="height: 200px; object-fit: cover;" />
                        <?php else: ?>
                            <div class="bg-secondary rounded-top d-flex align-items-center justify-content-center" style="height: 200px; color: white;">Sem imagem</div>
                        <?php endif; ?>
                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                            <?= date('d/m/Y', strtotime($p['data'])) ?>
                        </span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold text-truncate mb-2" title="<?= htmlspecialchars($p['nome_peneira']) ?>">
                            <?= htmlspecialchars($p['nome_peneira']) ?>
                        </h5>
                        <p class="small mb-2"><i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($p['cidade']) ?></p>
                        <p class="small mb-2"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($p['local']) ?></p>
                        <p class="small mb-1"><a href="verpeneira.php?id=<?= $p['id_peneira']; ?>" class="btn btn-primary mt-auto w-100">Ver detalhes</a></p>
                        <p class="small mb-1"><a href="alterar_peneira.php?id_peneira=<?= $p['id_peneira'] ?>" class="btn btn-primary mt-auto w-100">Alterar</a></p>
                        <p class="small mb-1"><a href="usuarios_inscritos.php?id_peneira=<?= $p['id_peneira'] ?>" class="btn btn-primary mt-auto w-100">Usuários</a></p>
                        <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta peneira?');" class="flex-grow-1">
                            <input type="hidden" name="id_peneira" value="<?= $p['id_peneira'] ?>">
                            <button type="submit" name="excluir_peneira" class="btn btn-danger btn-sm w-100">Excluir</button>
                        </form>

                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</main>

<?php include 'rodape.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');

    function setTheme(theme) {
        body.classList.toggle('bg-dark', theme === 'dark');
        body.classList.toggle('bg-light', theme === 'light');
        themeToggle.innerHTML = theme === 'dark'
            ? '<i class="bi bi-sun-fill"></i>'
            : '<i class="bi bi-moon-stars-fill"></i>';
        localStorage.setItem('theme', theme);
    }

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    themeToggle.addEventListener('click', () => {
        const newTheme = body.classList.contains('bg-light') ? 'dark' : 'light';
        setTheme(newTheme);
    });
</script>
</body>
</html>
