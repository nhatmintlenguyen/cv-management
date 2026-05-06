<?php

use App\Core\View;

$activeSiteTab = 'my-jobs';
$jobs = $jobs ?? [];
$formatDate = static function (?string $value): string {
    if ($value === null || $value === '') {
        return 'Recently updated';
    }

    $timestamp = strtotime($value);

    return $timestamp === false ? $value : date('M d, Y', $timestamp);
};
$companyInitials = static function (string $name): string {
    $words = preg_split('/\s+/', trim($name)) ?: [];
    $letters = array_map(static fn (string $word): string => strtoupper(substr($word, 0, 1)), array_slice(array_filter($words), 0, 2));

    return implode('', $letters) ?: 'OC';
};
$locationLabel = static function (array $job): string {
    $parts = array_values(array_filter([
        $job['district_name'] ?? '',
        $job['city_name'] ?? '',
        $job['country_name'] ?? '',
    ]));

    return $parts === [] ? 'Location not provided' : implode(', ', $parts);
};
?>
<?php require dirname(__DIR__, 2) . '/partials/site-topbar.php'; ?>

<main class="builder-page builder-page-wide">
    <section class="builder-heading">
        <?php
        $breadcrumbItems = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'My Jobs'],
        ];
        require dirname(__DIR__, 2) . '/partials/breadcrumb.php';
        ?>

        <div class="builder-heading-row">
            <div>
                <h1>Job Vacancy Management</h1>
                <p>Manage your job postings, continue drafts, and prepare new vacancy listings.</p>
            </div>

            <a class="builder-primary-button" href="<?= View::url('/employer/jobs/create') ?>">
                Post Job
                <span>add</span>
            </a>
        </div>
    </section>

    <section class="employer-job-board">
        <div class="employer-job-board-header">
            <div class="builder-section-title">
                <span>work</span>
                <h2>Your Job Postings</h2>
            </div>
            <span><?= count($jobs) ?> vacancy<?= count($jobs) === 1 ? '' : 'ies' ?></span>
        </div>

        <?php if ($jobs === []): ?>
            <div class="employer-empty-state">
                <span>work_alert</span>
                <h2>No Job Vacancies Yet</h2>
                <p>Start with the Post Job flow to publish your first opening.</p>
            </div>
        <?php else: ?>
            <div class="employer-job-list">
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $companyName = (string) ($job['company_name'] ?? 'Company');
                    $isActive = ($job['status'] ?? '') === 'active';
                    ?>
                    <a class="employer-job-card" href="<?= View::url('/jobs/show?id=' . (int) $job['id']) ?>">
                        <div class="employer-job-logo">
                            <?php if (! empty($job['company_avatar_url'])): ?>
                                <img src="<?= View::e($job['company_avatar_url']) ?>" alt="<?= View::e($companyName) ?> logo">
                            <?php else: ?>
                                <?= View::e($companyInitials($companyName)) ?>
                            <?php endif; ?>
                        </div>

                        <div class="employer-job-main">
                            <div class="employer-job-title-row">
                                <div>
                                    <h2><?= View::e($job['job_title']) ?></h2>
                                    <p><?= View::e($companyName) ?></p>
                                </div>
                                <span class="employer-job-status <?= $isActive ? 'is-active' : 'is-inactive' ?>">
                                    <?= View::e($isActive ? 'Active' : 'Inactive') ?>
                                </span>
                            </div>

                            <div class="employer-job-meta">
                                <span><i>location_on</i><?= View::e($locationLabel($job)) ?></span>
                                <span><i>business_center</i><?= View::e($job['employment_type'] ?? 'Employment type') ?></span>
                                <span><i>signal_cellular_alt</i><?= View::e($job['job_level'] ?? 'Level') ?></span>
                                <span><i>home_work</i><?= View::e($job['work_arrangement'] ?? 'Arrangement') ?></span>
                            </div>

                            <div class="employer-job-tags">
                                <span><?= View::e($job['job_category'] ?? 'Category') ?></span>
                                <span><?= View::e($job['industry_name'] ?? 'Industry') ?></span>
                                <span><?= (int) ($job['required_skill_count'] ?? 0) ?> required skill<?= (int) ($job['required_skill_count'] ?? 0) === 1 ? '' : 's' ?></span>
                            </div>
                        </div>

                        <aside class="employer-job-side">
                            <strong><?= View::e(($job['salary_range'] ?? 'Salary') . ' / ' . ($job['salary_type'] ?? 'Type')) ?></strong>
                            <span><?= (int) ($job['number_of_openings'] ?? 1) ?> opening<?= (int) ($job['number_of_openings'] ?? 1) === 1 ? '' : 's' ?></span>
                            <small>Updated <?= View::e($formatDate($job['updated_at'] ?? null)) ?></small>
                        </aside>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
