<div id="banner2">
    <img src="/img/lbt_title.png" class="lbt_title" alt="LBT">
</div>

<h3><?= __("Change Facility Name"); ?></h3>

<?= $this->Form->create(false) ?>
<?= $this->Form->input('name', ['label' => __('Facility Name'), 'value' => $name]) ?>
<a href="<?= $this->Url->build(["controller" => "users", "controller" => "buildings"]); ?>"
   class="btn btn-default"><?= __("Go Back"); ?></a>
<?= $this->Form->button(__('Update'), ['class' => 'btn  btn-primary pull-right']) ?>
<?= $this->Form->end() ?>
