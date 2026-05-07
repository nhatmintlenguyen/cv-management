<?php

use App\Core\View;

$currentStep = $currentStep ?? 1;
$jobStepStatus = $jobStepStatus ?? [];
$steps = [
    1 => ['label' => 'Job Basics', 'url' => '/employer/jobs/create/basics'],
    2 => ['label' => 'Location & Compensation', 'url' => '/employer/jobs/create/location'],
    3 => ['label' => 'Requirements & Description', 'url' => '/employer/jobs/create/requirements'],
    4 => ['label' => 'Review & Publish', 'url' => '/employer/jobs/create/review'],
];
?>
<aside class="builder-stepper" aria-label="Job vacancy posting progress">
    <?php foreach ($steps as $number => $step): ?>
        <?php
        $isComplete = (bool) ($jobStepStatus[$number] ?? false);
        $classes = ['builder-step'];

        if ($isComplete) {
            $classes[] = 'is-complete';
        } elseif ($number < $currentStep) {
            $classes[] = 'is-incomplete';
        }

        if ($number === $currentStep) {
            $classes[] = 'active';
        }

        $status = $isComplete ? 'Complete' : ($number === $currentStep ? 'In Progress' : ($number < $currentStep ? 'Required' : 'Upcoming'));
        ?>
        <a class="<?= implode(' ', $classes) ?>" href="<?= View::url($step['url']) ?>">
            <span><?= $number ?></span>
            <div>
                <strong><?= $step['label'] ?></strong>
                <small><?= $status ?></small>
            </div>
        </a>
    <?php endforeach; ?>
</aside>
