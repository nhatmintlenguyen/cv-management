<?php

use App\Core\View;

$activeSiteTab = 'post-job';
$draft = $draft ?? [];
$currentStep = 4;
$nameById = static function (array $rows, string $id, string $field = 'name'): string {
    foreach ($rows as $row) {
        if ((string) $row['id'] === (string) $id) {
            return (string) ($row[$field] ?? '');
        }
    }

    return 'Not selected';
};
$draftText = static fn (string $field): string => trim((string) ($draft[$field] ?? '')) ?: 'Not provided yet.';
?>
<?php require dirname(__DIR__, 2) . '/partials/site-topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>Post Job</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Review &amp; Publish</strong>
        </div>

        <div class="builder-heading-row">
            <div>
                <h1>Step 4: Review &amp; Publish</h1>
                <p>Review the vacancy draft before publishing it for job seekers to discover.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Final Review</strong>
            </div>
        </div>
    </section>

    <div class="builder-shell">
        <?php require __DIR__ . '/partials/stepper.php'; ?>

        <section class="builder-form-card">
            <div class="builder-section-title">
                <span>preview</span>
                <h2>Vacancy Preview</h2>
            </div>

            <div class="builder-form-grid">
                <div class="builder-field">
                    <span>Job Title</span>
                    <input value="<?= View::e($nameById($jobTitles ?? [], (string) ($draft['job_title_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Category</span>
                    <input value="<?= View::e($nameById($jobCategories ?? [], (string) ($draft['job_category_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Employment Type</span>
                    <input value="<?= View::e($nameById($employmentTypes ?? [], (string) ($draft['employment_type_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Industry</span>
                    <input value="<?= View::e($nameById($industries ?? [], (string) ($draft['industry_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Job Level</span>
                    <input value="<?= View::e($nameById($jobLevels ?? [], (string) ($draft['job_level_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Number of Openings</span>
                    <input value="<?= View::e((string) ($draft['number_of_openings'] ?? 'Not provided')) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Location</span>
                    <input value="<?= View::e($nameById($cities ?? [], (string) ($draft['city_id'] ?? '')) . ', ' . $nameById($countries ?? [], (string) ($draft['country_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Work Arrangement</span>
                    <input value="<?= View::e($nameById($workArrangements ?? [], (string) ($draft['work_arrangement_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Salary</span>
                    <input value="<?= View::e($nameById($salaryRanges ?? [], (string) ($draft['salary_range_id'] ?? ''), 'label') . ' / ' . $nameById($salaryTypes ?? [], (string) ($draft['salary_type_id'] ?? ''))) ?>" readonly>
                </div>
                <div class="builder-field">
                    <span>Minimum Degree</span>
                    <input value="<?= View::e($nameById($degreeLevels ?? [], (string) ($draft['minimum_degree_level_id'] ?? ''))) ?>" readonly>
                </div>
                <label class="builder-field builder-field-wide">
                    <span>Benefits</span>
                    <textarea rows="4" readonly><?= View::e($draftText('benefits')) ?></textarea>
                </label>
                <label class="builder-field builder-field-wide">
                    <span>Responsibilities</span>
                    <textarea rows="5" readonly><?= View::e($draftText('responsibilities')) ?></textarea>
                </label>
                <label class="builder-field builder-field-wide">
                    <span>Required Qualifications</span>
                    <textarea rows="5" readonly><?= View::e($draftText('required_qualifications')) ?></textarea>
                </label>
            </div>

            <div class="builder-form-actions">
                <a class="builder-ghost-button" href="<?= View::url('/employer/jobs/create/requirements') ?>">
                    <span>arrow_back</span>
                    Back to Step 3
                </a>

                <form method="post" action="<?= View::url('/employer/jobs/create/publish') ?>">
                    <button class="builder-primary-button" type="submit">
                        Publish Job
                        <span>check</span>
                    </button>
                </form>
            </div>
        </section>
    </div>
</main>
