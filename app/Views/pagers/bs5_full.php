<?php $pager->setSurroundCount(2); ?>
<nav aria-label="Paginação">
  <ul class="pagination mb-0">
    <?php if ($pager->hasPrevious()) : ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="Primeira">&laquo;</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Anterior">&lsaquo;</a>
      </li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
      <li class="page-item disabled"><span class="page-link">&lsaquo;</span></li>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
      <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
        <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
      </li>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()) : ?>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Próxima">&rsaquo;</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Última">&raquo;</a>
      </li>
    <?php else: ?>
      <li class="page-item disabled"><span class="page-link">&rsaquo;</span></li>
      <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
    <?php endif; ?>
  </ul>
</nav>
