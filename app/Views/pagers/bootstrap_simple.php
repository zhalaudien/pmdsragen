<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(0);
?>

<nav aria-label="Navigasi Halaman Sederhana" class="d-inline-block">
    <ul class="pagination pagination-sm m-0 shadow-xs">
        <li class="page-item <?= $pager->hasPrevious() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasPrevious() ? $pager->getPrevious() : '#' ?>" aria-label="Sebelumnya">
                <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
            </a>
        </li>
        <li class="page-item <?= $pager->hasNext() ? '' : 'disabled' ?>">
            <a class="page-link" href="<?= $pager->hasNext() ? $pager->getNext() : '#' ?>" aria-label="Berikutnya">
                Berikutnya <i class="fas fa-chevron-right ml-1"></i>
            </a>
        </li>
    </ul>
</nav>
