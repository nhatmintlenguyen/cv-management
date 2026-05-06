<?php

use App\Core\View;

$breadcrumbItems = $breadcrumbItems ?? [];
?>
<?php if ($breadcrumbItems !== []): ?>
    <nav class="site-breadcrumb" aria-label="Breadcrumb">
        <?php foreach ($breadcrumbItems as $index => $item): ?>
            <?php
            $isLast = $index === count($breadcrumbItems) - 1;
            $label = (string) ($item['label'] ?? '');
            $url = $item['url'] ?? null;
            ?>
            <?php if (! $isLast && is_string($url) && $url !== ''): ?>
                <a href="<?= View::url($url) ?>"><?= View::e($label) ?></a>
            <?php else: ?>
                <span aria-current="<?= $isLast ? 'page' : 'false' ?>"><?= View::e($label) ?></span>
            <?php endif; ?>

            <?php if (! $isLast): ?>
                <i aria-hidden="true">chevron_right</i>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
