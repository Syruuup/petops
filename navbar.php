<header class="d-flex justify-content-between align-items-center px-3 py-2 text-white">
    <div class="d-flex align-items-center gap-2">
        <a href="home.php">
            <div><img src="img/logotest.png" alt="Logo Petops" height="50"></div>
        </a>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
                <select class="btn btn-outline-light text-start w-auto" style="background-color: transparent; cursor: pointer;" onchange="if(this.value) window.location.href=this.value;">
                    <option value="" disabled selected>Selecione</option>
                    <option value="usuario.php">Perfil</option>
                    <option value="cadastropeneira.php">Criar Peneiras</option>
                    <option value="peneirascriadas.php">Peneiras Criadas</option>
                    <option value="peneirasescritas.php">Peneiras Inscritas</option>
                </select>
            <button id="themeToggle" class="btn btn-outline-light btn-sm">
                <i class="moon-stars"></i>
            </button>
        </div>
</header>