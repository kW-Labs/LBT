<?php
if (!isset($user)) {
    $user = [];
}
?>
    <div class="text-center" id="banner2">
    </div>
    <h3><?= __("Change Password"); ?> </h3>
<?= $this->Form->create($user, ['url' => ['action' => 'change_password']]); ?>
    <fieldset>
        <?= $this->Form->input('old_password', ['type' => 'password', 'required' => 'required', 'label' => __('Old password')]) ?>
        <?= $this->Form->input('password1', ['type' => 'password', 'required' => 'required', 'label' => __('New Password')]) ?>
        <?= $this->Form->input('password2', ['type' => 'password', 'required' => 'required', 'label' => __('Confirm Password')]) ?>
    </fieldset>
    <a href="<?= $this->Url->build(["controller" => "users", "action" => "profile"]); ?>"
       class="btn btn-default"><?= __("Go Back"); ?></a>
<?= $this->Form->button(__('Update'), ['class' => 'btn  btn-primary pull-right']) ?>
<?= $this->Form->end() ?>