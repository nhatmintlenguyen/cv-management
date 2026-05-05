<?php

use App\Core\View;

$activeJobSeekerTab = 'builder';
$genders = $genders ?? [];
$countries = $countries ?? [];
$cities = $cities ?? [];
$districts = $districts ?? [];
$categories = $categories ?? [];
$cv = $cv ?? [];
$old = $_SESSION['_old'] ?? [];
$fieldValue = static fn (string $field, mixed $default = ''): string => (string) ($old[$field] ?? $cv[$field] ?? $default);
$isSelected = static fn (string $field, mixed $value): string => (string) ($old[$field] ?? $cv[$field] ?? '') === (string) $value ? 'selected' : '';
?>
<?php require dirname(__DIR__) . '/job-seeker/partials/topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-breadcrumb">
            <span>CV Builder</span>
            <span class="builder-symbol">chevron_right</span>
            <strong>Personal Information</strong>
        </div>

        <div class="builder-heading-row">
            <div>
                <h1>Step 1: Identity &amp; Reach</h1>
                <p>Provide your personal details, contact information, location, CV category, and professional summary.</p>
            </div>

            <div class="builder-progress-card">
                <span>Current Progress</span>
                <strong>Identity &amp; Reach</strong>
            </div>
        </div>
    </section>

    <div class="builder-shell">
        <aside class="builder-stepper" aria-label="CV builder progress">
            <div class="builder-step active">
                <span>1</span>
                <div>
                    <strong>Personal Info</strong>
                    <small>In Progress</small>
                </div>
            </div>
            <div class="builder-step">
                <span>2</span>
                <div>
                    <strong>Education &amp; Experience</strong>
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

        <form class="builder-form-card js-builder-identity-form" method="post" action="<?= View::url('/cv/identity') ?>">
            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>account_circle</span>
                    <h2>Core Details</h2>
                </div>

                <div class="builder-form-grid">
                    <label class="builder-field builder-field-wide">
                        <span>Full Name</span>
                        <input type="text" name="full_name" value="<?= View::e($fieldValue('full_name')) ?>" placeholder="e.g. Dang Ngoc Linh" required>
                    </label>

                    <label class="builder-field">
                        <span>Date of Birth</span>
                        <input type="date" name="date_of_birth" value="<?= View::e($fieldValue('date_of_birth')) ?>" required>
                    </label>

                    <label class="builder-field">
                        <span>Gender</span>
                        <select name="gender_id" required>
                            <option value="">Select gender</option>
                            <?php foreach ($genders as $gender): ?>
                                <option value="<?= (int) $gender['id'] ?>" <?= $isSelected('gender_id', $gender['id']) ?>><?= View::e($gender['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>Email Address</span>
                        <input type="email" name="email" value="<?= View::e($fieldValue('email')) ?>" placeholder="linh@example.com" required>
                    </label>

                    <label class="builder-field">
                        <span>Phone Number</span>
                        <input type="tel" name="phone_number" value="<?= View::e($fieldValue('phone_number')) ?>" placeholder="+84 901 234 567" required>
                    </label>
                </div>
            </section>

            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>location_on</span>
                    <h2>Structured Address</h2>
                </div>

                <div class="builder-form-grid builder-form-grid-three">
                    <label class="builder-field builder-field-two">
                        <span>Country</span>
                        <select name="country_id" data-location-country required>
                            <option value="">Select country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= (int) $country['id'] ?>" <?= $isSelected('country_id', $country['id']) ?>><?= View::e($country['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>City / Province</span>
                        <select name="city_id" data-location-city required>
                            <option value="">Select city/province</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?= (int) $city['id'] ?>" data-country-id="<?= (int) $city['country_id'] ?>" <?= $isSelected('city_id', $city['id']) ?>><?= View::e($city['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field">
                        <span>District</span>
                        <select name="district_id" data-location-district>
                            <option value="">Select district</option>
                            <?php foreach ($districts as $district): ?>
                                <option value="<?= (int) $district['id'] ?>" data-city-id="<?= (int) $district['city_id'] ?>" <?= $isSelected('district_id', $district['id']) ?>><?= View::e($district['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field builder-field-two">
                        <span>Street Address</span>
                        <input type="text" name="street_address" value="<?= View::e($fieldValue('street_address')) ?>" placeholder="e.g. 12 Nguyen Hue Street" required>
                    </label>

                    <label class="builder-field">
                        <span>Postal Code</span>
                        <input type="text" name="postal_code" value="<?= View::e($fieldValue('postal_code')) ?>" placeholder="e.g. 700000">
                    </label>
                </div>
            </section>

            <section class="builder-form-section">
                <div class="builder-section-title">
                    <span>work</span>
                    <h2>CV Direction</h2>
                </div>

                <div class="builder-form-grid">
                    <label class="builder-field builder-field-wide">
                        <span>CV Category</span>
                        <select name="cv_category_id" required>
                            <option value="">Select CV category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= $isSelected('cv_category_id', $category['id']) ?>><?= View::e($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="builder-field builder-field-wide">
                        <span>Professional Summary</span>
                        <textarea name="summary" rows="6" placeholder="Write a concise career objective or personal introduction."><?= View::e($fieldValue('summary')) ?></textarea>
                    </label>
                </div>
            </section>

            <div class="builder-form-actions">
                <button class="builder-ghost-button" type="button">
                    <span>close</span>
                    Cancel Draft
                </button>

                <div>
                    <button class="builder-secondary-button" type="submit">Save Progress</button>
                    <button class="builder-primary-button" type="submit" name="next_step" value="academic">
                        Next Step
                        <span>arrow_forward</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
