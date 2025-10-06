<?php
include 'conexao.php';

$email_logado = $_SESSION['email'];
$result = $conn->query("SELECT id_usuario FROM usuarios WHERE email='$email_logado'");
$user = $result->fetch_assoc();
$id_usuario = $user['id_usuario'];

if (isset($_GET['id_peneira'])) {
    $id_peneira = $_GET['id_peneira'];

    $sql = "SELECT * FROM peneiras WHERE id_peneira = $id_peneira AND id_usuario = $id_usuario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $peneira = $result->fetch_assoc();
    } else {
        echo "Peneira não encontrada.";
        exit();
    }
} else {
    echo "ID de Peneira não informado.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_peneira = $_POST["nome_peneira"];
    $descricao = $_POST["descricao"];
    $data = $_POST["data"];
    $local = $_POST["local"];
    $cidade = $_POST["cidade"];
    $modalidade = $_POST["modalidade"];
    $imagem_url = $peneira['imagem'];

    if (isset($_FILES["imagem"]) && $_FILES["imagem"]["error"] == 0) {
        $extensao = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . "." . $extensao;
        $pasta_destino = __DIR__ . "/uploads/";
        $caminho_completo = $pasta_destino . $nome_arquivo;

        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminho_completo)) {
            $imagem_url = "uploads/" . $nome_arquivo;
        } else {
            echo "Erro ao mover a imagem para o diretório.";
        }
    }

    $stmt = $conn->prepare("UPDATE peneiras SET nome_peneira = ?, descricao = ?, data = ?, local = ?, cidade = ?, modalidade= ?, imagem = ? WHERE id_peneira = ?");
    $stmt->bind_param("sssssssi", $nome_peneira, $descricao, $data, $local, $cidade, $modalidade, $imagem_url, $id_peneira);

    if ($stmt->execute()) {
        echo "Peneira atualizada com sucesso!";
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
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
        <h2 class="text-center mb-4">Atualizar peneiras</h2><br>

        <form method="post" enctype="multipart/form-data">
            <label>Nome da Peneira:</label>
            <input type="text" name="nome_peneira" value="<?php echo htmlspecialchars($peneira['nome_peneira']); ?>" required>

            <label>Descrição:</label>
            <textarea name="descricao" required><?php echo htmlspecialchars($peneira['descricao']); ?></textarea>

            <label>Data:</label>
            <input type="date" name="data" value="<?php echo htmlspecialchars($peneira['data']); ?>" required>

            <label>Local:</label>
            <input type="text" name="local" value="<?php echo htmlspecialchars($peneira['local']); ?>" required>

            <label>Cidade:</label>
            <input type="text" name="cidade" value="<?php echo htmlspecialchars($peneira['cidade']); ?>" required>

            <label>modalidade:</label>
            <select name="modalidade" id="modalidade">
            <option value="Basquete">Basquete</option>
            <option value="futebol">Futebol</option>
            <option value="volei">Volei</option>
            <option value="outros">Outros</option>
            </select>

            <label>Imagem:</label>
            <input type="file" name="imagem" accept="image/*">

            <button class="btn btn-primary" type="submit">Alterar Peneira</button>
            <a href="peneirascriadas.php"><button class="btn btn-primary">Voltar</button></a>
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
