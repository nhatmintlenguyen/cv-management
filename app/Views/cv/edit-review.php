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
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>CV Builder</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Final Review</strong>
        </div>

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
        <aside class="builder-stepper" aria-label="CV builder progress">
            <div class="builder-step completed">
                <span>1</span>
                <div>
                    <strong>Personal Info</strong>
                    <small>Saved</small>
                </div>
            </div>
            <div class="builder-step completed">
                <span>2</span>
                <div>
                    <strong>Education &amp; Experience</strong>
                    <small>Saved</small>
                </div>
            </div>
            <div class="builder-step completed">
                <span>3</span>
                <div>
                    <strong>Qualifications &amp; Skills</strong>
                    <small>Saved</small>
                </div>
            </div>
            <div class="builder-step active">
                <span>4</span>
                <div>
                    <strong>Review</strong>
                    <small>In Progress</small>
                </div>
            </div>
        </aside>

        <?php if ($cv === null): ?>
            <section class="builder-form-card">
                <div class="builder-section-title">
                    <span>info</span>
                    <h2>Personal Information Required</h2>
                </div>
                <p class="builder-helper-text">Please save Step 1 before reviewing your CV.</p>
                <div class="builder-form-actions">
                    <a class="builder-primary-button" href="<?= View::url('/cv/edit/personal-info') ?>">
                        Back to Step 1
                        <span>arrow_forward</span>
                    </a>
                </div>
            </section>
        <?php else: ?>
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
                        <button class="builder-primary-button" type="button">
                            Finish CV
                            <span>check</span>
                        </button>
                    </div>
                </aside>

                <section class="template-preview-stage builder-review-preview">
                    <div class="template-preview-frame">
                        <?php require __DIR__ . '/templates-' . $selectedTemplate . '.php'; ?>
                    </div>
                </section>
            </section>
        <?php endif; ?>
    </div>
</main>
