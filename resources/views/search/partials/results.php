<?php

use App\Core\View;

$filters = $filters ?? [];
$candidates = $candidates ?? [];
$page = $page ?? 1;
$perPage = $perPage ?? 10;
$total = $total ?? 0;
$shown = min($total, $page * $perPage);
$queryString = $queryString ?? '';
$queryPrefix = $queryString === '' ? '?' : '?' . $queryString . '&';
?>
<header class="employer-results-header">
    <div>
        <h1>Candidate Discovery</h1>
        <p>Showing <?= (int) $shown ?> of <?= (int) $total ?> curated profiles matching your criteria</p>
    </div>
    <div class="employer-view-toggle" aria-hidden="true">
        <span class="active">grid_view</span>
        <span>view_list</span>
    </div>
</header>

<?php if ($candidates === []): ?>
    <section class="employer-empty-state">
        <span>manage_search</span>
        <h2>No matching CVs found</h2>
        <p>Try broadening your filters or removing some required skills.</p>
    </section>
<?php else: ?>
    <section class="employer-candidate-grid">
        <?php foreach ($candidates as $candidate): ?>
            <a class="employer-candidate-card" href="<?= View::url('/find-cvs/show?id=' . (int) $candidate['id']) ?>">
                <div class="employer-candidate-topline">
                    <div class="employer-candidate-avatar">
                        <?php if (! empty($candidate['avatar'])): ?>
                            <img src="<?= View::e($candidate['avatar']) ?>" alt="<?= View::e($candidate['full_name']) ?> avatar">
                        <?php else: ?>
                            <?= View::e($candidate['initials']) ?>
                        <?php endif; ?>
                    </div>
                    <span>bookmark</span>
                </div>

                <h2><?= View::e($candidate['full_name']) ?></h2>
                <strong><?= View::e($candidate['headline']) ?></strong>
                <p><?= View::e($candidate['summary'] ?: 'No professional summary has been added yet.') ?></p>

                <div class="employer-card-meta">
                    <span><?= View::e($candidate['city']) ?></span>
                    <span><?= (int) $candidate['experience_years'] ?> yrs exp</span>
                </div>

                <div class="employer-card-skills">
                    <?php foreach ($candidate['skills'] as $skill): ?>
                        <?php if ($skill !== ''): ?>
                            <span><?= View::e($skill) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </section>

    <?php if ($shown < $total): ?>
        <div class="employer-load-more">
            <a data-ajax-search-page href="<?= View::url('/find-cvs' . $queryPrefix . 'page=' . ((int) $page + 1)) ?>">
                Load More Candidates
                <span>expand_more</span>
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>
