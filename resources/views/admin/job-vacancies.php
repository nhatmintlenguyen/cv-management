<?php

use App\Core\View;

$jobs = $jobs ?? [];
$stats = $stats ?? ['total' => 0, 'active' => 0, 'inactive' => 0, 'suspicious' => 0, 'openings' => 0];
$statusClass = static fn (string $status): string => match ($status) {
    'active' => 'is-active',
    'suspicious' => 'is-suspicious',
    default => 'is-inactive',
};
$formatDate = static function (?string $value): string {
    if ($value === null || $value === '') {
        return 'Recently updated';
    }

    $timestamp = strtotime($value);

    return $timestamp === false ? $value : date('M d, Y', $timestamp);
};
?>
<main class="admin-shell">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <section class="admin-main">
        <?php require __DIR__ . '/partials/topbar.php'; ?>

        <section class="admin-page-heading">
            <p class="eyebrow">Employer Content Moderation</p>
            <h1>Job Vacancies</h1>
            <p>View published job postings, monitor vacancy volume, and remove inappropriate or invalid postings.</p>
        </section>

        <section class="metric-grid admin-job-metrics">
            <article class="metric-card">
                <span>Total Vacancies</span>
                <strong><?= View::e($stats['total']) ?></strong>
                <small>All employer-created job postings</small>
            </article>
            <article class="metric-card">
                <span>Active</span>
                <strong><?= View::e($stats['active']) ?></strong>
                <small>Visible to job seekers</small>
            </article>
            <article class="metric-card">
                <span>Inactive</span>
                <strong><?= View::e($stats['inactive']) ?></strong>
                <small>Hidden or paused postings</small>
            </article>
            <article class="metric-card">
                <span>Suspicious</span>
                <strong><?= View::e($stats['suspicious']) ?></strong>
                <small>Flagged for admin review</small>
            </article>
            <article class="metric-card">
                <span>Total Openings</span>
                <strong><?= View::e($stats['openings']) ?></strong>
                <small>Combined number of seats</small>
            </article>
        </section>

        <section class="admin-table-card">
            <div class="table-heading">
                <div>
                    <h3>All Job Postings</h3>
                    <p><?= count($jobs) ?> posting<?= count($jobs) === 1 ? '' : 's' ?> found</p>
                </div>
            </div>

            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Company</th>
                            <th>Employer</th>
                            <th>Location</th>
                            <th>Skills</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($jobs === []): ?>
                            <tr>
                                <td colspan="8" class="empty-cell">No job vacancies found.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($jobs as $job): ?>
                            <tr class="admin-clickable-row" data-row-href="<?= View::url('/jobs/show?id=' . (int) $job['id']) ?>">
                                <td>
                                    <strong><?= View::e($job['job_title']) ?></strong><br>
                                    <small><?= View::e($job['job_category']) ?> · <?= (int) $job['number_of_openings'] ?> opening<?= (int) $job['number_of_openings'] === 1 ? '' : 's' ?></small>
                                </td>
                                <td><?= View::e($job['company_name']) ?></td>
                                <td>
                                    <?= View::e($job['employer_name']) ?><br>
                                    <small><?= View::e($job['employer_email']) ?></small>
                                </td>
                                <td><?= View::e($job['city_name'] . ', ' . $job['country_name']) ?></td>
                                <td><?= (int) $job['required_skill_count'] ?></td>
                                <td>
                                    <span class="admin-status-pill <?= $statusClass((string) ($job['status'] ?? '')) ?>">
                                        <?= View::e(ucfirst((string) $job['status'])) ?>
                                    </span>
                                </td>
                                <td><?= View::e($formatDate($job['updated_at'] ?? null)) ?></td>
                                <td class="text-right" data-no-row-nav>
                                    <button
                                        class="table-icon-button js-open-job-status-modal"
                                        type="button"
                                        data-id="<?= (int) $job['id'] ?>"
                                        data-title="<?= View::e($job['job_title']) ?>"
                                        data-company="<?= View::e($job['company_name']) ?>"
                                        data-status="<?= View::e((string) $job['status']) ?>"
                                    >
                                        Update Status
                                    </button>
                                    <form method="post" action="<?= View::url('/admin/job-vacancies/delete') ?>" class="inline-form" onsubmit="return confirm('Remove this job vacancy?');">
                                        <input type="hidden" name="id" value="<?= (int) $job['id'] ?>">
                                        <button class="table-icon-button danger" type="submit">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="reference-modal" id="job-status-modal" hidden>
            <div class="reference-modal-backdrop js-close-job-status-modal"></div>
            <section class="reference-modal-panel" role="dialog" aria-modal="true" aria-labelledby="job-status-modal-title">
                <div class="reference-modal-heading">
                    <div>
                        <p class="eyebrow">Moderation</p>
                        <h2 id="job-status-modal-title">Update Job Status</h2>
                        <p id="job-status-modal-summary"></p>
                    </div>
                    <button class="modal-close-button js-close-job-status-modal" type="button">Close</button>
                </div>

                <form class="reference-form reference-modal-form" method="post" action="<?= View::url('/admin/job-vacancies/status') ?>">
                    <input type="hidden" name="id" id="job-status-modal-id">

                    <label>
                        <span>Status</span>
                        <select name="status" id="job-status-modal-status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspicious">Suspicious</option>
                        </select>
                    </label>

                    <div class="reference-modal-actions">
                        <button class="secondary-button js-close-job-status-modal" type="button">Cancel</button>
                        <button class="primary-button" type="submit">Update Status</button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</main>
