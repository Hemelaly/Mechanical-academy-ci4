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
    <h4 class="text-white mb-4">⚙️ Mechanical</h4>

    <?php foreach ($sidebarLinks as $link): ?>
        <a href="<?= $link['url'] ?>" class="my-1 nav-link <?= $currentUrl == base_url($link['url']) ? 'active' : '' ?>">
            <i class="bi <?= $link['icon'] ?> me-2"></i> <?= $link['label'] ?>
        </a>
    <?php endforeach; ?>
</div>