<?php

use App\Core\View;

$activeSiteTab = 'find-cvs';
$templates = $templates ?? [];
$selectedTemplate = $selectedTemplate ?? 'modern';
$mockCv = $mockCv ?? [];
$cv = $cv ?? [];
$backUrl = $backUrl ?? View::url('/find-cvs');
?>
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="employer-cv-page">
    <header class="employer-cv-header">
        <div>
            <a class="employer-back-link" href="<?= View::e($backUrl) ?>">
                <span>arrow_back</span>
                Back to candidates
            </a>
            <h1><?= View::e($cv['full_name'] ?? 'Candidate CV') ?></h1>
            <p>Read-only CV preview for employer review. Template switching does not modify candidate data.</p>
        </div>
        <div class="employer-readonly-pill">
            <span>lock</span>
            Read-only access
        </div>
    </header>

    <section class="employer-cv-layout">
        <aside class="template-sidebar employer-template-sidebar">
            <section class="template-panel">
                <h2>
                    <span class="template-panel-icon">dashboard_customize</span>
                    CV Templates
                </h2>

                <div class="template-choice-list">
                    <?php foreach ($templates as $key => $template): ?>
                        <a
                            class="template-choice <?= $selectedTemplate === $key ? 'active' : '' ?>"
                            href="<?= View::url('/find-cvs/show?id=' . (int) ($cv['id'] ?? 0) . '&template=' . $key) ?>"
                        >
                            <span class="template-choice-icon"><?= $key === 'modern' ? 'architecture' : ($key === 'classic' ? 'menu_book' : 'view_agenda') ?></span>
                            <span>
                                <strong><?= View::e($template['name']) ?></strong>
                                <small><?= View::e($template['description']) ?></small>
                            </span>
                            <span class="template-check"><?= $selectedTemplate === $key ? 'check_circle' : 'radio_button_unchecked' ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="employer-cv-facts">
                <h2>Candidate Snapshot</h2>
                <p><strong>Category</strong><span><?= View::e($mockCv['category'] ?? '') ?></span></p>
                <p><strong>Location</strong><span><?= View::e(trim(($mockCv['city'] ?? '') . ', ' . ($mockCv['country'] ?? ''), ', ')) ?></span></p>
                <p><strong>Skills</strong><span><?= count($mockCv['skills'] ?? []) ?> listed</span></p>
                <p><strong>Experience</strong><span><?= count($mockCv['work_histories'] ?? []) ?> roles</span></p>
            </section>
        </aside>

        <section class="template-preview-stage employer-cv-preview-stage">
            <div class="template-preview-frame employer-cv-preview-frame">
                <?php require dirname(__DIR__) . '/cv/templates-' . $selectedTemplate . '.php'; ?>
            </div>
        </section>
    </section>
</main>
