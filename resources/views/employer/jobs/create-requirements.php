<?php

use App\Core\View;

$activeSiteTab = 'post-job';
$draft = $draft ?? [];
$skillsDraft = is_array($draft['skills'] ?? null) ? $draft['skills'] : [[]];
$value = static fn (string $field, mixed $default = ''): string => (string) ($draft[$field] ?? $default);
$selected = static fn (string $field, mixed $id): string => (string) ($draft[$field] ?? '') === (string) $id ? 'selected' : '';
$currentStep = 3;
?>
<?php require dirname(__DIR__, 2) . '/partials/site-topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>Post Job</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Requirements &amp; Description</strong>
        </div>

        <div class="builder-heading-row">
            <div>
                <h1>Step 3: Requirements &amp; Description</h1>
                <p>Describe the work clearly and define the skills, degree, and experience needed for this role.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Requirements</strong>
            </div>
        </div>
    </section>

    <div class="builder-shell">
        <?php require __DIR__ . '/partials/stepper.php'; ?>

        <form class="builder-form-card js-dynamic-builder-form" method="post" action="<?= View::url('/employer/jobs/create/requirements') ?>">
            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>description</span>
                    <h2>Job Description</h2>
                </div>

                <div class="builder-form-grid">
                    <label class="builder-field builder-field-wide">
                        <span>Responsibilities</span>
                        <textarea name="responsibilities" rows="6" placeholder="List the main responsibilities and day-to-day work." required><?= View::e($value('responsibilities')) ?></textarea>
                    </label>

                    <label class="builder-field builder-field-wide">
                        <span>Required Qualifications</span>
                        <textarea name="required_qualifications" rows="6" placeholder="Explain the non-negotiable qualifications for this vacancy." required><?= View::e($value('required_qualifications')) ?></textarea>
                    </label>

                    <label class="builder-field builder-field-wide">
                        <span>Preferred Skills</span>
                        <textarea name="preferred_skills" rows="5" placeholder="Mention nice-to-have skills or experience."><?= View::e($value('preferred_skills')) ?></textarea>
                    </label>

                    <label class="builder-field builder-field-wide">
                        <span>Additional Notes</span>
                        <textarea name="additional_notes" rows="4" placeholder="Add schedule notes, portfolio requirements, interview process, or other context."><?= View::e($value('additional_notes')) ?></textarea>
                    </label>
                </div>
            </section>

            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>school</span>
                    <h2>Education &amp; Experience</h2>
                </div>

                <div class="builder-form-grid">
                    <label class="builder-field">
                        <span>Minimum Degree Level</span>
                        <select name="minimum_degree_level_id" required>
                            <option value="">Select degree level</option>
                            <?php foreach (($degreeLevels ?? []) as $degreeLevel): ?>
                                <option value="<?= (int) $degreeLevel['id'] ?>" <?= $selected('minimum_degree_level_id', $degreeLevel['id']) ?>><?= View::e($degreeLevel['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Minimum Years Experience</span>
                        <input type="number" min="0" max="50" name="minimum_years_experience" value="<?= View::e($value('minimum_years_experience', '0')) ?>" required>
                    </label>
                </div>
            </section>

            <section class="builder-form-section">
                <div class="builder-section-title-row">
                    <div class="builder-section-title">
                        <span>psychology</span>
                        <h2>Required Skills</h2>
                    </div>
                    <button class="builder-secondary-button js-add-dynamic-row" type="button" data-target="skill">
                        <span>add</span>
                        Add Skill
                    </button>
                </div>

                <p class="builder-helper-text">Add up to 5 required skills. The Add Skill button becomes disabled once the limit is reached.</p>

                <div class="builder-dynamic-list" data-dynamic-list="skill" data-max-items="5">
                    <?php foreach (array_slice(array_pad($skillsDraft, 1, []), 0, 5) as $index => $skillDraft): ?>
                        <article class="builder-dynamic-item" data-dynamic-item>
                            <div class="builder-dynamic-heading">
                                <strong>Required Skill <span data-item-number><?= $index + 1 ?></span></strong>
                                <button class="builder-remove-button js-remove-dynamic-row" type="button" <?= $index === 0 ? 'disabled' : '' ?>>Remove</button>
                            </div>

                            <div class="builder-form-grid">
                                <label class="builder-field">
                                    <span>Skill</span>
                                    <select name="skills[<?= $index ?>][skill_id]">
                                        <option value="">Select skill</option>
                                        <?php foreach (($skills ?? []) as $skill): ?>
                                            <option value="<?= (int) $skill['id'] ?>" <?= (string) ($skillDraft['skill_id'] ?? '') === (string) $skill['id'] ? 'selected' : '' ?>><?= View::e($skill['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>

                                <label class="builder-field">
                                    <span>Minimum Proficiency</span>
                                    <select name="skills[<?= $index ?>][minimum_proficiency_level_id]">
                                        <option value="">Select proficiency</option>
                                        <?php foreach (($proficiencyLevels ?? []) as $level): ?>
                                            <option value="<?= (int) $level['id'] ?>" <?= (string) ($skillDraft['minimum_proficiency_level_id'] ?? '') === (string) $level['id'] ? 'selected' : '' ?>><?= View::e($level['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </label>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="builder-form-actions">
                <a class="builder-ghost-button" href="<?= View::url('/employer/jobs/create/location') ?>">
                    <span>arrow_back</span>
                    Back to Step 2
                </a>

                <div>
                    <button class="builder-secondary-button" type="submit">Save Progress</button>
                    <button class="builder-primary-button" type="submit" name="next_step" value="review">
                        Next Step
                        <span>arrow_forward</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
