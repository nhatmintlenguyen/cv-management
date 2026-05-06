<?php

use App\Core\View;

$filters = $filters ?? [];
$jobs = $jobs ?? [];
$page = $page ?? 1;
$perPage = $perPage ?? 10;
$total = $total ?? count($jobs);
$shown = min($total, $page * $perPage);
$queryString = $queryString ?? '';
$queryPrefix = $queryString === '' ? '?' : '?' . $queryString . '&';

$formatDate = static function (?string $value): string {
    if ($value === null || $value === '') {
        return 'Recently posted';
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
$excerpt = static function (string $value, int $length = 170): string {
    $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

    if (strlen($value) <= $length) {
        return $value;
    }

    return rtrim(substr($value, 0, $length - 3)) . '...';
};
?>
<header class="employer-results-header">
    <div>
        <h1>Job Discovery</h1>
        <p>Showing <?= (int) $shown ?> of <?= (int) $total ?> active vacancies matching your criteria</p>
    </div>
    <div class="employer-view-toggle" aria-hidden="true">
        <span class="active">view_list</span>
        <span>work</span>
    </div>
</header>

<?php if ($jobs === []): ?>
    <section class="employer-empty-state">
        <span>work_alert</span>
        <h2>No matching jobs found</h2>
        <p>Try a broader keyword, remove one skill, or clear a location filter.</p>
    </section>
<?php else: ?>
    <section class="employer-job-list job-search-results">
        <?php foreach ($jobs as $job): ?>
            <?php $companyName = (string) ($job['company_name'] ?? 'Company'); ?>
            <a class="employer-job-card job-search-card" href="<?= View::url('/jobs/show?id=' . (int) $job['id']) ?>">
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
                        <span class="job-search-posted">Posted <?= View::e($formatDate($job['created_at'] ?? null)) ?></span>
                    </div>

                    <div class="employer-job-meta">
                        <span><i>location_on</i><?= View::e($locationLabel($job)) ?></span>
                        <span><i>payments</i><?= View::e(($job['salary_range'] ?? 'Salary') . ' / ' . ($job['salary_type'] ?? 'Type')) ?></span>
                        <span><i>business_center</i><?= View::e($job['employment_type'] ?? 'Employment type') ?></span>
                        <span><i>home_work</i><?= View::e($job['work_arrangement'] ?? 'Arrangement') ?></span>
                    </div>

                    <p class="job-search-description">
                        <?= View::e($excerpt((string) ($job['responsibilities'] ?? ''))) ?>
                    </p>

                    <div class="employer-job-tags">
                        <span><?= View::e($job['job_category'] ?? 'Category') ?></span>
                        <span><?= View::e($job['job_level'] ?? 'Level') ?></span>
                        <span><?= (int) ($job['required_skill_count'] ?? 0) ?> required skill<?= (int) ($job['required_skill_count'] ?? 0) === 1 ? '' : 's' ?></span>
                    </div>
                </div>

                <aside class="employer-job-side">
                    <strong><?= View::e($job['salary_range'] ?? 'Salary') ?></strong>
                    <span><?= View::e($job['work_arrangement'] ?? 'Arrangement') ?></span>
                    <small>View details</small>
                </aside>
            </a>
        <?php endforeach; ?>
    </section>

    <?php if ($shown < $total): ?>
        <div class="employer-load-more">
            <a data-ajax-search-page href="<?= View::url('/jobs' . $queryPrefix . 'page=' . ((int) $page + 1)) ?>">
                Load More Jobs
                <span>expand_more</span>
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>
