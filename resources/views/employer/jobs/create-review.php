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
$companyAvatarUrl = trim((string) ($draft['company_avatar_url'] ?? ''));
$locationParts = array_values(array_filter([
    ($draft['district_id'] ?? '') === '' ? '' : $nameById($districts ?? [], (string) $draft['district_id']),
    ($draft['city_id'] ?? '') === '' ? '' : $nameById($cities ?? [], (string) $draft['city_id']),
    ($draft['country_id'] ?? '') === '' ? '' : $nameById($countries ?? [], (string) $draft['country_id']),
], static fn (string $part): bool => $part !== '' && $part !== 'Not selected'));
$locationLabel = $locationParts === [] ? 'Not selected' : implode(', ', $locationParts);
$requiredSkills = array_values(array_filter($draft['skills'] ?? [], static fn (array $skill): bool => ($skill['skill_id'] ?? '') !== ''));
$requiredSkillLabels = array_map(
    static fn (array $skill): string => sprintf(
        '%s - %s',
        $nameById($skills ?? [], (string) ($skill['skill_id'] ?? '')),
        $nameById($proficiencyLevels ?? [], (string) ($skill['minimum_proficiency_level_id'] ?? ''))
    ),
    $requiredSkills
);
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
                    <span>Company Name</span>
                    <div class="job-review-value"><?= View::e($draftText('company_name')) ?></div>
                </div>
                <div class="builder-field">
                    <span>Company Avatar</span>
                    <div class="job-review-value">
                        <?php if ($companyAvatarUrl !== ''): ?>
                            <img class="company-avatar-preview" src="<?= View::e($companyAvatarUrl) ?>" alt="Company avatar">
                        <?php else: ?>
                            <span class="job-review-empty">Not uploaded</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="builder-field builder-field-wide">
                    <span>Company Description</span>
                    <div class="job-review-value job-review-long"><?= nl2br(View::e($draftText('company_description'))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Job Title</span>
                    <div class="job-review-value"><?= View::e($nameById($jobTitles ?? [], (string) ($draft['job_title_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Category</span>
                    <div class="job-review-value"><?= View::e($nameById($jobCategories ?? [], (string) ($draft['job_category_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Employment Type</span>
                    <div class="job-review-value"><?= View::e($nameById($employmentTypes ?? [], (string) ($draft['employment_type_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Industry</span>
                    <div class="job-review-value"><?= View::e($nameById($industries ?? [], (string) ($draft['industry_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Job Level</span>
                    <div class="job-review-value"><?= View::e($nameById($jobLevels ?? [], (string) ($draft['job_level_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Number of Openings</span>
                    <div class="job-review-value"><?= View::e((string) ($draft['number_of_openings'] ?? 'Not provided')) ?></div>
                </div>
                <div class="builder-field">
                    <span>Location</span>
                    <div class="job-review-value"><?= View::e($locationLabel) ?></div>
                </div>
                <div class="builder-field">
                    <span>Work Arrangement</span>
                    <div class="job-review-value"><?= View::e($nameById($workArrangements ?? [], (string) ($draft['work_arrangement_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Salary</span>
                    <div class="job-review-value"><?= View::e($nameById($salaryRanges ?? [], (string) ($draft['salary_range_id'] ?? ''), 'label') . ' / ' . $nameById($salaryTypes ?? [], (string) ($draft['salary_type_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Minimum Degree</span>
                    <div class="job-review-value"><?= View::e($nameById($degreeLevels ?? [], (string) ($draft['minimum_degree_level_id'] ?? ''))) ?></div>
                </div>
                <div class="builder-field">
                    <span>Minimum Years Experience</span>
                    <div class="job-review-value"><?= View::e((string) ($draft['minimum_years_experience'] ?? 'Not provided')) ?></div>
                </div>
                <div class="builder-field builder-field-wide">
                    <span>Benefits</span>
                    <div class="job-review-value job-review-long"><?= nl2br(View::e($draftText('benefits'))) ?></div>
                </div>
                <div class="builder-field builder-field-wide">
                    <span>Required Skills</span>
                    <div class="job-review-value">
                        <?php if ($requiredSkillLabels === []): ?>
                            <span class="job-review-empty">Not selected</span>
                        <?php else: ?>
                            <div class="job-review-skill-tags">
                                <?php foreach ($requiredSkillLabels as $skillLabel): ?>
                                    <span><?= View::e($skillLabel) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="builder-field builder-field-wide">
                    <span>Responsibilities</span>
                    <div class="job-review-value job-review-long"><?= nl2br(View::e($draftText('responsibilities'))) ?></div>
                </div>
                <div class="builder-field builder-field-wide">
                    <span>Required Qualifications</span>
                    <div class="job-review-value job-review-long"><?= nl2br(View::e($draftText('required_qualifications'))) ?></div>
                </div>
                <div class="builder-field builder-field-wide">
                    <span>Preferred Skills</span>
                    <div class="job-review-value job-review-long"><?= nl2br(View::e($draftText('preferred_skills'))) ?></div>
                </div>
                <div class="builder-field builder-field-wide">
                    <span>Additional Notes</span>
                    <div class="job-review-value job-review-long"><?= nl2br(View::e($draftText('additional_notes'))) ?></div>
                </div>
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
