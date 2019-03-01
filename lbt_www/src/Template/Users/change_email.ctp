<?php
// Defined variables from controller
if (!isset($user)) {
    $user = [];
}
if (!isset($email)) {
    $email = '';
}
?>
<div id="banner2">
    <img src="/img/lbt_title.png" class="lbt_title" alt="LBT">
</div>

<h3><?= __("Change Email"); ?></h3>
<p>
    <?= __("Current Email"); ?><br>
    <?= $email; ?>
</p>

<?= $this->Form->create($user) ?>
<?= $this->Form->input('email', ['label' => __('New Email')]) ?>
<?= $this->Form->input('password', ['label' => __('Current Password')]) ?>
<a href="<?= $this->Url->build(["controller" => "users", "action" => "profile"]); ?>"
   class="btn btn-default"><?= __("Go Back"); ?></a>
<?= $this->Form->button(__('Update'), ['class' => 'btn  btn-primary pull-right']) ?>
<?= $this->Form->end() ?>
