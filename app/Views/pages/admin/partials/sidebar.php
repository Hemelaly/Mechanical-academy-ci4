<style>
    /* Sidebar fixo à esquerda */
    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 320px;
        /* ajuste como preferir */
        height: 100vh !important;
        /* cor de fundo do sidebar */
        padding: 24px 16px;
        overflow-y: auto;
        /* rolagem se precisar */
    }

    /* Links do menu */
    #sidebar a {
        display: block;
        padding: 12px 20px;
        margin: 6px 8px 6px 0;
        /* pequena “folga” direita para o hover não encostar na borda */
        border-radius: 12px;
        color: #94a3b8;
        text-decoration: none;
        transition: background .2s, color .2s;
    }

    #sidebar a:hover,
    #sidebar a.active {
        background: #1e293b;
        /* fica dentro do sidebar */
        color: #fff;
    }

    /* empurre o conteúdo principal para a direita do sidebar */
    .main-content {
        margin-left: 260px;
        /* mesma largura do sidebar */
    }
</style>

<div class="d-none d-md-block ps-4 pt-4" id="sidebar">
    <div class="image mb-4" style="width: 150px; height: auto;">
        <img class="img-fluid" src="<?= base_url('assets/img/logo_light.png') ?>" alt="">
    </div>

    <?php foreach ($sidebarLinks as $link): ?>
        <?php
        $currentUrl = current_url();
        $isActive = str_ends_with($currentUrl, $link['url']);
        ?>

        <a href="<?= site_url($link['url']) ?>"
            class="my-1 nav-link <?= $isActive ? 'active' : '' ?>">
            <i class="bi <?= $link['icon'] ?> me-2"></i> <?= $link['label'] ?>
        </a>
    <?php endforeach; ?>
    <a href="/logout" id="logoutBtn" class="nav-link text-danger mt-5">
        <i class="bi bi-box-arrow-right me-2" onclick="return confirm('Tem certeza que deseja sair da conta?');"></i> Sair
    </a>
</div>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Tem certeza?',
            text: "Você será desconectado da sessão.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, sair',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redireciona para a rota de logout do CI4
                window.location.href = "<?= site_url('logout') ?>";
            }
        });
    });
</script>