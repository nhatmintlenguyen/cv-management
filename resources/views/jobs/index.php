<?php

use App\Core\View;

$activeSiteTab = 'job-search';
$filters = $filters ?? [];
$jobs = $jobs ?? [];
$jobCategories = $jobCategories ?? [];
$countries = $countries ?? [];
$cities = $cities ?? [];
$skills = $skills ?? [];
$employmentTypes = $employmentTypes ?? [];
$jobLevels = $jobLevels ?? [];
$salaryRanges = $salaryRanges ?? [];
$workArrangements = $workArrangements ?? [];
$selectedSkills = array_map('strval', $filters['skill_ids'] ?? []);
$page = $page ?? 1;
$perPage = $perPage ?? 10;
$total = $total ?? count($jobs);
$shown = min($total, $page * $perPage);
$queryPrefix = $queryString === '' ? '?' : '?' . $queryString . '&';

$selected = static fn (string $key, mixed $value): string => (string) ($filters[$key] ?? '') === (string) $value ? 'selected' : '';
$checkedSkill = static fn (mixed $value): string => in_array((string) $value, $selectedSkills, true) ? 'checked' : '';
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
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="employer-search-page job-search-page">
    <form class="employer-search-shell" method="get" action="<?= View::url('/jobs') ?>">
        <aside class="employer-filter-panel">
            <section class="employer-filter-section">
                <h2>Quick Search</h2>
                <label class="employer-search-input">
                    <span>search</span>
                    <input type="search" name="keyword" value="<?= View::e($filters['keyword'] ?? '') ?>" placeholder="Job title, description...">
                </label>
            </section>

            <section class="employer-filter-section">
                <h2>Core Filters</h2>

                <label class="employer-filter-field">
                    <span>Job Category</span>
                    <select name="job_category_id">
                        <option value="">Any category</option>
                        <?php foreach ($jobCategories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= $selected('job_category_id', $category['id']) ?>><?= View::e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="employer-filter-field">
                    <span>Country</span>
                    <select name="country_id" data-employer-country>
                        <option value="">Any country</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= (int) $country['id'] ?>" <?= $selected('country_id', $country['id']) ?>><?= View::e($country['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="employer-filter-field">
                    <span>City</span>
                    <select name="city_id" data-employer-city>
                        <option value="">Any city</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?= (int) $city['id'] ?>" data-country-id="<?= (int) $city['country_id'] ?>" <?= $selected('city_id', $city['id']) ?>><?= View::e($city['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="employer-filter-field">
                    <span>Employment Type</span>
                    <select name="employment_type_id">
                        <option value="">Any type</option>
                        <?php foreach ($employmentTypes as $employmentType): ?>
                            <option value="<?= (int) $employmentType['id'] ?>" <?= $selected('employment_type_id', $employmentType['id']) ?>><?= View::e($employmentType['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="employer-filter-field">
                    <span>Job Level</span>
                    <select name="job_level_id">
                        <option value="">Any level</option>
                        <?php foreach ($jobLevels as $jobLevel): ?>
                            <option value="<?= (int) $jobLevel['id'] ?>" <?= $selected('job_level_id', $jobLevel['id']) ?>><?= View::e($jobLevel['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="employer-filter-field">
                    <span>Salary Range</span>
                    <select name="salary_range_id">
                        <option value="">Any salary</option>
                        <?php foreach ($salaryRanges as $salaryRange): ?>
                            <option value="<?= (int) $salaryRange['id'] ?>" <?= $selected('salary_range_id', $salaryRange['id']) ?>><?= View::e($salaryRange['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="employer-filter-field">
                    <span>Work Arrangement</span>
                    <select name="work_arrangement_id">
                        <option value="">Any arrangement</option>
                        <?php foreach ($workArrangements as $workArrangement): ?>
                            <option value="<?= (int) $workArrangement['id'] ?>" <?= $selected('work_arrangement_id', $workArrangement['id']) ?>><?= View::e($workArrangement['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </section>

            <section class="employer-filter-section">
                <div class="employer-filter-heading-row">
                    <h2>Required Skills</h2>
                    <a href="<?= View::url('/jobs') ?>">Clear</a>
                </div>

                <div class="employer-skill-picker" aria-label="Required skill filters">
                    <?php foreach ($skills as $skill): ?>
                        <label>
                            <input type="checkbox" name="skill_ids[]" value="<?= (int) $skill['id'] ?>" <?= $checkedSkill($skill['id']) ?>>
                            <span><?= View::e($skill['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="employer-filter-section">
                <h2>Sorting</h2>
                <label class="employer-filter-field">
                    <span>Sort By</span>
                    <select name="sort">
                        <option value="recent" <?= $selected('sort', 'recent') ?>>Most recently posted</option>
                        <option value="salary_asc" <?= $selected('sort', 'salary_asc') ?>>Salary ascending</option>
                        <option value="salary_desc" <?= $selected('sort', 'salary_desc') ?>>Salary descending</option>
                        <option value="title" <?= $selected('sort', 'title') ?>>Job title alphabetical</option>
                    </select>
                </label>
            </section>

            <div class="employer-filter-actions">
                <button type="submit">Search Jobs</button>
            </div>
        </aside>

        <section class="employer-results-panel">
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
                        <a href="<?= View::url('/jobs' . $queryPrefix . 'page=' . ((int) $page + 1)) ?>">
                            Load More Jobs
                            <span>expand_more</span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </form>
</main>
