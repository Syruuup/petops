<?php
require_once 'conexao.php';

$email = $_SESSION['email'] ?? '';
$userStmt = $conn->prepare('SELECT nome_completo FROM usuarios WHERE email = ?');
$userStmt->bind_param('s', $email);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

$cidade = $_GET['cidade'] ?? '';
$today  = date('Y-m-d');

$sql = "SELECT p.id_peneira, p.nome_peneira, p.local, p.data, p.modalidade, p.imagem, p.cidade, u.nome_completo AS criador
        FROM peneiras p
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        WHERE u.email <> ? AND p.data >= ?";
$params = [$email, $today];

if ($cidade !== '') {
    $sql .= ' AND p.cidade LIKE ?';
    $params[] = "%{$cidade}%";
}

$types = str_repeat('s', count($params));
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$peneiras = $stmt->get_result();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petops</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="cssgeral.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100"> 

<header class="d-flex align-items-center justify-content-between px-3 py-2 text-white">
    <div><img src="img/logotest.png" alt="Logo Petops" height="50"></div>
        <form class="d-flex flex-grow-1 mx-md-4" method="GET" style="max-width: 500px;">
            <div class="input-group">
                <input name="cidade" type="text" class="form-control" placeholder="Filtrar por cidade"
                    value="<?= htmlspecialchars($cidade); ?>">
                <button class="btn btn-light" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <div class="d-flex align-items-center gap-2">
            <select class="btn btn-outline-light text-start w-auto" style="background-color: transparent; cursor: pointer;" onchange="if(this.value) window.location.href=this.value;">
                <option value="" disabled selected>Selecione</option>
                <option value="usuario.php">Perfil</option>
                <option value="cadastropeneira.php">Criar Peneira</option>
                <option value="peneirascriadas.php">Peneiras Criadas</option>
                <option value="peneirasescritas.php">Peneiras Inscritas</option>
            </select>
            <button id="themeToggle" class="btn btn-outline-light">
                <i class="bi bi-moon-stars-fill"></i>
            </button>
        </div>
</header>

<main class="container my-5">
    <?php if (!$peneiras->num_rows): ?>
        <div class="alert alert-warning text-center">Nenhuma peneira encontrada.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php while ($p = $peneiras->fetch_assoc()): ?>
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="card shadow h-100">
                        <div class="position-relative">
                            <img src="<?= htmlspecialchars($p['imagem']); ?>" alt="Imagem da peneira" class="w-100 rounded-top" style="height: 200px; object-fit: cover;">
                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                <?= date('d/m/Y', strtotime($p['data'])); ?>
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="fw-bold text-truncate mb-1" title="<?= htmlspecialchars($p['nome_peneira']); ?>">
                                <?= htmlspecialchars($p['nome_peneira']); ?>
                            </h5>
                            <p class="small mb-2"><i class="bi bi-record-fill"></i> <?= htmlspecialchars($p['modalidade']); ?></p>
                            <p class="small mb-2"><i class="bi bi-geo-alt-fill me-1"></i> <?= htmlspecialchars($p['cidade']); ?></p>
                            <p class="small mb-2"><i class="bi bi-person-fill me-1"></i> <?= htmlspecialchars($p['criador']); ?></p>
                            <a href="verpeneira.php?id=<?= $p['id_peneira']; ?>" class="btn btn-primary mt-auto w-100">
                                Ver Detalhes
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>
<br>

<?php include 'rodape.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');

    const setTheme = (theme) => {
        if (theme === 'dark') {
            body.classList.add('bg-dark');
            body.classList.remove('bg-light');
            themeToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
        } else {
            body.classList.add('bg-light');
            body.classList.remove('bg-dark');
            themeToggle.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        }
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
