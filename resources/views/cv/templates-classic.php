<?php

use App\Core\View;

$address = trim(($mockCv['street_address'] ?? '') . ', ' . ($mockCv['district'] ?? '') . ', ' . ($mockCv['city'] ?? '') . ', ' . ($mockCv['country'] ?? ''));
?>
<article class="cv-output cv-output-classic">
    <header class="cv-classic-header">
        <img class="cv-avatar" src="<?= View::e($mockCv['avatar'] ?? '') ?>" alt="<?= View::e($mockCv['full_name'] ?? 'Candidate') ?> portrait">
        <div>
            <div class="cv-classic-title-row">
                <h2><?= View::e($mockCv['full_name'] ?? '') ?></h2>
                <strong><?= View::e($mockCv['headline'] ?? '') ?></strong>
            </div>
            <div class="cv-classic-contact">
                <span><i>cake</i><?= View::e($mockCv['date_of_birth'] ?? '') ?></span>
                <span><i>wc</i><?= View::e($mockCv['gender'] ?? '') ?></span>
                <span><i>call</i><?= View::e($mockCv['phone_number'] ?? '') ?></span>
                <span><i>mail</i><?= View::e($mockCv['email'] ?? '') ?></span>
                <span><i>location_on</i><?= View::e($address) ?></span>
                <span><i>markunread_mailbox</i><?= View::e($mockCv['postal_code'] ?? '') ?></span>
                <span><i>work</i><?= View::e($mockCv['category'] ?? '') ?></span>
            </div>
        </div>
    </header>

    <section class="cv-classic-section">
        <h3>Professional Summary</h3>
        <p><?= View::e($mockCv['summary'] ?? '') ?></p>
    </section>

    <section class="cv-classic-section">
        <h3>Education</h3>
        <?php foreach (($mockCv['educations'] ?? []) as $education): ?>
            <div class="cv-classic-split">
                <div>
                    <strong><?= View::e($education['major']) ?></strong>
                    <span><?= View::e($education['start_year']) ?> - <?= View::e($education['end_year']) ?></span>
                </div>
                <div>
                    <strong><?= View::e($education['institution']) ?></strong>
                    <p><?= View::e($education['degree_level']) ?> · <?= View::e($education['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="cv-classic-section">
        <h3>Work Experience</h3>
        <?php foreach (($mockCv['work_histories'] ?? []) as $work): ?>
            <div class="cv-classic-split">
                <div>
                    <strong><?= View::e($work['job_title']) ?></strong>
                    <span><?= View::e($work['start_year']) ?> - <?= View::e($work['is_current'] ? 'Present' : $work['end_year']) ?></span>
                </div>
                <div>
                    <strong><?= View::e($work['company_name']) ?></strong>
                    <p><?= View::e($work['employment_type']) ?> · <?= View::e($work['industry']) ?></p>
                    <ul>
                        <?php foreach (explode('. ', $work['job_description']) as $sentence): ?>
                            <?php if (trim($sentence) !== ''): ?>
                                <li><?= View::e(rtrim($sentence, '.')) ?>.</li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="cv-classic-section cv-classic-columns">
        <div>
            <h3>Skills</h3>
            <?php foreach (($mockCv['skills'] ?? []) as $skill): ?>
                <p><strong><?= View::e($skill['skill']) ?></strong> · <?= View::e($skill['proficiency']) ?> (<?= (int) $skill['level'] ?>/10)</p>
            <?php endforeach; ?>
        </div>
        <div>
            <h3>Certificates</h3>
            <?php foreach (($mockCv['certificates'] ?? []) as $certificate): ?>
                <p><strong><?= View::e($certificate['year_issued']) ?> · <?= View::e($certificate['certificate_name']) ?></strong><br><?= View::e($certificate['issuing_organization']) ?> · <?= View::e($certificate['description']) ?></p>
            <?php endforeach; ?>
        </div>
    </section>
</article>
