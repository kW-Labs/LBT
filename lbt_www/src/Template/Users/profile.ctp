<?php
if (!isset($email)) {
    $email = '';
}
?>

<div id="banner2">
    <img src="/img/lbt_title.png" class="lbt_title" alt="LBT">
</div>

<div class="row">
    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
        <p>
            <?=__("Password");?>:   ******
            <?= $this->Html->link('Change Password', '/users/change_password', ['class' => 'pull-right']);?>
        </p>
    </div>
</div>
