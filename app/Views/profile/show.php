<?php

use App\Core\View;

$activeSiteTab = 'profile';
$old = $_SESSION['_old'] ?? [];
$cv = $cv ?? null;
$countries = $countries ?? [];
$cities = $cities ?? [];
$fullName = $old['full_name'] ?? $user['full_name'] ?? '';
$email = $old['email'] ?? $user['email'] ?? '';
$initials = strtoupper(substr((string) $fullName, 0, 1));
$headline = $headline ?? 'Active Job Seeker';
$profileCompletion = $profileCompletion ?? 0;
$phoneNumber = $old['phone_number'] ?? $cv['phone_number'] ?? '';
$selectedCountryId = (string) ($old['country_id'] ?? $cv['country_id'] ?? '');
$selectedCityId = (string) ($old['city_id'] ?? $cv['city_id'] ?? '');
$streetAddress = $old['street_address'] ?? $cv['street_address'] ?? '';
$activeTemplate = $_SESSION['selected_cv_template'] ?? 'modern';
$cvPreviewName = str_replace(' ', '_', trim((string) $fullName) ?: 'OneCV_Profile') . '.pdf';
?>
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="profile-page">
    <form class="profile-dashboard js-profile-form is-readonly" method="post" action="<?= View::url('/profile') ?>" enctype="multipart/form-data">
        <section class="profile-hero-card" aria-label="Profile summary">
            <div class="profile-hero-media">
                <div class="profile-avatar-preview">
                    <?php if (! empty($avatarUrl)): ?>
                        <img class="js-profile-avatar-preview" src="<?= View::e($avatarUrl) ?>" alt="<?= View::e($fullName) ?> avatar">
                    <?php else: ?>
                        <span class="js-profile-avatar-initials"><?= View::e($initials) ?></span>
                    <?php endif; ?>
                </div>
                <input class="profile-avatar-input js-profile-avatar-input" id="profile-avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp,image/gif">
                <label class="profile-avatar-camera" for="profile-avatar" aria-label="Upload avatar">
                    <span>photo_camera</span>
                </label>
                <button class="profile-pencil-button js-profile-edit-toggle" type="button" aria-label="Edit profile information">
                    <span>edit</span>
                </button>
            </div>

            <div class="profile-hero-copy">
                <label class="profile-name-field">
                    <span>Full Name</span>
                    <input class="js-profile-editable" type="text" name="full_name" value="<?= View::e($fullName) ?>" required readonly>
                </label>
                <p><?= View::e($headline) ?></p>
                <div class="profile-badges" aria-label="Profile badges">
                    <span><span>verified</span> Executive Tier</span>
                    <span>Active Job Seeker</span>
                </div>
            </div>

            <button class="profile-save-button js-profile-save-button" type="submit" hidden>
                Save Profile
            </button>
        </section>

        <section class="profile-content-grid">
            <article class="profile-details-card">
                <div class="profile-card-title">
                    <span>person</span>
                    <h1>Personal Details</h1>
                </div>

                <div class="profile-detail-grid">
                    <label class="profile-detail-field">
                        <span>Email Address</span>
                        <input class="js-profile-editable" type="email" name="email" value="<?= View::e($email) ?>" required readonly>
                    </label>

                    <label class="profile-detail-field">
                        <span>Phone Number</span>
                        <input class="js-profile-editable" type="tel" name="phone_number" value="<?= View::e($phoneNumber) ?>" placeholder="Not added yet" readonly>
                    </label>

                    <label class="profile-detail-field">
                        <span>Country</span>
                        <select class="js-profile-editable" name="country_id" data-profile-country disabled>
                            <option value="">Not added yet</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= (int) $country['id'] ?>" <?= $selectedCountryId === (string) $country['id'] ? 'selected' : '' ?>>
                                    <?= View::e($country['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="profile-detail-field">
                        <span>City</span>
                        <select class="js-profile-editable" name="city_id" data-profile-city disabled>
                            <option value="">Not added yet</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?= (int) $city['id'] ?>" data-country-id="<?= (int) $city['country_id'] ?>" <?= $selectedCityId === (string) $city['id'] ? 'selected' : '' ?>>
                                    <?= View::e($city['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="profile-detail-field profile-detail-wide">
                        <span>Street Address</span>
                        <input class="js-profile-editable" type="text" name="street_address" value="<?= View::e($streetAddress) ?>" placeholder="Not added yet" readonly>
                    </label>
                </div>
            </article>

            <aside class="profile-active-cv-card" aria-label="Active CV">
                <div class="profile-card-title profile-card-title-inverse">
                    <span>description</span>
                    <h2>Active CV</h2>
                </div>

                <a class="profile-cv-preview" href="<?= View::url('/cv/edit') ?>">
                    <div class="profile-cv-paper">
                        <div class="profile-cv-side">
                            <?php if (! empty($avatarUrl)): ?>
                                <img src="<?= View::e($avatarUrl) ?>" alt="">
                            <?php else: ?>
                                <span><?= View::e($initials) ?></span>
                            <?php endif; ?>
                            <strong><?= View::e($fullName ?: 'OneCV User') ?></strong>
                            <small><?= View::e($headline) ?></small>
                        </div>
                        <div class="profile-cv-lines">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <div class="profile-cv-caption">
                        <strong><?= View::e($cvPreviewName) ?></strong>
                        <span><?= View::e(ucfirst((string) $activeTemplate)) ?> Template</span>
                    </div>
                </a>

                <div class="profile-completion">
                    <div>
                        <span>Profile Completion</span>
                        <strong><?= View::e((string) $profileCompletion) ?>%</strong>
                    </div>
                    <div class="profile-completion-bar" aria-hidden="true">
                        <span style="width: <?= View::e((string) max(0, min(100, (int) $profileCompletion))) ?>%"></span>
                    </div>
                </div>

                <a class="profile-cv-button" href="<?= View::url('/cv/edit') ?>">
                    Open CV Builder
                    <span>arrow_forward</span>
                </a>
            </aside>
        </section>
    </form>
</main>
