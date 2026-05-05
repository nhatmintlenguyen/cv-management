<?php

use App\Core\View;

$activeSiteTab = 'post-job';
$draft = $draft ?? [];
$value = static fn (string $field, mixed $default = ''): string => (string) ($draft[$field] ?? $default);
$selected = static fn (string $field, mixed $id): string => (string) ($draft[$field] ?? '') === (string) $id ? 'selected' : '';
$currentStep = 2;
$companyAvatarUrl = trim((string) ($draft['company_avatar_url'] ?? ''));
?>
<?php require dirname(__DIR__, 2) . '/partials/site-topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>Post Job</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Location &amp; Compensation</strong>
        </div>

        <div class="builder-heading-row">
            <div>
                <h1>Step 2: Location &amp; Compensation</h1>
                <p>Set where the role is based, how work happens, and what compensation range candidates can expect.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Location &amp; Compensation</strong>
            </div>
        </div>
    </section>

    <div class="builder-shell">
        <?php require __DIR__ . '/partials/stepper.php'; ?>

        <form class="builder-form-card js-builder-identity-form" method="post" action="<?= View::url('/employer/jobs/create/location') ?>" enctype="multipart/form-data">
            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>apartment</span>
                    <h2>Company Profile</h2>
                </div>

                <div class="builder-form-grid">
                    <label class="builder-field">
                        <span>Company Name</span>
                        <input type="text" name="company_name" value="<?= View::e($value('company_name')) ?>" placeholder="e.g. OneTech Labs" required>
                    </label>

                    <label class="builder-field">
                        <span>Company Avatar</span>
                        <input type="file" name="company_avatar" accept="image/jpeg,image/png,image/webp,image/gif">
                    </label>

                    <?php if ($companyAvatarUrl !== ''): ?>
                        <div class="builder-field builder-field-wide">
                            <span>Uploaded Avatar URL</span>
                            <div class="job-review-value">
                                <img class="company-avatar-preview" src="<?= View::e($companyAvatarUrl) ?>" alt="Company avatar preview">
                                <span><?= View::e($companyAvatarUrl) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <label class="builder-field builder-field-wide">
                        <span>Company Description</span>
                        <textarea name="company_description" rows="5" placeholder="Briefly describe the company, team, product, or workplace culture." required><?= View::e($value('company_description')) ?></textarea>
                    </label>
                </div>
            </section>

            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>location_on</span>
                    <h2>Structured Job Location</h2>
                </div>

                <div class="builder-form-grid builder-form-grid-three">
                    <label class="builder-field">
                        <span>Country</span>
                        <select name="country_id" data-location-country required>
                            <option value="">Select country</option>
                            <?php foreach (($countries ?? []) as $country): ?>
                                <option value="<?= (int) $country['id'] ?>" <?= $selected('country_id', $country['id']) ?>><?= View::e($country['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>City / Province</span>
                        <select name="city_id" data-location-city required>
                            <option value="">Select city/province</option>
                            <?php foreach (($cities ?? []) as $city): ?>
                                <option value="<?= (int) $city['id'] ?>" data-country-id="<?= (int) $city['country_id'] ?>" <?= $selected('city_id', $city['id']) ?>><?= View::e($city['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>District</span>
                        <select name="district_id" data-location-district>
                            <option value="">Select district</option>
                            <?php foreach (($districts ?? []) as $district): ?>
                                <option value="<?= (int) $district['id'] ?>" data-city-id="<?= (int) $district['city_id'] ?>" <?= $selected('district_id', $district['id']) ?>><?= View::e($district['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Work Arrangement</span>
                        <select name="work_arrangement_id" required>
                            <option value="">Select arrangement</option>
                            <?php foreach (($workArrangements ?? []) as $workArrangement): ?>
                                <option value="<?= (int) $workArrangement['id'] ?>" <?= $selected('work_arrangement_id', $workArrangement['id']) ?>><?= View::e($workArrangement['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Salary Range</span>
                        <select name="salary_range_id" required>
                            <option value="">Select salary range</option>
                            <?php foreach (($salaryRanges ?? []) as $salaryRange): ?>
                                <option value="<?= (int) $salaryRange['id'] ?>" <?= $selected('salary_range_id', $salaryRange['id']) ?>><?= View::e($salaryRange['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Salary Type</span>
                        <select name="salary_type_id" required>
                            <option value="">Select salary type</option>
                            <?php foreach (($salaryTypes ?? []) as $salaryType): ?>
                                <option value="<?= (int) $salaryType['id'] ?>" <?= $selected('salary_type_id', $salaryType['id']) ?>><?= View::e($salaryType['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field builder-field-wide">
                        <span>Benefits</span>
                        <textarea name="benefits" rows="6" placeholder="Describe benefits, insurance, bonuses, equipment, or flexible working policies."><?= View::e($value('benefits')) ?></textarea>
                    </label>
                </div>
            </section>

            <div class="builder-form-actions">
                <a class="builder-ghost-button" href="<?= View::url('/employer/jobs/create/basics') ?>">
                    <span>arrow_back</span>
                    Back to Step 1
                </a>

                <div>
                    <button class="builder-secondary-button" type="submit">Save Progress</button>
                    <button class="builder-primary-button" type="submit" name="next_step" value="requirements">
                        Next Step
                        <span>arrow_forward</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
