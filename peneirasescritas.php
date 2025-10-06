<?php
include 'conexao.php';

$email_logado = $_SESSION['email'] ?? '';


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
$id_usuario = $user['id_usuario'] ?? null;

$stmt = $conn->prepare("SELECT p.id_peneira, p.nome_peneira, p.data, p.local, p.imagem, p.cidade
                        FROM peneiras p 
                        JOIN participacoes pa ON p.id_peneira = pa.id_peneira 
                        WHERE pa.id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$peneiras = $stmt->get_result();
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
    <h2 class="mb-4 text-center text-primary">Minhas Peneiras inscritas</h2>

    <?php if ($peneiras->num_rows <= 0): ?>
        <div class="alert alert-info text-center">Você ainda não está inscrito em nenhuma peneira.</div>
    <?php else: ?>
        
        <div class="row g-4">
    <?php while ($row = $peneiras->fetch_assoc()): ?>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card shadow h-100">
                <div class="position-relative">
                    <?php if (!empty($row['imagem'])): ?>
                        <img src="<?= htmlspecialchars($row['imagem']); ?>" class="w-100 rounded-top" alt="Imagem da Peneira" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary rounded-top d-flex align-items-center justify-content-center" style="height: 200px; color: white;">
                            Imagem não disponível
                        </div>
                    <?php endif; ?>
                    <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                        <?= date("d/m/Y", strtotime($row['data'])); ?>
                    </span>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold text-truncate mb-2" title="<?= htmlspecialchars($row['nome_peneira']); ?>">
                        <?= htmlspecialchars($row['nome_peneira']); ?>
                    </h5>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($row['cidade']); ?>
                    </p>
                    <p class="small mb-2">
                        <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($row['local']); ?>
                    <p class="small mb-1">
                        <a href="verpeneira.php?id=<?= $row['id_peneira']; ?>" class="btn btn-primary mt-auto w-100">Ver Detalhes</a>
                    </p>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

    <?php endif; ?>
</main>

<?php include 'rodape.php'; ?>

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

<?php $conn->close(); ?>
