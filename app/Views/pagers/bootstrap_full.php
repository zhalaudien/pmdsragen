<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="Navigasi Halaman" class="d-inline-block">
    <ul class="pagination pagination-sm m-0 shadow-xs">
        <?php if ($pager->hasPreviousPage()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="Halaman Pertama" title="Halaman Pertama">
                    <i class="fas fa-angle-double-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Halaman Sebelumnya" title="Halaman Sebelumnya">
                    <i class="fas fa-angle-left"></i>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true" title="Halaman Pertama"><i class="fas fa-angle-double-left"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true" title="Halaman Sebelumnya"><i class="fas fa-angle-left"></i></span>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>" <?= $link['active'] ? 'aria-current="page"' : '' ?>>
                <?php if ($link['active']) : ?>
                    <span class="page-link font-weight-bold">
                        <?= $link['title'] ?>
                    </span>
                <?php else : ?>
                    <a class="page-link" href="<?= $link['uri'] ?>">
                        <?= $link['title'] ?>
                    </a>
                <?php endif ?>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNextPage()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Halaman Berikutnya" title="Halaman Berikutnya">
                    <i class="fas fa-angle-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="Halaman Terakhir" title="Halaman Terakhir">
                    <i class="fas fa-angle-double-right"></i>
                </a>
            </li>
        <?php else : ?>
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true" title="Halaman Berikutnya"><i class="fas fa-angle-right"></i></span>
            </li>
            <li class="page-item disabled">
                <span class="page-link" aria-hidden="true" title="Halaman Terakhir"><i class="fas fa-angle-double-right"></i></span>
            </li>
        <?php endif ?>
    </ul>
</nav>
