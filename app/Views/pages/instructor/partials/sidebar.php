<style>
    #sidebar a {
        color: #94a3b8;
        /* slate-400 */
        text-decoration: none;
        display: block;
        padding: 12px 20px;
        border-radius: 0.75rem;
        transition: 0.3s;
        font-weight: 500;
    }

    #sidebar a:hover,
    #sidebar a.active {
        background: #1e293b;
        /* slate-800 */
        color: #fff;
    }
</style>

<div class="d-none d-md-block sticky-top pt-4" id="sidebar">
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
    <a href="/logout" class="nav-link mt-5">
        <i class="bi bi-box-arrow-right me-2" onclick="return confirm('Tem certeza que deseja sair da conta?');"></i> Sair
    </a>
</div>