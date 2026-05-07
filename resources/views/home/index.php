<?php

use App\Core\View;

$activeSiteTab = 'home';
$user = $_SESSION['user'] ?? null;
$isJobSeeker = ($user['role'] ?? null) === 'job_seeker';
$cvActionUrl = $isJobSeeker ? '/cv/templates' : ($user === null ? '/register?redirect=' . rawurlencode('/cv/templates') : null);
$chipItems = [
    ['label' => '+ Add Education', 'url' => '/cv/edit/academic'],
    ['label' => '+ Add Experience', 'url' => '/cv/edit/academic'],
    ['label' => '+ Add Certificate', 'url' => '/cv/edit/qualifications'],
];
?>
<?php require dirname(__DIR__) . '/partials/site-topbar.php'; ?>

<main class="marketing-home">
    <section class="marketing-hero">
        <div class="marketing-shell marketing-hero-grid">
            <div class="marketing-hero-copy">
                <span class="marketing-pill">Smart Resume Builder</span>
                <h1>
                    Build a Professional CV
                    <span>In Just a Few Steps.</span>
                </h1>
                <p>
                    Stop fighting with complicated design software. Enter your details into our simple step-by-step forms,
                    choose a premium layout, and let OneCV generate a polished resume instantly.
                </p>
                <div class="marketing-actions">
                    <?php if ($cvActionUrl !== null): ?>
                        <a class="marketing-primary-action" href="<?= View::url($cvActionUrl) ?>">
                            Create Your CV Now
                            <span>arrow_forward</span>
                        </a>
                    <?php else: ?>
                        <span class="marketing-primary-action is-disabled" aria-disabled="true" title="CV Builder is available for Job Seeker accounts only.">
                            Create Your CV Now
                            <span>lock</span>
                        </span>
                    <?php endif; ?>
                    <a class="marketing-secondary-action" href="<?= View::url('/cv/templates') ?>">View Templates</a>
                </div>
            </div>

            <div class="marketing-hero-media">
                <img
                    src="https://www.topcv.vn/cv/snapshot/template-cv/mau-cv-an-tuong-6-_VlJVBQcNCQgBBVdWAgMCVQFTUAYPVgBUCVNRAQb0f5.webp?t=1756435041&color=574040&template_name=impressive_6_v2&lang=vi"
                    alt="Resume builder interface"
                >
                <div class="marketing-floating-card">
                    <div>
                        <span>auto_awesome</span>
                        <strong>Auto-Formatted</strong>
                    </div>
                    <p>Perfect spacing and typography applied automatically to every section.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="marketing-organized" id="features">
        <div class="marketing-shell">
            <div class="marketing-section-heading">
                <h2>Your Career, Effortlessly Organized</h2>
                <p>We focus on your data, so you do not have to focus on the design. Dynamic forms make updating your history simple.</p>
            </div>

            <div class="marketing-feature-grid">
                <article class="marketing-large-card">
                    <div>
                        <span class="marketing-feature-icon">checklist</span>
                        <h3>Step-by-Step Forms</h3>
                        <p>Easily add degrees, work experiences, certificates, and your strongest skills using focused form sections.</p>
                    </div>
                    <div class="marketing-chip-row">
                        <?php foreach ($chipItems as $item): ?>
                            <?php if ($user === null): ?>
                                <a href="<?= View::url('/login?redirect=' . rawurlencode($item['url'])) ?>"><?= View::e($item['label']) ?></a>
                            <?php elseif ($isJobSeeker): ?>
                                <a href="<?= View::url($item['url']) ?>"><?= View::e($item['label']) ?></a>
                            <?php else: ?>
                                <span><?= View::e($item['label']) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="marketing-dark-card">
                    <div>
                        <h3>Focus on Content</h3>
                        <p>Our standardized database keeps industries, majors, skills, and profile details structured consistently.</p>
                    </div>
                    <span class="marketing-dark-icon">dataset</span>
                </article>
            </div>
        </div>
    </section>

    <section class="marketing-templates">
        <div class="marketing-shell marketing-template-grid">
            <div class="marketing-template-preview">
                <img
                    src="https://www.topcv.vn/cv/snapshot/template-cv/mau-cv-hien-dai-1-_VAFaBAAJVw0CAQ4CUQECV1QABQcCAgkCVgNVCw391b.webp?t=1750134891&color=a94a4b&template_name=modern_1_v2&lang=vi"
                    alt="CV template preview"
                >
                <div class="marketing-layout-badge">
                    <strong>1-Click</strong>
                    <span>Layout Switch</span>
                </div>
            </div>

            <div class="marketing-template-copy">
                <span class="marketing-kicker">Template Library</span>
                <h2>Three Distinct Styles. One Powerful Resume.</h2>
                <p>
                    Input your information once and switch between multiple layout architectures. Pick the style that fits your industry.
                </p>

                <div class="marketing-template-list">
                    <article>
                        <span>dashboard_customize</span>
                        <div>
                            <h3>Modern Layout</h3>
                            <p>A clean, asymmetrical design with bold typography for software, marketing, and creative roles.</p>
                        </div>
                    </article>
                    <article>
                        <span>article</span>
                        <div>
                            <h3>Classic Layout</h3>
                            <p>Elegant, structured, and familiar for finance, accounting, and traditional corporate environments.</p>
                        </div>
                    </article>
                    <article>
                        <span>view_agenda</span>
                        <div>
                            <h3>Professional Layout</h3>
                            <p>A balanced layout with generous whitespace for work history and technical expertise.</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="marketing-stats">
        <div class="marketing-shell marketing-stats-grid">
            <div>
                <strong>3</strong>
                <span>Premium Layouts</span>
            </div>
            <div>
                <strong>10</strong>
                <span>Minutes to Complete</span>
            </div>
            <div>
                <strong>100%</strong>
                <span>Free for Seekers</span>
            </div>
        </div>
    </section>

    <section class="marketing-cta">
        <div class="marketing-cta-card">
            <h2>Ready to Land Your Next Role?</h2>
            <p>Fill in your details, pick your template, and export a beautiful CV today.</p>
            <?php if ($cvActionUrl !== null): ?>
                <a class="marketing-primary-action" href="<?= View::url($cvActionUrl) ?>">Start Building Now</a>
            <?php else: ?>
                <span class="marketing-primary-action is-disabled" aria-disabled="true" title="CV Builder is available for Job Seeker accounts only.">Start Building Now</span>
            <?php endif; ?>
        </div>
    </section>
</main>
