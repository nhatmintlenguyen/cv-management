<?php

use App\Core\View;

$activeJobSeekerTab = 'templates';
$templates = $templates ?? [];
$selectedTemplate = $selectedTemplate ?? array_key_first($templates);
$selected = $templates[$selectedTemplate] ?? [];
$mockCv = $mockCv ?? [];
?>
<?php require dirname(__DIR__) . '/job-seeker/partials/topbar.php'; ?>

<main class="job-page template-page">
    <section class="template-hero">
        <div>
            <h1>Curate Your Identity</h1>
            <p>Select a layout that mirrors your professional narrative. Each template is structured for clean presentation and consistent CV data.</p>
        </div>

    </section>

    <section class="template-workspace">
        <aside class="template-sidebar">
            <section class="template-panel">
                <h2>
                    <span class="template-panel-icon">grid_view</span>
                    Layout Engine
                </h2>

                <div class="template-choice-list">
                    <?php foreach ($templates as $key => $template): ?>
                        <a
                            class="template-choice <?= $selectedTemplate === $key ? 'active' : '' ?>"
                            href="<?= View::url('/cv/templates?template=' . $key) ?>"
                        >
                            <span class="template-choice-icon"><?= $key === 'modern' ? 'architecture' : ($key === 'classic' ? 'menu_book' : 'view_quilt') ?></span>
                            <span>
                                <strong><?= View::e($template['name']) ?></strong>
                                <small><?= View::e($template['description']) ?></small>
                            </span>
                            <?php if ($selectedTemplate === $key): ?>
                                <span class="template-check">check_circle</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </aside>

        <section class="template-preview-stage">
            <div class="template-preview-frame">
                <?php require __DIR__ . '/templates-' . $selectedTemplate . '.php'; ?>
            </div>
        </section>
    </section>
</main>
