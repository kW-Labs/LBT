<?php
// Defined variables from controller
if(!isset($token)){$token =[];}
?>
<div class="container-fluid">
    <?= $this->Form->create(null, ['url' => ['action' => 'reset',$token]]);?>
    <fieldset>
        <legend><?= __('Pick a New Password') ?></legend>
        <?php
        echo $this->Form->input('email', array('type' => 'hidden'));
        echo $this->Form->input('password1', array('type' => 'password', 'label'=> __('New Password')));
        echo $this->Form->input('password2', array('type' => 'password', 'label' => __('Confirm Password')));
        ?>
    </fieldset>
    <?= $this->Form->button(__('Change Password'),['class' => 'btn btn-block  btn-primary']) ?>
    <?= $this->Form->end() ?>
</div>