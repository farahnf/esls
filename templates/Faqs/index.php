<?php

use Cake\Routing\Router;

echo $this->Html->css('select2/css/select2.css');
echo $this->Html->script('select2/js/select2.full.min.js');
echo $this->Html->css('jquery.datetimepicker.min.css');
echo $this->Html->script('jquery.datetimepicker.full.js');
echo $this->Html->script('https://cdn.jsdelivr.net/npm/apexcharts');
echo $this->Html->script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js');
$c_name = $this->request->getParam('controller');
echo $this->Html->script('bootstrapModal', ['block' => 'scriptBottom']);
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
// Mock data (defined before usage)
$general = [
    (object)[
        'id' => 1,
        'category' => 'general',
        'question' => 'What is DaySync?',
        'answer' => 'DaySync is a web-based system designed to streamline employee scheduling and leave management.'
    ],
    (object)[
        'id' => 2,
        'category' => 'general',
        'question' => 'Can I view my shift schedule in advance?',
        'answer' => 'Yes, you can view your upcoming shifts on both calendar and list view.'
    ]
];

$account = [
    (object)[
        'id' => 1,
        'category' => 'account',
        'question' => 'How do I reset my password?',
        'answer' => 'Click on "Forgot Password" at login and follow the instructions sent to your email.'
    ]
];

$other = [
    (object)[
        'id' => 1,
        'category' => 'other',
        'question' => 'Does DaySync support different leave types?',
        'answer' => 'Yes, including Annual, Emergency, Pregnancy, and Replacement leave.'
    ]
];
?>

<!-- Your existing view HTML below -->
<div class="row text-body-secondary">
    <div class="col-12">
        <h1 class="my-0 page_title"><?= $title ?? 'Frequently Asked Questions'; ?></h1>
        <h6 class="sub_title text-body-secondary"><?= $system_name ?? 'DaySync – Employee Scheduling and Leave System'; ?></h6>
    </div>
</div>
<div class="line mb-4"></div>

<div class="row">
    <!-- General -->
    <div class="col-md-6">
        <div class="card bg-body-tertiary border-0 shadow mb-4">
            <div class="card-body">
                <div class="card-title mb-0">General</div>
                <div class="tricolor_line mb-3"></div>
                <?php foreach ($general as $faq) : ?>
                    <a href="#<?= $faq->category . $faq->id ?>" data-bs-toggle="collapse" aria-controls="<?= $faq->category . $faq->id ?>">
                        <i class="far fa-plus-square"></i> <?= h($faq->question) ?><br>
                    </a>
                    <div class="collapse" id="<?= $faq->category . $faq->id ?>">
                        <?= $faq->answer ?><br><br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Account -->
    <div class="col-md-6">
        <div class="card bg-body-tertiary border-0 shadow mb-4">
            <div class="card-body">
                <div class="card-title mb-0">Account</div>
                <div class="tricolor_line mb-3"></div>
                <?php foreach ($account as $faq) : ?>
                    <a href="#<?= $faq->category . $faq->id ?>" data-bs-toggle="collapse" aria-controls="<?= $faq->category . $faq->id ?>">
                        <i class="far fa-plus-square"></i> <?= h($faq->question) ?><br>
                    </a>
                    <div class="collapse" id="<?= $faq->category . $faq->id ?>">
                        <?= $faq->answer ?><br><br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Others -->
    <div class="col-md-6">
        <div class="card bg-body-tertiary border-0 shadow mb-4">
            <div class="card-body">
                <div class="card-title mb-0">Others</div>
                <div class="tricolor_line mb-3"></div>
                <?php foreach ($other as $faq) : ?>
                    <a href="#<?= $faq->category . $faq->id ?>" data-bs-toggle="collapse" aria-controls="<?= $faq->category . $faq->id ?>">
                        <i class="far fa-plus-square"></i> <?= h($faq->question) ?><br>
                    </a>
                    <div class="collapse" id="<?= $faq->category . $faq->id ?>">
                        <?= $faq->answer ?><br><br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
                </div>
</div>