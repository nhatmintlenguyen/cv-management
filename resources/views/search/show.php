<?php

use App\Core\View;

$activeSiteTab = 'find-cvs';
$selectedTemplate = $selectedTemplate ?? 'modern';
$selectedTemplateInfo = $selectedTemplateInfo ?? ['name' => ucfirst($selectedTemplate), 'description' => 'Candidate selected template'];
$mockCv = $mockCv ?? [];
$cv = $cv ?? [];
$backUrl = $backUrl ?? View::url('/find-cvs');
?>
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="employer-cv-page">
    <?php
    $breadcrumbItems = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Find CVs', 'url' => '/find-cvs'],
        ['label' => $mockCv['category'] ?? 'Category', 'url' => ! empty($cv['cv_category_id']) ? '/find-cvs?category_id=' . (int) $cv['cv_category_id'] : '/find-cvs'],
        ['label' => $cv['full_name'] ?? 'Candidate CV'],
    ];
    require dirname(__DIR__) . '/partials/breadcrumb.php';
    ?>

    <header class="employer-cv-header">
        <div>
            <a class="employer-back-link" href="<?= View::e($backUrl) ?>">
                <span>arrow_back</span>
                Back to candidates
            </a>
            <h1><?= View::e($cv['full_name'] ?? 'Candidate CV') ?></h1>
            <p>Read-only CV preview using the presentation template selected by this candidate.</p>
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
                    <span class="template-panel-icon">verified</span>
                    Selected Template
                </h2>
                <div class="employer-selected-template-card">
                    <span><?= $selectedTemplate === 'modern' ? 'architecture' : ($selectedTemplate === 'classic' ? 'menu_book' : 'view_agenda') ?></span>
                    <div>
                        <strong><?= View::e($selectedTemplateInfo['name'] ?? ucfirst($selectedTemplate)) ?></strong>
                        <small><?= View::e($selectedTemplateInfo['description'] ?? '') ?></small>
                    </div>
                    <i>lock</i>
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
