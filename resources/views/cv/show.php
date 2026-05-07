<?php

use App\Core\View;

$activeJobSeekerTab = 'builder';
$cv = $cv ?? null;
$templates = $templates ?? [];
$selectedTemplate = $selectedTemplate ?? 'modern';
$mockCv = $mockCv ?? [];
$isFinished = $isFinished ?? false;
?>
<?php require dirname(__DIR__) . '/job-seeker/partials/topbar.php'; ?>

<main class="builder-page builder-page-wide">
    <?php
    $breadcrumbItems = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'CV Builder', 'url' => '/cv/edit/personal-info'],
        ['label' => 'Completed CV'],
    ];
    require dirname(__DIR__) . '/partials/breadcrumb.php';
    ?>

    <section class="builder-heading">
        <div class="builder-heading-row">
            <div>
                <h1>Your Completed CV</h1>
                <p><?= $isFinished ? 'Your CV is ready with the selected presentation template.' : 'Preview your current CV draft.' ?></p>
            </div>

            <div class="builder-progress-card">
                <span>Selected Template</span>
                <strong><?= View::e($templates[$selectedTemplate]['name'] ?? ucfirst($selectedTemplate)) ?></strong>
            </div>
        </div>
    </section>

    <?php if ($cv === null): ?>
        <section class="builder-form-card">
            <div class="builder-section-title">
                <span>info</span>
                <h2>No CV Found</h2>
            </div>
            <p class="builder-helper-text">Please create your CV before opening the final preview.</p>
            <div class="builder-form-actions">
                <a class="builder-primary-button" href="<?= View::url('/cv/edit/personal-info') ?>">
                    Start CV Builder
                    <span>arrow_forward</span>
                </a>
            </div>
        </section>
    <?php else: ?>
        <section class="completed-cv-layout">
            <aside class="template-sidebar builder-review-sidebar">
                <section class="template-panel">
                    <h2>
                        <span class="template-panel-icon">check_circle</span>
                        Finished
                    </h2>
                    <p class="builder-helper-text">Template selection is stored in your current session.</p>
                </section>

                <div class="builder-review-actions">
                    <a class="builder-primary-button" href="<?= View::url('/cv/edit/personal-info') ?>">
                        Edit CV
                        <span>edit</span>
                    </a>
                    <a class="builder-ghost-button" href="<?= View::url('/cv/edit/review?template=' . $selectedTemplate) ?>">
                        <span>visibility</span>
                        Review Template
                    </a>
                </div>
            </aside>

            <section class="template-preview-stage builder-review-preview">
                <div class="template-preview-frame">
                    <?php require __DIR__ . '/templates-' . $selectedTemplate . '.php'; ?>
                </div>
            </section>
        </section>
    <?php endif; ?>
</main>
