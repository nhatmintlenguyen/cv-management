<?php

use App\Core\View;

$activeJobSeekerTab = 'builder';
$old = $_SESSION['_old'] ?? [];
$cv = $cv ?? null;
$educations = $old['educations'] ?? $educations ?? [];
$workHistories = $old['work_histories'] ?? $workHistories ?? [];
$institutions = $institutions ?? [];
$degreeLevels = $degreeLevels ?? [];
$majors = $majors ?? [];
$jobTitles = $jobTitles ?? [];
$employmentTypes = $employmentTypes ?? [];
$industries = $industries ?? [];

$educations = $educations === [] ? [[]] : array_values($educations);
$workHistories = $workHistories === [] ? [[]] : array_values($workHistories);

$selected = static fn (array $row, string $field, mixed $value): string => (string) ($row[$field] ?? '') === (string) $value ? 'selected' : '';
$checked = static fn (array $row, string $field): string => ! empty($row[$field]) ? 'checked' : '';
$rowValue = static fn (array $row, string $field, mixed $default = ''): string => (string) ($row[$field] ?? $default);
?>
<?php require dirname(__DIR__) . '/job-seeker/partials/topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>CV Builder</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Education &amp; Experience</strong>
        </div>

        <div class="builder-heading-row">
            <div>
                <h1>Step 2: Academic &amp; Career Narrative</h1>
                <p>Add your education background and work history. You can add multiple degrees and multiple work experiences.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Education &amp; Experience</strong>
            </div>
        </div>
    </section>

    <div class="builder-shell">
        <aside class="builder-stepper" aria-label="CV builder progress">
            <div class="builder-step completed">
                <span>1</span>
                <div>
                    <strong>Personal Info</strong>
                    <small>Saved</small>
                </div>
            </div>
            <div class="builder-step active">
                <span>2</span>
                <div>
                    <strong>Education &amp; Experience</strong>
                    <small>In Progress</small>
                </div>
            </div>
            <div class="builder-step">
                <span>3</span>
                <div>
                    <strong>Qualifications &amp; Skills</strong>
                </div>
            </div>
            <div class="builder-step">
                <span>4</span>
                <div>
                    <strong>Review</strong>
                </div>
            </div>
        </aside>

        <?php if ($cv === null): ?>
            <section class="builder-form-card">
                <div class="builder-section-title">
                    <span>info</span>
                    <h2>Personal Information Required</h2>
                </div>
                <p class="builder-helper-text">Please save Step 1 before adding education and work history.</p>
                <div class="builder-form-actions">
                    <a class="builder-primary-button" href="<?= View::url('/cv/edit/personal-info') ?>">
                        Back to Step 1
                        <span>arrow_forward</span>
                    </a>
                </div>
            </section>
        <?php else: ?>
            <form class="builder-form-card js-dynamic-builder-form" method="post" action="<?= View::url('/cv/academic') ?>">
                <section class="builder-form-section">
                    <div class="builder-section-title builder-section-title-row">
                        <div>
                            <span>school</span>
                            <h2>Education</h2>
                        </div>
                        <button class="builder-secondary-button js-add-dynamic-row" type="button" data-target="education">
                            <span>add</span>
                            Add Degree
                        </button>
                    </div>

                    <div class="builder-dynamic-list" data-dynamic-list="education">
                        <?php foreach ($educations as $index => $education): ?>
                            <article class="builder-dynamic-item" data-dynamic-item>
                                <div class="builder-dynamic-heading">
                                    <strong>Degree <span data-item-number><?= $index + 1 ?></span></strong>
                                    <button class="builder-remove-button js-remove-dynamic-row" type="button">Remove</button>
                                </div>

                                <div class="builder-form-grid">
                                    <label class="builder-field">
                                        <span>Institution</span>
                                        <select name="educations[<?= $index ?>][institution_id]" required>
                                            <option value="">Select institution</option>
                                            <?php foreach ($institutions as $institution): ?>
                                                <option value="<?= (int) $institution['id'] ?>" <?= $selected($education, 'institution_id', $institution['id']) ?>><?= View::e($institution['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Degree Level</span>
                                        <select name="educations[<?= $index ?>][degree_level_id]" required>
                                            <option value="">Select degree level</option>
                                            <?php foreach ($degreeLevels as $degreeLevel): ?>
                                                <option value="<?= (int) $degreeLevel['id'] ?>" <?= $selected($education, 'degree_level_id', $degreeLevel['id']) ?>><?= View::e($degreeLevel['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field builder-field-wide">
                                        <span>Major</span>
                                        <select name="educations[<?= $index ?>][major_id]" required>
                                            <option value="">Select major</option>
                                            <?php foreach ($majors as $major): ?>
                                                <option value="<?= (int) $major['id'] ?>" <?= $selected($education, 'major_id', $major['id']) ?>><?= View::e($major['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Start Year</span>
                                        <input type="number" name="educations[<?= $index ?>][start_year]" value="<?= View::e($rowValue($education, 'start_year')) ?>" min="1950" max="<?= (int) date('Y') + 1 ?>" placeholder="2019" required>
                                    </label>

                                    <label class="builder-field">
                                        <span>End Year</span>
                                        <input type="number" name="educations[<?= $index ?>][end_year]" value="<?= View::e($rowValue($education, 'end_year')) ?>" min="1950" max="<?= (int) date('Y') + 1 ?>" placeholder="2023" required>
                                    </label>

                                    <label class="builder-field builder-field-wide">
                                        <span>Description</span>
                                        <textarea name="educations[<?= $index ?>][description]" rows="4" placeholder="Describe your study focus, thesis, achievements, or relevant coursework."><?= View::e($rowValue($education, 'description')) ?></textarea>
                                    </label>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="builder-form-section">
                    <div class="builder-section-title builder-section-title-row">
                        <div>
                            <span>business_center</span>
                            <h2>Work History</h2>
                        </div>
                        <button class="builder-secondary-button js-add-dynamic-row" type="button" data-target="work">
                            <span>add</span>
                            Add Work History
                        </button>
                    </div>

                    <div class="builder-dynamic-list" data-dynamic-list="work">
                        <?php foreach ($workHistories as $index => $workHistory): ?>
                            <article class="builder-dynamic-item" data-dynamic-item>
                                <div class="builder-dynamic-heading">
                                    <strong>Work History <span data-item-number><?= $index + 1 ?></span></strong>
                                    <button class="builder-remove-button js-remove-dynamic-row" type="button">Remove</button>
                                </div>

                                <div class="builder-form-grid">
                                    <label class="builder-field">
                                        <span>Job Title</span>
                                        <select name="work_histories[<?= $index ?>][job_title_id]" required>
                                            <option value="">Select job title</option>
                                            <?php foreach ($jobTitles as $jobTitle): ?>
                                                <option value="<?= (int) $jobTitle['id'] ?>" <?= $selected($workHistory, 'job_title_id', $jobTitle['id']) ?>><?= View::e($jobTitle['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Employment Type</span>
                                        <select name="work_histories[<?= $index ?>][employment_type_id]" required>
                                            <option value="">Select employment type</option>
                                            <?php foreach ($employmentTypes as $employmentType): ?>
                                                <option value="<?= (int) $employmentType['id'] ?>" <?= $selected($workHistory, 'employment_type_id', $employmentType['id']) ?>><?= View::e($employmentType['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Industry</span>
                                        <select name="work_histories[<?= $index ?>][industry_id]" required>
                                            <option value="">Select industry</option>
                                            <?php foreach ($industries as $industry): ?>
                                                <option value="<?= (int) $industry['id'] ?>" <?= $selected($workHistory, 'industry_id', $industry['id']) ?>><?= View::e($industry['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Company Name</span>
                                        <input type="text" name="work_histories[<?= $index ?>][company_name]" value="<?= View::e($rowValue($workHistory, 'company_name')) ?>" placeholder="e.g. OneCV Labs" required>
                                    </label>

                                    <label class="builder-field">
                                        <span>Start Year</span>
                                        <input type="number" name="work_histories[<?= $index ?>][start_year]" value="<?= View::e($rowValue($workHistory, 'start_year')) ?>" min="1950" max="<?= (int) date('Y') + 1 ?>" placeholder="2022" required>
                                    </label>

                                    <label class="builder-field">
                                        <span>End Year</span>
                                        <input type="number" name="work_histories[<?= $index ?>][end_year]" value="<?= View::e($rowValue($workHistory, 'end_year')) ?>" min="1950" max="<?= (int) date('Y') + 1 ?>" placeholder="2024">
                                    </label>

                                    <label class="builder-check-field builder-field-wide">
                                        <input type="checkbox" name="work_histories[<?= $index ?>][is_current]" value="1" <?= $checked($workHistory, 'is_current') ?>>
                                        <span>This is my current role</span>
                                    </label>

                                    <label class="builder-field builder-field-wide">
                                        <span>Job Description</span>
                                        <textarea name="work_histories[<?= $index ?>][job_description]" rows="5" placeholder="Summarize responsibilities, achievements, technologies, or measurable impact." required><?= View::e($rowValue($workHistory, 'job_description')) ?></textarea>
                                    </label>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <div class="builder-form-actions">
                    <a class="builder-ghost-button" href="<?= View::url('/cv/edit/personal-info') ?>">
                        <span>arrow_back</span>
                        Back to Step 1
                    </a>

                    <div>
                        <button class="builder-secondary-button" type="submit">Save Progress</button>
                        <button class="builder-primary-button" type="button">
                            Next Step
                            <span>arrow_forward</span>
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>
