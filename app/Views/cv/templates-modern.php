<?php

use App\Core\View;

$address = trim(($mockCv['street_address'] ?? '') . ', ' . ($mockCv['district'] ?? '') . ', ' . ($mockCv['city'] ?? '') . ', ' . ($mockCv['country'] ?? ''));
?>
<article class="cv-output cv-output-modern">
    <aside class="cv-modern-sidebar">
        <img class="cv-avatar" src="<?= View::e($mockCv['avatar'] ?? '') ?>" alt="<?= View::e($mockCv['full_name'] ?? 'Candidate') ?> portrait">
        <h2><?= View::e($mockCv['full_name'] ?? '') ?></h2>
        <p class="cv-headline"><?= View::e($mockCv['headline'] ?? '') ?></p>

        <section>
            <h3>Contact</h3>
            <p><span>call</span><?= View::e($mockCv['phone_number'] ?? '') ?></p>
            <p><span>cake</span><?= View::e($mockCv['date_of_birth'] ?? '') ?></p>
            <p><span>wc</span><?= View::e($mockCv['gender'] ?? '') ?></p>
            <p><span>mail</span><?= View::e($mockCv['email'] ?? '') ?></p>
            <p><span>location_on</span><?= View::e($address) ?></p>
            <p><span>markunread_mailbox</span><?= View::e($mockCv['postal_code'] ?? '') ?></p>
        </section>

        <section>
            <h3>Education</h3>
            <?php foreach (($mockCv['educations'] ?? []) as $education): ?>
                <div class="cv-compact-item">
                    <strong><?= View::e($education['degree_level']) ?> in <?= View::e($education['major']) ?></strong>
                    <span><?= View::e($education['start_year']) ?> - <?= View::e($education['end_year']) ?></span>
                    <p><?= View::e($education['institution']) ?></p>
                    <small><?= View::e($education['description']) ?></small>
                </div>
            <?php endforeach; ?>
        </section>

        <section>
            <h3>Skills</h3>
            <?php foreach (($mockCv['skills'] ?? []) as $skill): ?>
                <div class="cv-skill-meter">
                    <span><?= View::e($skill['skill']) ?> · <?= View::e($skill['proficiency']) ?></span>
                    <i style="--level: <?= (int) $skill['level'] ?>0%;"></i>
                </div>
            <?php endforeach; ?>
        </section>
    </aside>

    <main class="cv-modern-main">
        <section>
            <h3>Professional Summary</h3>
            <p><?= View::e($mockCv['summary'] ?? '') ?></p>
            <div class="cv-meta-strip">
                <span>Category: <?= View::e($mockCv['category'] ?? '') ?></span>
            </div>
        </section>

        <section>
            <h3>Work Experience</h3>
            <?php foreach (($mockCv['work_histories'] ?? []) as $work): ?>
                <div class="cv-timeline-item">
                    <div>
                        <strong><?= View::e($work['job_title']) ?></strong>
                        <span><?= View::e($work['start_year']) ?> - <?= View::e($work['is_current'] ? 'Present' : $work['end_year']) ?></span>
                    </div>
                    <p class="cv-company"><?= View::e($work['company_name']) ?> · <?= View::e($work['employment_type']) ?> · <?= View::e($work['industry']) ?></p>
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

        <section>
            <h3>Certificates</h3>
            <?php foreach (($mockCv['certificates'] ?? []) as $certificate): ?>
                <div class="cv-certificate-row">
                    <strong><?= View::e($certificate['year_issued']) ?></strong>
                    <p>
                        <?= View::e($certificate['certificate_name']) ?> · <?= View::e($certificate['issuing_organization']) ?>
                        <span><?= View::e($certificate['description']) ?></span>
                    </p>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
</article>
