<?php

use App\Core\View;

$activeJobSeekerTab = 'builder';
$old = $_SESSION['_old'] ?? [];
$cv = $cv ?? null;
$certificates = $old['certificates'] ?? $certificates ?? [];
$cvSkills = $old['skills'] ?? $cvSkills ?? [];
$certificateNames = $certificateNames ?? [];
$issuingOrganizations = $issuingOrganizations ?? [];
$skills = $skills ?? [];
$proficiencyLevels = $proficiencyLevels ?? [];

$certificates = $certificates === [] ? [[]] : array_values($certificates);
$cvSkills = $cvSkills === [] ? [[]] : array_values($cvSkills);

$selected = static fn (array $row, string $field, mixed $value): string => (string) ($row[$field] ?? '') === (string) $value ? 'selected' : '';
$rowValue = static fn (array $row, string $field, mixed $default = ''): string => (string) ($row[$field] ?? $default);
?>
<?php require dirname(__DIR__) . '/job-seeker/partials/topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>CV Builder</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Qualifications &amp; Skills</strong>
        </div>

        <div class="builder-heading-row">
            <div>
                <h1>Step 3: Qualifications &amp; Skills</h1>
                <p>Add certificates and select up to 5 strongest skills with proficiency levels.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Qualifications &amp; Skills</strong>
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
            <div class="builder-step completed">
                <span>2</span>
                <div>
                    <strong>Education &amp; Experience</strong>
                    <small>Saved</small>
                </div>
            </div>
            <div class="builder-step active">
                <span>3</span>
                <div>
                    <strong>Qualifications &amp; Skills</strong>
                    <small>In Progress</small>
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
                <p class="builder-helper-text">Please save Step 1 before adding certificates and skills.</p>
                <div class="builder-form-actions">
                    <a class="builder-primary-button" href="<?= View::url('/cv/edit/personal-info') ?>">
                        Back to Step 1
                        <span>arrow_forward</span>
                    </a>
                </div>
            </section>
        <?php else: ?>
            <form class="builder-form-card js-dynamic-builder-form" method="post" action="<?= View::url('/cv/qualifications') ?>">
                <section class="builder-form-section">
                    <div class="builder-section-title builder-section-title-row">
                        <div>
                            <span>workspace_premium</span>
                            <h2>Certificates</h2>
                        </div>
                        <button class="builder-secondary-button js-add-dynamic-row" type="button" data-target="certificate">
                            <span>add</span>
                            Add Certificate
                        </button>
                    </div>

                    <div class="builder-dynamic-list" data-dynamic-list="certificate">
                        <?php foreach ($certificates as $index => $certificate): ?>
                            <article class="builder-dynamic-item" data-dynamic-item>
                                <div class="builder-dynamic-heading">
                                    <strong>Certificate <span data-item-number><?= $index + 1 ?></span></strong>
                                    <button class="builder-remove-button js-remove-dynamic-row" type="button">Remove</button>
                                </div>

                                <div class="builder-form-grid">
                                    <label class="builder-field">
                                        <span>Certificate Name</span>
                                        <select name="certificates[<?= $index ?>][certificate_name_id]" required>
                                            <option value="">Select certificate</option>
                                            <?php foreach ($certificateNames as $certificateName): ?>
                                                <option value="<?= (int) $certificateName['id'] ?>" <?= $selected($certificate, 'certificate_name_id', $certificateName['id']) ?>><?= View::e($certificateName['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Issuing Organization</span>
                                        <select name="certificates[<?= $index ?>][issuing_organization_id]" required>
                                            <option value="">Select organization</option>
                                            <?php foreach ($issuingOrganizations as $organization): ?>
                                                <option value="<?= (int) $organization['id'] ?>" <?= $selected($certificate, 'issuing_organization_id', $organization['id']) ?>><?= View::e($organization['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Year Issued</span>
                                        <input type="number" name="certificates[<?= $index ?>][year_issued]" value="<?= View::e($rowValue($certificate, 'year_issued')) ?>" min="1950" max="<?= (int) date('Y') + 1 ?>" placeholder="2024" required>
                                    </label>

                                    <label class="builder-field builder-field-wide">
                                        <span>Description</span>
                                        <textarea name="certificates[<?= $index ?>][description]" rows="4" placeholder="Describe what this certificate validates."><?= View::e($rowValue($certificate, 'description')) ?></textarea>
                                    </label>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="builder-form-section">
                    <div class="builder-section-title builder-section-title-row">
                        <div>
                            <span>psychology</span>
                            <h2>Strongest Skills</h2>
                        </div>
                        <button class="builder-secondary-button js-add-dynamic-row" type="button" data-target="skill">
                            <span>add</span>
                            Add Skill
                        </button>
                    </div>

                    <p class="builder-helper-text">Choose up to 5 skills. The database trigger also enforces this limit when records are saved.</p>

                    <div class="builder-dynamic-list" data-dynamic-list="skill" data-max-items="5">
                        <?php foreach ($cvSkills as $index => $cvSkill): ?>
                            <article class="builder-dynamic-item" data-dynamic-item>
                                <div class="builder-dynamic-heading">
                                    <strong>Skill <span data-item-number><?= $index + 1 ?></span></strong>
                                    <button class="builder-remove-button js-remove-dynamic-row" type="button">Remove</button>
                                </div>

                                <div class="builder-form-grid">
                                    <label class="builder-field">
                                        <span>Skill</span>
                                        <select name="skills[<?= $index ?>][skill_id]" required>
                                            <option value="">Select skill</option>
                                            <?php foreach ($skills as $skill): ?>
                                                <option value="<?= (int) $skill['id'] ?>" <?= $selected($cvSkill, 'skill_id', $skill['id']) ?>><?= View::e($skill['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>

                                    <label class="builder-field">
                                        <span>Proficiency Level</span>
                                        <select name="skills[<?= $index ?>][proficiency_level_id]" required>
                                            <option value="">Select proficiency</option>
                                            <?php foreach ($proficiencyLevels as $level): ?>
                                                <option value="<?= (int) $level['id'] ?>" <?= $selected($cvSkill, 'proficiency_level_id', $level['id']) ?>><?= View::e($level['name']) ?> (<?= (int) $level['level_value'] ?>/10)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <div class="builder-form-actions">
                    <a class="builder-ghost-button" href="<?= View::url('/cv/edit/academic') ?>">
                        <span>arrow_back</span>
                        Back to Step 2
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
