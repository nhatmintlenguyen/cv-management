<?php

use App\Core\View;

$address = trim(($mockCv['street_address'] ?? '') . ', ' . ($mockCv['district'] ?? '') . ', ' . ($mockCv['city'] ?? '') . ', ' . ($mockCv['country'] ?? ''));
?>
<article class="cv-output cv-output-minimal">
    <header class="cv-minimal-header">
        <h2><?= View::e($mockCv['full_name'] ?? '') ?></h2>
        <p><?= View::e($mockCv['headline'] ?? '') ?> · <?= View::e($mockCv['category'] ?? '') ?></p>
        <div>
            <span><i>call</i><?= View::e($mockCv['phone_number'] ?? '') ?></span>
            <span><i>mail</i><?= View::e($mockCv['email'] ?? '') ?></span>
            <span><i>cake</i><?= View::e($mockCv['date_of_birth'] ?? '') ?></span>
            <span><i>wc</i><?= View::e($mockCv['gender'] ?? '') ?></span>
            <span><i>location_on</i><?= View::e($address) ?></span>
            <span><i>markunread_mailbox</i><?= View::e($mockCv['postal_code'] ?? '') ?></span>
        </div>
    </header>

    <section class="cv-minimal-section">
        <h3>Professional Summary</h3>
        <p><?= View::e($mockCv['summary'] ?? '') ?></p>
    </section>

    <section class="cv-minimal-section">
        <h3>Education</h3>
        <?php foreach (($mockCv['educations'] ?? []) as $education): ?>
            <div class="cv-minimal-entry">
                <div>
                    <strong><?= View::e($education['institution']) ?></strong>
                    <span><?= View::e($education['start_year']) ?> - <?= View::e($education['end_year']) ?></span>
                </div>
                <p><b><?= View::e($education['degree_level']) ?> · <?= View::e($education['major']) ?></b></p>
                <p><?= View::e($education['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="cv-minimal-section">
        <h3>Work Experience</h3>
        <?php foreach (($mockCv['work_histories'] ?? []) as $work): ?>
            <div class="cv-minimal-entry">
                <div>
                    <strong><?= View::e($work['company_name']) ?></strong>
                    <span><?= View::e($work['start_year']) ?> - <?= View::e($work['is_current'] ? 'Present' : $work['end_year']) ?></span>
                </div>
                <p><b><?= View::e($work['job_title']) ?></b> · <?= View::e($work['employment_type']) ?> · <?= View::e($work['industry']) ?></p>
                <ul>
                    <?php foreach (explode('. ', $work['job_description']) as $sentence): ?>
                        <?php if (trim($sentence) !== ''): ?>
                            <li><?= View::e(rtrim($sentence, '.')) ?>.</li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="cv-minimal-section">
        <h3>Skills</h3>
        <div class="cv-minimal-skill-table">
            <?php foreach (($mockCv['skills'] ?? []) as $skill): ?>
                <p><strong><?= View::e($skill['skill']) ?></strong><span><?= View::e($skill['proficiency']) ?> · <?= (int) $skill['level'] ?>/10</span></p>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="cv-minimal-section">
        <h3>Certificates</h3>
        <?php foreach (($mockCv['certificates'] ?? []) as $certificate): ?>
            <div class="cv-minimal-entry">
                <div>
                    <strong><?= View::e($certificate['certificate_name']) ?></strong>
                    <span><?= View::e($certificate['year_issued']) ?></span>
                </div>
                <p><?= View::e($certificate['issuing_organization']) ?> · <?= View::e($certificate['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </section>
</article>
