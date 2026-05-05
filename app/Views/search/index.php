<?php

use App\Core\View;

$activeSiteTab = 'find-cvs';
$filters = $filters ?? [];
$candidates = $candidates ?? [];
$categories = $categories ?? [];
$countries = $countries ?? [];
$cities = $cities ?? [];
$skills = $skills ?? [];
$proficiencyLevels = $proficiencyLevels ?? [];
$degreeLevels = $degreeLevels ?? [];
$selectedSkills = array_map('strval', $filters['skill_ids'] ?? []);
$page = $page ?? 1;
$perPage = $perPage ?? 6;
$total = $total ?? 0;
$shown = min($total, $page * $perPage);
$queryPrefix = $queryString === '' ? '?' : '?' . $queryString . '&';

$selected = static fn (string $key, mixed $value): string => (string) ($filters[$key] ?? '') === (string) $value ? 'selected' : '';
$checkedSkill = static fn (mixed $value): string => in_array((string) $value, $selectedSkills, true) ? 'checked' : '';
?>
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="employer-search-page">
    <form class="employer-search-shell" method="get" action="<?= View::url('/find-cvs') ?>">
        <aside class="employer-filter-panel">
            <section class="employer-filter-section">
                <h2>Quick Search</h2>
                <label class="employer-search-input">
                    <span>search</span>
                    <input type="search" name="keyword" value="<?= View::e($filters['keyword'] ?? '') ?>" placeholder="Keywords, job title...">
                </label>
            </section>

            <section class="employer-filter-section">
                <h2>Core Filters</h2>

                <label class="employer-filter-field">
                    <span>Category</span>
                    <select name="category_id">
                        <option value="">Any category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= $selected('category_id', $category['id']) ?>><?= View::e($category['name']) ?></option>
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
                    <span>Education Level</span>
                    <select name="degree_level_id">
                        <option value="">Any degree</option>
                        <?php foreach ($degreeLevels as $degreeLevel): ?>
                            <option value="<?= (int) $degreeLevel['id'] ?>" <?= $selected('degree_level_id', $degreeLevel['id']) ?>><?= View::e($degreeLevel['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </section>

            <section class="employer-filter-section">
                <div class="employer-filter-heading-row">
                    <h2>Skills &amp; Proficiency</h2>
                    <a href="<?= View::url('/find-cvs') ?>">Clear</a>
                </div>

                <label class="employer-proficiency-slider">
                    <span>Minimum Proficiency</span>
                    <strong>
                        Level
                        <output data-employer-proficiency-output>
                            <?= (int) max(1, min(10, (int) ($filters['min_proficiency'] ?? 1))) ?>
                        </output>
                        / 10
                    </strong>
                    <input
                        type="range"
                        name="min_proficiency"
                        min="1"
                        max="10"
                        step="1"
                        value="<?= (int) max(1, min(10, (int) ($filters['min_proficiency'] ?? 1))) ?>"
                        data-employer-proficiency-range
                    >
                    <div aria-hidden="true">
                        <span>1</span>
                        <span>5</span>
                        <span>10</span>
                    </div>
                </label>

                <div class="employer-skill-picker" aria-label="Skill filters">
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
                        <option value="recent" <?= $selected('sort', 'recent') ?>>Most recently updated</option>
                        <option value="alphabetical" <?= $selected('sort', 'alphabetical') ?>>Alphabetical order</option>
                        <option value="experience" <?= $selected('sort', 'experience') ?>>Approximate experience length</option>
                    </select>
                </label>
            </section>

            <div class="employer-filter-actions">
                <button type="submit">Apply Filters</button>
            </div>
        </aside>

        <section class="employer-results-panel">
            <header class="employer-results-header">
                <div>
                    <h1>Candidate Discovery</h1>
                    <p>Showing <?= (int) $shown ?> of <?= (int) $total ?> curated profiles matching your criteria</p>
                </div>
                <div class="employer-view-toggle" aria-hidden="true">
                    <span class="active">grid_view</span>
                    <span>view_list</span>
                </div>
            </header>

            <?php if ($candidates === []): ?>
                <section class="employer-empty-state">
                    <span>manage_search</span>
                    <h2>No matching CVs found</h2>
                    <p>Try broadening your filters or removing some required skills.</p>
                </section>
            <?php else: ?>
                <section class="employer-candidate-grid">
                    <?php foreach ($candidates as $candidate): ?>
                        <a class="employer-candidate-card" href="<?= View::url('/find-cvs/show?id=' . (int) $candidate['id']) ?>">
                            <div class="employer-candidate-topline">
                                <div class="employer-candidate-avatar">
                                    <?php if (! empty($candidate['avatar'])): ?>
                                        <img src="<?= View::e($candidate['avatar']) ?>" alt="<?= View::e($candidate['full_name']) ?> avatar">
                                    <?php else: ?>
                                        <?= View::e($candidate['initials']) ?>
                                    <?php endif; ?>
                                </div>
                                <span>bookmark</span>
                            </div>

                            <h2><?= View::e($candidate['full_name']) ?></h2>
                            <strong><?= View::e($candidate['headline']) ?></strong>
                            <p><?= View::e($candidate['summary'] ?: 'No professional summary has been added yet.') ?></p>

                            <div class="employer-card-meta">
                                <span><?= View::e($candidate['city']) ?></span>
                                <span><?= (int) $candidate['experience_years'] ?> yrs exp</span>
                            </div>

                            <div class="employer-card-skills">
                                <?php foreach ($candidate['skills'] as $skill): ?>
                                    <?php if ($skill !== ''): ?>
                                        <span><?= View::e($skill) ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </section>

                <?php if ($shown < $total): ?>
                    <div class="employer-load-more">
                        <a href="<?= View::url('/find-cvs' . $queryPrefix . 'page=' . ((int) $page + 1)) ?>">
                            Load More Candidates
                            <span>expand_more</span>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </form>
</main>
