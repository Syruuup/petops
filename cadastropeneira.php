<?php
include 'conexao.php';

$email_logado = $_SESSION['email'] ?? '';
$stmt = $conn->prepare("SELECT id_usuario, nome_completo FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email_logado);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$id_usuario = $user['id_usuario'] ?? null;
$stmt->close();

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && $id_usuario) {
    $nome_peneira = $_POST["nome_peneira"];
    $descricao = $_POST["descricao"];
    $data = $_POST["data"];
    $local = $_POST["local"];
    $modalidade = $_POST["modalidade"];
    $cidade = $_POST["cidade"];

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] === 0) {
        $extensao = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . "." . $extensao;
        $pasta_destino = __DIR__ . "/uploads/";
        $caminho_completo = $pasta_destino . $nome_arquivo;

        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho_completo)) {
            $imagem_url = "uploads/" . $nome_arquivo;

            $stmt = $conn->prepare("INSERT INTO peneiras (id_usuario, nome_peneira, descricao, data, local, modalidade, imagem, cidade) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $id_usuario, $nome_peneira, $descricao, $data, $local, $modalidade, $imagem_url, $cidade);

            if ($stmt->execute()) {
                $mensagem = "Peneira cadastrada com sucesso!";
                $tipo_mensagem = "sucesso";
            } else {
                $mensagem = "Erro ao cadastrar: " . $stmt->error;
                $tipo_mensagem = "erro";
            }

            $stmt->close();
        } else {
            $mensagem = "Erro ao mover a imagem.";
            $tipo_mensagem = "erro";
        }
    } else {
        $mensagem = "Erro ao carregar imagem: " . $_FILES["imagem"]["error"];
        $tipo_mensagem = "erro";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Peneira</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="cssgeral.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100"> 
<?php include 'navbar.php'; ?>

<main class="container my-5">
    <div class="login-container">
        <h2 class="text-center mb-4">Cadastrar Nova Peneira</h2>

        <?php if ($mensagem): ?>
            <div class="<?= $tipo_mensagem === 'sucesso' ? 'sucesso' : 'erro'; ?>">
                <?= htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nome da Peneira:</label>
            <input type="text" class="form-control" name="nome_peneira" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descrição:</label>
            <textarea class="form-control" name="descricao" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Data:</label>
            <input type="date" class="form-control" name="data" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Local:</label>
            <input type="text" class="form-control" name="local" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cidade:</label>
            <input type="text" class="form-control" name="cidade" required>
        </div>

        <div class="mb-3">
            <label class="form-label">modalidade:</label>
            <select name="modalidade" class="form-control" id="modalidade">
            <option value="Basquete">Basquete</option>
            <option value="futebol">Futebol</option>
            <option value="volei">Volei</option>
            <option value="outros">Outros</option>
            </select>
        </div>

        <div class="mb-4">
            <label>Imagem:</label>
            <input type="file" name="imagem" accept="image/*" required>
        </div>

            <button class="btn btn-primary" type="submit">Cadastrar Peneira</button>
        </form>
    </div>
</main>

<?php include 'rodape.php'; ?>

<!-- Scripts -->
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
