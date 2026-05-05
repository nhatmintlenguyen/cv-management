<?php

use App\Core\View;

$activeSiteTab = 'my-jobs';
$jobs = $jobs ?? [];
?>
<?php require dirname(__DIR__, 2) . '/partials/site-topbar.php'; ?>

<main class="builder-page">
    <section class="builder-heading">
        <div class="builder-heading-row">
            <div>
                <h1>Job Vacancy Management</h1>
                <p>Manage your job postings, continue drafts, and prepare new vacancy listings.</p>
            </div>

            <a class="builder-primary-button" href="<?= View::url('/employer/jobs/create') ?>">
                Post Job
                <span>add</span>
            </a>
        </div>
    </section>

    <section class="builder-form-card">
        <div class="builder-section-title">
            <span>work</span>
            <h2>Your Job Postings</h2>
        </div>

        <?php if ($jobs === []): ?>
            <p class="builder-helper-text">No job vacancies have been created yet. Start with the Post Job flow.</p>
        <?php else: ?>
            <div class="admin-reference-table-wrap">
                <table class="admin-reference-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?= View::e($job['job_title']) ?></td>
                                <td><?= View::e($job['job_category']) ?></td>
                                <td><?= View::e(ucfirst((string) $job['status'])) ?></td>
                                <td><?= View::e($job['updated_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
