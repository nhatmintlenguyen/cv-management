<?php

use App\Core\View;

$currentStep = $currentStep ?? 1;
$steps = [
    1 => ['label' => 'Job Basics', 'url' => '/employer/jobs/create/basics', 'status' => $currentStep === 1 ? 'In Progress' : 'Saved'],
    2 => ['label' => 'Location & Compensation', 'url' => '/employer/jobs/create/location', 'status' => $currentStep === 2 ? 'In Progress' : ($currentStep > 2 ? 'Saved' : 'Upcoming')],
    3 => ['label' => 'Requirements & Description', 'url' => '/employer/jobs/create/requirements', 'status' => $currentStep === 3 ? 'In Progress' : ($currentStep > 3 ? 'Saved' : 'Upcoming')],
    4 => ['label' => 'Review & Publish', 'url' => '/employer/jobs/create/review', 'status' => $currentStep === 4 ? 'In Progress' : 'Upcoming'],
];
?>
<aside class="builder-stepper" aria-label="Job vacancy posting progress">
    <?php foreach ($steps as $number => $step): ?>
        <?php
        $class = $number === $currentStep ? 'active' : '';
        $class = $number < $currentStep ? 'completed' : $class;
        ?>
        <a class="builder-step <?= $class ?>" href="<?= View::url($step['url']) ?>">
            <span><?= $number ?></span>
            <div>
                <strong><?= $step['label'] ?></strong>
                <small><?= $step['status'] ?></small>
            </div>
        </a>
    <?php endforeach; ?>
</aside>
