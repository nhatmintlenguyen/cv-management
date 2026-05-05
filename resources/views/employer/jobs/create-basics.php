<?php

use App\Core\View;

$activeSiteTab = 'post-job';
$draft = $draft ?? [];
$value = static fn (string $field, mixed $default = ''): string => (string) ($draft[$field] ?? $default);
$selected = static fn (string $field, mixed $id): string => (string) ($draft[$field] ?? '') === (string) $id ? 'selected' : '';
$currentStep = 1;
?>
<?php require dirname(__DIR__, 2) . '/partials/site-topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>Post Job</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Job Basics</strong>
        </div>

        <div class="builder-heading-row">
            <div>
                <h1>Step 1: Job Basics</h1>
                <p>Define the core metadata that makes this vacancy searchable and easy to understand.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Job Basics</strong>
            </div>
        </div>
    </section>

    <div class="builder-shell">
        <?php require __DIR__ . '/partials/stepper.php'; ?>

        <form class="builder-form-card" method="post" action="<?= View::url('/employer/jobs/create/basics') ?>">
            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>badge</span>
                    <h2>Role Definition</h2>
                </div>

                <div class="builder-form-grid">
                    <label class="builder-field">
                        <span>Job Title</span>
                        <select name="job_title_id" required>
                            <option value="">Select job title</option>
                            <?php foreach (($jobTitles ?? []) as $jobTitle): ?>
                                <option value="<?= (int) $jobTitle['id'] ?>" <?= $selected('job_title_id', $jobTitle['id']) ?>><?= View::e($jobTitle['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Job Category</span>
                        <select name="job_category_id" required>
                            <option value="">Select job category</option>
                            <?php foreach (($jobCategories ?? []) as $jobCategory): ?>
                                <option value="<?= (int) $jobCategory['id'] ?>" <?= $selected('job_category_id', $jobCategory['id']) ?>><?= View::e($jobCategory['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Employment Type</span>
                        <select name="employment_type_id" required>
                            <option value="">Select employment type</option>
                            <?php foreach (($employmentTypes ?? []) as $employmentType): ?>
                                <option value="<?= (int) $employmentType['id'] ?>" <?= $selected('employment_type_id', $employmentType['id']) ?>><?= View::e($employmentType['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Industry</span>
                        <select name="industry_id" required>
                            <option value="">Select industry</option>
                            <?php foreach (($industries ?? []) as $industry): ?>
                                <option value="<?= (int) $industry['id'] ?>" <?= $selected('industry_id', $industry['id']) ?>><?= View::e($industry['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Job Level</span>
                        <select name="job_level_id" required>
                            <option value="">Select job level</option>
                            <?php foreach (($jobLevels ?? []) as $jobLevel): ?>
                                <option value="<?= (int) $jobLevel['id'] ?>" <?= $selected('job_level_id', $jobLevel['id']) ?>><?= View::e($jobLevel['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Number of Openings</span>
                        <input type="number" min="1" name="number_of_openings" value="<?= View::e($value('number_of_openings', '1')) ?>" required>
                    </label>
                </div>
            </section>

            <div class="builder-form-actions">
                <a class="builder-ghost-button" href="<?= View::url('/employer/jobs') ?>">
                    <span>arrow_back</span>
                    Back to My Jobs
                </a>

                <div>
                    <button class="builder-secondary-button" type="submit">Save Progress</button>
                    <button class="builder-primary-button" type="submit" name="next_step" value="location">
                        Next Step
                        <span>arrow_forward</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
