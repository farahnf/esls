<?php
echo $this->Html->css('animate.min');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Morphext/2.4.4/morphext.css" integrity="sha256-iwSnUqgAndMlZnwFWAAzto9R/6Un2RBguZEITMb0Olk=" crossorigin="anonymous" />

<div class="mx-auto my-auto p-2 col-md-6">
    <div class="card bg-body-tertiary border-0 shadow mb-4">
        <div class="card-body">
            <!-- Header -->
            <div class="my-4 text-center">
                <h1 class="my-0 page_title">LOGIN</h1>
            </div>
            <div class="tricolor_line mb-3"></div>

            <!-- Login Form -->
            <?= $this->Form->create(null, ['class' => 'needs-validation']) ?>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <?= $this->Form->control('email', [
                        'label' => 'Email',
                        'required' => true,
                        'class' => 'form-control border-0',
                        'autocomplete' => 'off'
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $this->Form->control('password', [
                        'label' => 'Password',
                        'required' => true,
                        'class' => 'form-control border-0'
                    ]) ?>
                </div>
            </div>

            <!-- Buttons -->
            <div class="text-end mb-3">
                <?= $this->Form->button('Reset', ['type' => 'reset', 'class' => 'btn btn-outline-warning btn-sm']) ?>
                <?= $this->Form->button(__('Submit'), ['type' => 'submit', 'class' => 'btn btn-outline-primary btn-sm']) ?>
            </div>
            <?= $this->Form->end() ?>

           <!-- Links -->
<div class="text-center mb-3 small">
    <?= $this->Html->link('Forgot Password?', ['controller' => 'Users', 'action' => 'forgot_password'], ['class' => 'link-danger']) ?>
</div>
            <hr>

            <!-- Quick Access Buttons -->
            <div class="btn-grid text-center mb-3">
                <?= $this->Html->link('User Manual', ['controller' => 'Pages', 'action' => 'manual'], ['class' => 'btn btn-primary btn-xs']) ?>
                <?= $this->Html->link('Frequently Asked Question', ['controller' => 'Faqs', 'action' => 'index'], ['class' => 'btn btn-primary btn-xs']) ?>
                <?= $this->Html->link('Contact Us', ['controller' => 'Contacts', 'action' => 'add'], ['class' => 'btn btn-primary btn-xs']) ?>
            </div>

            <hr>

            <!-- Branding -->
            <div class="text-center mb-3">
                <b class="gradient-animate-tiny"><b class="logo-small">&lt;/&gt;</b> <?= h($system_abbr); ?></b>
            </div>

            <!-- Footer Info -->
            <div class="text-center small text-muted">
                <p class="mb-1">Leading The CRUD Evolution</p>
                <p class="mb-1"><?= h($system_name); ?> (<?= h($system_abbr); ?>)</p>
                <p class="mb-0">
                    &copy; 2022-<script>document.write(new Date().getFullYear());</script> <?= h($system_abbr); ?>. All rights reserved.
                    [V <?= h($version); ?>]
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Morphext JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Morphext/2.4.4/morphext.min.js" integrity="sha256-qG3zvg7/f5CZHwV8IeaQfBY5Hm+M0KR3PMk9lAHp39s=" crossorigin="anonymous"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById("js-rotating")) {
            $("#js-rotating").Morphext({
                animation: "fadeInDown",
                complete: function () {
                    console.log("Phrase rotated, index: " + this.index);
                }
            });
        }
    });
</script>