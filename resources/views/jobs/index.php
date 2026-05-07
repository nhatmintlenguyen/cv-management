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
    <?php
    $breadcrumbItems = [
        ['label' => 'Home', 'url' => '/'],
        ['label' => 'Jobs'],
    ];
    require dirname(__DIR__) . '/partials/breadcrumb.php';
    ?>

    <form class="employer-search-shell" method="get" action="<?= View::url('/jobs') ?>" data-ajax-search-form>
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
                <a class="employer-clear-button" href="<?= View::url('/jobs') ?>" data-ajax-search-clear>Clear Criteria</a>
            </div>
        </aside>

        <section class="employer-results-panel" data-ajax-search-results>
            <?php require __DIR__ . '/partials/results.php'; ?>
        </section>
    </form>
</main>
