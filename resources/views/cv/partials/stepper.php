<?php

use App\Core\View;

$activeStep = $activeStep ?? 'personal';
$builderStepStatus = $builderStepStatus ?? [];
$steps = [
    'personal' => [
        'number' => 1,
        'label' => 'Personal Info',
        'url' => '/cv/edit/personal-info',
    ],
    'academic' => [
        'number' => 2,
        'label' => 'Education & Experience',
        'url' => '/cv/edit/academic',
    ],
    'qualifications' => [
        'number' => 3,
        'label' => 'Qualifications & Skills',
        'url' => '/cv/edit/qualifications',
    ],
    'review' => [
        'number' => 4,
        'label' => 'Review',
        'url' => '/cv/edit/review',
    ],
];
$activeStepNumber = (int) ($steps[$activeStep]['number'] ?? 1);
?>
<aside class="builder-stepper" aria-label="CV builder progress">
    <?php foreach ($steps as $key => $step): ?>
        <?php
        $isActive = $activeStep === $key;
        $isComplete = (bool) ($builderStepStatus[$key] ?? false);
        $classes = ['builder-step'];

        if ($isComplete) {
            $classes[] = 'is-complete';
        } elseif ((int) $step['number'] < $activeStepNumber) {
            $classes[] = 'is-incomplete';
        }

        if ($isActive) {
            $classes[] = 'active';
        }

        $statusLabel = $isComplete ? 'Complete' : 'Required';
        ?>
        <a class="<?= implode(' ', $classes) ?>" href="<?= View::url($step['url']) ?>" onclick="window.location.href = this.href; return false;">
            <span><?= (int) $step['number'] ?></span>
            <div>
                <strong><?= View::e($step['label']) ?></strong>
                <small><?= View::e($isActive && ! $isComplete ? 'In Progress' : $statusLabel) ?></small>
            </div>
        </a>
    <?php endforeach; ?>
</aside>
