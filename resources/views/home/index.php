<?php

use App\Core\View;

$activeSiteTab = 'home';
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
                    <a class="marketing-primary-action" href="<?= View::url('/cv/templates') ?>">
                        Create Your CV Now
                        <span>arrow_forward</span>
                    </a>
                    <a class="marketing-secondary-action" href="<?= View::url('/cv/templates') ?>">View Templates</a>
                </div>
            </div>

            <div class="marketing-hero-media">
                <img
                    src="https://lh3.googleusercontent.com/aida/ADBb0ugqKc4mruksAhejJ2QgPhxgYKkyR1zoQ3xvDRB3bpxkbp1_DEeFR9ymx_agarQbiyUQzjqdCBEVuO0ei4ih8HAtSD8zy9JmmGqdF0GsoXOip7UYwdoi7r2qylC_ou65LJSfZ835aqR1Paf7hgye_RTPzsLVvkc3D8-5tXI7zcz4-wzzctwdA8oEvsAE5f7sEZTRA8xjTFeYKcH54ytdiAX0-eoL4Igyk44MySuQa9sMaxI3X2MILgpoqAS7v-CXf293B9TIVYRgeQ"
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
                        <span>+ Add Education</span>
                        <span>+ Add Experience</span>
                        <span>+ Add Certificate</span>
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
                    src="https://lh3.googleusercontent.com/aida/ADBb0uik-4A3N7LC9XMHDDhvV729n8TKTEASYd_F0hFBgr07V3dZhUyB_pEHUlqfask3nMxHpqzFwqKYw-Xbu_sJ9-dggEKNnl-sxCzqLj5xHt2RyA6uOALOCyUhLBdTePkJI3_bCG6mBUG-r4IglP8kcFv9_wfBrHPH0vKU1ld8qYtOr8gWYMYhiwYE5SdKt54yHrudXvvZJKtw_hWA9LKcU0YXUT67RBQIPBgWTqsaPgQBkKa3uvlNSJB8KpIUVgUqzCFwFUuSTGH3S-s"
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
            <a class="marketing-primary-action" href="<?= View::url('/cv/templates') ?>">Start Building Now</a>
        </div>
    </section>
</main>
