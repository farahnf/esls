<?php
//echo $this->Html->css('select2/css/select2.css');
//echo $this->Html->script('select2/js/select2.full.min.js');
//echo $this->Html->script('tinymce/tinymce.min.js');
//echo $this->Html->script('ckeditor/ckeditor');
?>
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
<!--Header-->
<div class="row text-body-secondary">
    <div class="col-10">
        <h1 class="my-0 page_title"><?php echo $title; ?></h1>
        <h6 class="sub_title text-body-secondary"><?php echo $system_name; ?></h6>
    </div>
    <div class="col-2 text-end">
        <?= $this->Html->link(__('<i class="far fa-comment-alt"></i> Check Response'), ['action' => 'check'], ['class' => 'btn btn-outline-primary btn-sm', 'escape' => false]) ?>
    </div>
</div>
<div class="line mb-4"></div>
<!--/Header-->
<div class="card bg-body-tertiary border-0 shadow mb-4">
    <div class="card-body m-0 p-0">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.2000131893815!2d101.61876301525233!3d3.0409885546548754!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc4bcd2e2268f9%3A0x18a6873c8e308d42!2sPuchong%2C%20Selangor%2C%20Malaysia!5e0!3m2!1sen!2smy!4v1632231729035!5m2!1sen!2smy" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

        <div class="card-body">
            <?php
            $length =   7;
            $chrDb  =   array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3', '4', '5', '6', '7', '8', '9');

            $str = '';
            for ($count = 0; $count < $length; $count++) {

                $chr = $chrDb[rand(0, count($chrDb) - 1)];

                if (rand(0, 1) == 0) {
                    $chr = strtolower($chr);
                }
                if (3 == $count) {
                    $str .= '-';
                }
                $str .= $chr;
            }
            ?>

            <div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Contact Us</h4>
        </div>
        <div class="card-body">
            <p><strong>Syairah Husna Saiful Bahri</strong></p>
            <p>Database Administrator</p>
            <p><strong>Phone:</strong> <a href="tel:+60176010204">+6017-6010204</a></p>
            <p><strong>Email:</strong> <a href="mailto:support@daysync.my">support@daysync.my</a></p>
        </div>
    </div>
</div>


<script>
    function myFunction() {
        /* Get the text field */
        var copyText = document.getElementById("ticketid");

        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        navigator.clipboard.writeText(copyText.value);

        /* Alert the copied text */
        alert("Reference copied: " + copyText.value);
    }
</script>