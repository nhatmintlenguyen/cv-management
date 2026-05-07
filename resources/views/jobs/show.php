<?php

use App\Core\View;

$user = $_SESSION['user'] ?? null;
$activeSiteTab = ($user['role'] ?? null) === 'employer' ? 'my-jobs' : 'job-search';
$job = $job ?? [];
$requiredSkills = $requiredSkills ?? [];
$isEmployerOwner = $isEmployerOwner ?? false;
$companyName = (string) ($job['company_name'] ?? 'Company');
$companyInitials = static function (string $name): string {
    $words = preg_split('/\s+/', trim($name)) ?: [];
    $letters = array_map(static fn (string $word): string => strtoupper(substr($word, 0, 1)), array_slice(array_filter($words), 0, 2));

    return implode('', $letters) ?: 'OC';
};
$locationParts = array_values(array_filter([
    $job['district_name'] ?? '',
    $job['city_name'] ?? '',
    $job['country_name'] ?? '',
]));
$locationLabel = $locationParts === [] ? 'Location not provided' : implode(', ', $locationParts);
$statusLabel = ucfirst((string) ($job['status'] ?? 'inactive'));
$statusClass = match ((string) ($job['status'] ?? '')) {
    'active' => 'is-active',
    'suspicious' => 'is-suspicious',
    default => 'is-inactive',
};
$formatDate = static function (?string $value): string {
    if ($value === null || $value === '') {
        return 'Recently updated';
    }

    $timestamp = strtotime($value);

    return $timestamp === false ? $value : date('M d, Y', $timestamp);
};
?>
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="builder-page builder-page-wide">
    <?php
    $breadcrumbItems = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => ($user['role'] ?? null) === 'employer' ? 'My Jobs' : 'Jobs', 'url' => ($user['role'] ?? null) === 'employer' ? '/employer/jobs' : '/jobs'],
        ['label' => $job['job_category'] ?? 'Category', 'url' => ! empty($job['job_category_id']) ? '/jobs?job_category_id=' . (int) $job['job_category_id'] : '/jobs'],
        ['label' => $job['job_title'] ?? 'Job Vacancy'],
    ];
    require dirname(__DIR__) . '/partials/breadcrumb.php';
    ?>

    <section class="job-detail-hero">
        <a class="employer-back-link" href="<?= View::url(($user['role'] ?? null) === 'employer' ? '/employer/jobs' : '/jobs') ?>">
            <span>arrow_back</span>
            Back to jobs
        </a>

        <div class="job-detail-hero-card">
            <div class="employer-job-logo job-detail-logo">
                <?php if (! empty($job['company_avatar_url'])): ?>
                    <img src="<?= View::e($job['company_avatar_url']) ?>" alt="<?= View::e($companyName) ?> logo">
                <?php else: ?>
                    <?= View::e($companyInitials($companyName)) ?>
                <?php endif; ?>
            </div>

            <div>
                <div class="job-detail-title-row">
                    <div>
                        <h1><?= View::e($job['job_title'] ?? 'Job Vacancy') ?></h1>
                        <p><?= View::e($companyName) ?></p>
                    </div>
                    <span class="employer-job-status <?= $statusClass ?>">
                        <?= View::e($statusLabel) ?>
                    </span>
                </div>

                <div class="employer-job-meta">
                    <span><i>location_on</i><?= View::e($locationLabel) ?></span>
                    <span><i>payments</i><?= View::e(($job['salary_range'] ?? 'Salary') . ' / ' . ($job['salary_type'] ?? 'Type')) ?></span>
                    <span><i>business_center</i><?= View::e($job['employment_type'] ?? 'Employment type') ?></span>
                    <span><i>home_work</i><?= View::e($job['work_arrangement'] ?? 'Arrangement') ?></span>
                </div>
            </div>
        </div>
    </section>

    <div class="job-detail-layout">
        <section class="job-detail-main">
            <article class="job-detail-section">
                <h2>Company</h2>
                <p><?= nl2br(View::e($job['company_description'] ?? 'No company description provided.')) ?></p>
            </article>

            <article class="job-detail-section">
                <h2>Responsibilities</h2>
                <p><?= nl2br(View::e($job['responsibilities'] ?? 'Not provided.')) ?></p>
            </article>

            <article class="job-detail-section">
                <h2>Required Qualifications</h2>
                <p><?= nl2br(View::e($job['required_qualifications'] ?? 'Not provided.')) ?></p>
            </article>

            <article class="job-detail-section">
                <h2>Preferred Skills</h2>
                <p><?= nl2br(View::e($job['preferred_skills'] ?? 'Not provided.')) ?></p>
            </article>

            <article class="job-detail-section">
                <h2>Benefits</h2>
                <p><?= nl2br(View::e($job['benefits'] ?? 'Not provided.')) ?></p>
            </article>

            <article class="job-detail-section">
                <h2>Additional Notes</h2>
                <p><?= nl2br(View::e($job['additional_notes'] ?? 'Not provided.')) ?></p>
            </article>
        </section>

        <aside class="job-detail-side">
            <?php if ($isEmployerOwner): ?>
                <section class="job-detail-actions">
                    <h2>Employer Actions</h2>
                    <a class="builder-primary-button" href="<?= View::url('/employer/jobs/edit?id=' . (int) $job['id']) ?>">
                        Edit Job
                        <span>edit</span>
                    </a>
                    <form method="post" action="<?= View::url('/employer/jobs/toggle-status') ?>">
                        <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
                        <button class="builder-secondary-button" type="submit">
                            <?= ($job['status'] ?? '') === 'active' ? 'Deactivate' : 'Activate' ?>
                            <span><?= ($job['status'] ?? '') === 'active' ? 'visibility_off' : 'visibility' ?></span>
                        </button>
                    </form>
                    <form method="post" action="<?= View::url('/employer/jobs/delete') ?>" onsubmit="return confirm('Delete this job vacancy? This action cannot be undone.');">
                        <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
                        <button class="job-danger-button" type="submit">
                            Delete Job
                            <span>delete</span>
                        </button>
                    </form>
                </section>
            <?php endif; ?>

            <section class="job-detail-facts">
                <h2>Job Snapshot</h2>
                <dl>
                    <div><dt>Category</dt><dd><?= View::e($job['job_category'] ?? 'Category') ?></dd></div>
                    <div><dt>Industry</dt><dd><?= View::e($job['industry_name'] ?? 'Industry') ?></dd></div>
                    <div><dt>Level</dt><dd><?= View::e($job['job_level'] ?? 'Level') ?></dd></div>
                    <div><dt>Openings</dt><dd><?= (int) ($job['number_of_openings'] ?? 1) ?></dd></div>
                    <div><dt>Minimum Degree</dt><dd><?= View::e($job['minimum_degree_level'] ?? 'Degree') ?></dd></div>
                    <div><dt>Experience</dt><dd><?= (int) ($job['minimum_years_experience'] ?? 0) ?> years</dd></div>
                    <div><dt>Posted</dt><dd><?= View::e($formatDate($job['created_at'] ?? null)) ?></dd></div>
                </dl>
            </section>

            <section class="job-detail-facts">
                <h2>Required Skills</h2>
                <div class="job-review-skill-tags">
                    <?php foreach ($requiredSkills as $skill): ?>
                        <span><?= View::e($skill['skill_name']) ?> - Level <?= (int) $skill['level_value'] ?></span>
                    <?php endforeach; ?>
                    <?php if ($requiredSkills === []): ?>
                        <span>No skills listed</span>
                    <?php endif; ?>
                </div>
            </section>
        </aside>
    </div>
</main>
