<?php

use App\Core\View;

$pagination = $pagination ?? [
    'page' => 1,
    'total_pages' => 1,
    'total' => count($rows ?? []),
    'from' => 0,
    'to' => count($rows ?? []),
];
$fields = $selectedConfig['fields'] ?? ['name' => 'Name'];
$fieldOptions = $fieldOptions ?? [];
$isLocked = ! empty($selectedConfig['locked']);

$renderField = function (string $field, string $label, array $row = [], ?string $formId = null) use ($fieldOptions): void {
    $value = $row[$field] ?? '';
    $formAttribute = $formId === null ? '' : ' form="' . View::e($formId) . '"';

    if (isset($fieldOptions[$field])) {
        echo '<select name="' . View::e($field) . '"' . $formAttribute . ' required>';
        echo '<option value="">Select ' . View::e($label) . '</option>';

        foreach ($fieldOptions[$field] as $option) {
            $selected = (string) ($option['id'] ?? '') === (string) $value ? ' selected' : '';
            echo '<option value="' . View::e($option['id'] ?? '') . '"' . $selected . '>' . View::e($option['name'] ?? '') . '</option>';
        }

        echo '</select>';
        return;
    }

    $type = in_array($field, ['sort_order', 'level_value'], true) ? 'number' : 'text';
    $min = $field === 'level_value' ? ' min="1" max="10"' : ($field === 'sort_order' ? ' min="0"' : '');

    echo '<input type="' . $type . '" name="' . View::e($field) . '" value="' . View::e($value) . '" placeholder="' . View::e($label) . '"' . $min . $formAttribute . ' required>';
};

$fieldForColumn = function (string $column) use ($fields): ?string {
    if (array_key_exists($column, $fields)) {
        return $column;
    }

    return match ($column) {
        'country_name' => array_key_exists('country_id', $fields) ? 'country_id' : null,
        'city_name' => array_key_exists('city_id', $fields) ? 'city_id' : null,
        default => null,
    };
};
?>
<main class="admin-shell">
    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <section class="admin-main">
        <?php require __DIR__ . '/partials/topbar.php'; ?>

        <section class="admin-page-heading">
            <p class="eyebrow">System Configuration</p>
            <h1>Reference Data</h1>
            <p>Manage standard lookup values used by CV forms and employer search filters.</p>
        </section>

        <section class="reference-tabs">
            <?php foreach ($referenceTypes as $type => $config): ?>
                <a
                    class="<?= $selectedType === $type ? 'active' : '' ?>"
                    href="<?= View::url('/admin/reference?type=' . $type . '&page=1') ?>"
                >
                    <span><?= View::e($config['icon']) ?></span>
                    <?= View::e($config['label']) ?>
                </a>
            <?php endforeach; ?>
        </section>

        <section class="admin-table-card">
            <div class="table-heading">
                <div>
                    <h3><?= View::e($selectedConfig['label']) ?></h3>
                    <p>
                        Showing <?= View::e($pagination['from']) ?>-<?= View::e($pagination['to']) ?>
                        of <?= View::e($pagination['total']) ?> entries
                    </p>
                </div>
            </div>

            <?php if ($isLocked): ?>
                <div class="reference-locked-note">
                    This reference type is locked because it defines system-level ordering and validation rules.
                </div>
            <?php else: ?>
                <form class="reference-form" method="post" action="<?= View::url('/admin/reference/store') ?>">
                    <input type="hidden" name="type" value="<?= View::e($selectedType) ?>">
                    <input type="hidden" name="page" value="<?= View::e($pagination['page']) ?>">

                    <?php foreach ($fields as $field => $label): ?>
                        <label>
                            <span><?= View::e($label) ?></span>
                            <?php $renderField($field, $label); ?>
                        </label>
                    <?php endforeach; ?>

                    <button class="admin-action-button" type="submit">Add Entry</button>
                </form>
            <?php endif; ?>

            <div class="table-scroll">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <?php foreach ($selectedConfig['columns'] as $label): ?>
                                <th><?= View::e($label) ?></th>
                            <?php endforeach; ?>
                            <?php if (! $isLocked): ?>
                                <th class="text-right">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows === []): ?>
                            <tr>
                                <td colspan="<?= count($selectedConfig['columns']) + ($isLocked ? 0 : 1) ?>" class="empty-cell">No entries found.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($selectedConfig['columns'] as $column => $label): ?>
                                    <td>
                                        <?= View::e($row[$column] ?? '') ?>
                                    </td>
                                <?php endforeach; ?>

                                <?php if (! $isLocked): ?>
                                    <td class="text-right">
                                        <button
                                            class="table-icon-button js-open-reference-modal"
                                            type="button"
                                            data-id="<?= View::e($row['id']) ?>"
                                            <?php foreach ($fields as $field => $label): ?>
                                                data-<?= View::e(str_replace('_', '-', $field)) ?>="<?= View::e($row[$field] ?? '') ?>"
                                            <?php endforeach; ?>
                                        >
                                            Edit
                                        </button>

                                        <form method="post" action="<?= View::url('/admin/reference/delete') ?>" class="inline-form" onsubmit="return confirm('Delete this entry?');">
                                            <input type="hidden" name="type" value="<?= View::e($selectedType) ?>">
                                            <input type="hidden" name="page" value="<?= View::e($pagination['page']) ?>">
                                            <input type="hidden" name="id" value="<?= View::e($row['id']) ?>">
                                            <button class="table-icon-button danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-bar">
                <span>
                    Page <?= View::e($pagination['page']) ?> of <?= View::e($pagination['total_pages']) ?>
                </span>

                <nav class="pagination-links" aria-label="Reference data pagination">
                    <?php if ($pagination['page'] > 1): ?>
                        <a href="<?= View::url('/admin/reference?type=' . $selectedType . '&page=' . ($pagination['page'] - 1)) ?>">Previous</a>
                    <?php else: ?>
                        <span class="disabled">Previous</span>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $pagination['page'] - 2);
                    $endPage = min($pagination['total_pages'], $pagination['page'] + 2);
                    ?>

                    <?php if ($startPage > 1): ?>
                        <a href="<?= View::url('/admin/reference?type=' . $selectedType . '&page=1') ?>">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="ellipsis">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
                        <?php if ($page === $pagination['page']): ?>
                            <span class="active"><?= View::e($page) ?></span>
                        <?php else: ?>
                            <a href="<?= View::url('/admin/reference?type=' . $selectedType . '&page=' . $page) ?>"><?= View::e($page) ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($endPage < $pagination['total_pages']): ?>
                        <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                            <span class="ellipsis">...</span>
                        <?php endif; ?>
                        <a href="<?= View::url('/admin/reference?type=' . $selectedType . '&page=' . $pagination['total_pages']) ?>">
                            <?= View::e($pagination['total_pages']) ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($pagination['page'] < $pagination['total_pages']): ?>
                        <a href="<?= View::url('/admin/reference?type=' . $selectedType . '&page=' . ($pagination['page'] + 1)) ?>">Next</a>
                    <?php else: ?>
                        <span class="disabled">Next</span>
                    <?php endif; ?>
                </nav>
            </div>
        </section>
    </section>

    <?php if (! $isLocked): ?>
        <div class="reference-modal" id="reference-edit-modal" hidden>
            <div class="reference-modal-backdrop js-close-reference-modal"></div>
            <section class="reference-modal-panel" role="dialog" aria-modal="true" aria-labelledby="reference-modal-title">
                <div class="reference-modal-heading">
                    <div>
                        <p class="eyebrow">Edit Entry</p>
                        <h2 id="reference-modal-title"><?= View::e($selectedConfig['label']) ?></h2>
                    </div>
                </div>

                <form class="reference-form reference-modal-form" method="post" action="<?= View::url('/admin/reference/update') ?>">
                    <input type="hidden" name="type" value="<?= View::e($selectedType) ?>">
                    <input type="hidden" name="page" value="<?= View::e($pagination['page']) ?>">
                    <input type="hidden" name="id" id="reference-modal-id">

                    <?php foreach ($fields as $field => $label): ?>
                        <label>
                            <span><?= View::e($label) ?></span>
                            <?php $renderField($field, $label); ?>
                        </label>
                    <?php endforeach; ?>

                    <div class="reference-modal-actions">
                        <button class="secondary-button js-close-reference-modal" type="button">Cancel</button>
                        <button class="admin-action-button" type="submit">Save Changes</button>
                    </div>
                </form>
            </section>
        </div>
    <?php endif; ?>
</main>
