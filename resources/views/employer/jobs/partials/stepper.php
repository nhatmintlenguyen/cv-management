<?php

$currentStep = $currentStep ?? 1;
$steps = [
    1 => ['label' => 'Job Basics', 'status' => $currentStep === 1 ? 'In Progress' : 'Saved'],
    2 => ['label' => 'Location & Compensation', 'status' => $currentStep === 2 ? 'In Progress' : ($currentStep > 2 ? 'Saved' : 'Upcoming')],
    3 => ['label' => 'Requirements & Description', 'status' => $currentStep === 3 ? 'In Progress' : ($currentStep > 3 ? 'Saved' : 'Upcoming')],
    4 => ['label' => 'Review & Publish', 'status' => $currentStep === 4 ? 'In Progress' : 'Upcoming'],
];
?>
<aside class="builder-stepper" aria-label="Job vacancy posting progress">
    <?php foreach ($steps as $number => $step): ?>
        <?php
        $class = $number === $currentStep ? 'active' : '';
        $class = $number < $currentStep ? 'completed' : $class;
        ?>
        <div class="builder-step <?= $class ?>">
            <span><?= $number ?></span>
            <div>
                <strong><?= $step['label'] ?></strong>
                <small><?= $step['status'] ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</aside>
