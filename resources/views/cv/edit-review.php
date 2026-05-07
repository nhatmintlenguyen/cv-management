<?php

use App\Core\View;

$activeJobSeekerTab = 'builder';
$cv = $cv ?? null;
$templates = $templates ?? [];
$selectedTemplate = $selectedTemplate ?? 'modern';
$mockCv = $mockCv ?? [];
?>
<?php require dirname(__DIR__) . '/job-seeker/partials/topbar.php'; ?>

<main class="builder-page builder-page-wide">
    <?php
    $breadcrumbItems = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'CV Builder', 'url' => '/cv/edit/personal-info'],
        ['label' => 'Final Review'],
    ];
    require dirname(__DIR__) . '/partials/breadcrumb.php';
    ?>

    <section class="builder-heading">
        <div class="builder-heading-row">
            <div>
                <h1>Step 4: Final Review</h1>
                <p>Review your completed CV and choose a presentation template before finishing.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Final Review</strong>
            </div>
        </div>
    </section>

    <div class="builder-shell builder-review-shell">
        <?php $activeStep = 'review'; require __DIR__ . '/partials/stepper.php'; ?>

            <section class="builder-review-card">
                <aside class="template-sidebar builder-review-sidebar">
                    <section class="template-panel">
                        <h2>
                            <span class="template-panel-icon">grid_view</span>
                            CV Presentation
                        </h2>

                        <div class="template-choice-list">
                            <?php foreach ($templates as $key => $template): ?>
                                <a
                                    class="template-choice <?= $selectedTemplate === $key ? 'active' : '' ?>"
                                    href="<?= View::url('/cv/edit/review?template=' . $key) ?>"
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

                    <div class="builder-review-actions">
                        <a class="builder-ghost-button" href="<?= View::url('/cv/edit/qualifications') ?>">
                            <span>arrow_back</span>
                            Back to Step 3
                        </a>
                        <form method="post" action="<?= View::url('/cv/finish') ?>">
                            <input type="hidden" name="template" value="<?= View::e($selectedTemplate) ?>">
                            <button class="builder-primary-button" type="submit">
                                Finish CV
                                <span>check</span>
                            </button>
                        </form>
                    </div>
                </aside>

                <section class="template-preview-stage builder-review-preview">
                    <div class="template-preview-frame">
                        <?php require __DIR__ . '/templates-' . $selectedTemplate . '.php'; ?>
                    </div>
                </section>
            </section>
    </div>
</main>
