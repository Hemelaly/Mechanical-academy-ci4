<?php
/** @var \CodeIgniter\Pager\PagerRenderer $pager */
$pager->setSurroundCount(1);

$prev = $pager->getPrevious();
$next = $pager->getNext();
?>

<nav class="flex items-center flex-column flex-wrap md:flex-row justify-between p-4" aria-label="Table navigation">
  <ul class="flex -space-x-px text-sm">
    <li>
      <?php if (!empty($prev)): ?>
        <a href="<?= $prev ?>"
           class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-s-base text-sm px-3 h-9 focus:outline-none">
          Previous
        </a>
      <?php else: ?>
        <span
          class="flex items-center justify-center opacity-50 cursor-not-allowed text-body bg-neutral-secondary-medium box-border border border-default-medium font-medium rounded-s-base text-sm px-3 h-9">
          Previous
        </span>
      <?php endif; ?>
    </li>

    <?php foreach ($pager->links() as $link): ?>
      <li>
        <?php if ($link['active']): ?>
          <span aria-current="page"
                class="flex items-center justify-center text-fg-brand bg-brand-softer box-border border border-default-medium hover:bg-brand-soft hover:text-fg-brand font-medium text-sm w-9 h-9 focus:outline-none">
            <?= esc($link['title']) ?>
          </span>
        <?php else: ?>
          <a href="<?= $link['uri'] ?>"
             class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium text-sm w-9 h-9 focus:outline-none">
            <?= esc($link['title']) ?>
          </a>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>

    <li>
      <?php if (!empty($next)): ?>
        <a href="<?= $next ?>"
           class="flex items-center justify-center text-body bg-neutral-secondary-medium box-border border border-default-medium hover:bg-neutral-tertiary-medium hover:text-heading font-medium rounded-e-base text-sm px-3 h-9 focus:outline-none">
          Next
        </a>
      <?php else: ?>
        <span
          class="flex items-center justify-center opacity-50 cursor-not-allowed text-body bg-neutral-secondary-medium box-border border border-default-medium font-medium rounded-e-base text-sm px-3 h-9">
          Next
        </span>
      <?php endif; ?>
    </li>
  </ul>
</nav>
