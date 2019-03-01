<!-- Confirm Delete -->
<div class="modal fade" id="ConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="delete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header no-border">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-footer no-border">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Cancel"); ?></button>
                <?php
                echo $this->Form->postLink(
                    __('Confirm'),
                    array('action' => 'delete'),
                    array('class' => 'btn btn-danger'),
                    false
                );
                ?>
            </div>
        </div>
    </div>
</div>
<!-- Confirm Delete -->

<div id="banner2">
    <img src="/img/lbt_title.png" class="lbt_title" alt="LBT">
</div>

<a href="/buildings/add" class="pull-right btn btn-primary" id="add-building">
 <?=__("New Building");?>
</a>
<h3> <?=__("Your Buildings");?></h3>

<table class="table">
    <thead>
        <tr>
            <td>
                <?=__("Building Name"); ?>
            </td>
            <td>
                <?=__("Data Years"); ?>
            </td>
            <td>
                <?=__("Actions"); ?>
            </td>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($myBuildings as $id => $building):
            ?>
            <tr>
                <td>
                    <a href="/buildings/edit_name/<?= $myBuildingFacility[$id]['id'];  ?>">
                        <?= $myBuildingFacility[$id]['name']; ?> &nbsp;
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>
                   </td>
                <td>
                    <?php

                    foreach ($building as $obj):
                    ?>
                        <a href="/buildings/edit/<?= $obj['id']; ?>" >
                            <span class="badge badge-info"><?=$obj['year'];?> &nbsp;
                            <i class="glyphicon glyphicon-pencil"></i>
                                </span>
                        </a>


                    <?php
                    endforeach;
                    ?>
                    </td>
                <td>
                    <?php
                    if(count($building) == 1) {
                        echo $this->Html->link(__("Delete Building"), "#", ['escape' => false, "class" => "delete btn btn-sm btn-danger", "data-id" => $building[0]['id'], "data-title" => $myBuildingFacility[$id]['name']]);
                    }else{
                        echo $this->Html->link(__("Delete Building"), "#", ['escape' => false, "class" => "btn btn-sm btn-danger", "disabled" => true,"data-toggle" => "tooltip", " data-placement" => "top", "title" => "To help prevent accidental deletions of large amounts of data, buildings with more than one year of data can't be deleted directly. Please delete Data Years individually (from within the Edit screen for each year)."]);
                    }
                    ?>
                    <a href="/buildings/add/<?= $myBuildingFacility[$id]['id']; ?>/<?= $myBuildingFacility[$id]['last_building_id']; ?>" class="btn btn-sm btn-primary">
                        Add Data Year
                    </a>
                </td>
            </tr>
        <?php
        endforeach;
        ?>
    </tbody>
</table>

<script>
    $(function () {

        var confirmDelete = $("#ConfirmDelete");

        $('.delete').on('click', function (e) {
            e.preventDefault();
            confirmDelete.find('.modal-title').html('<?=__('Delete Building');?> ' + $(this).attr("data-title") + ' ?');
            $("form").attr('action', '/buildings/delete/' + $(this).attr('data-id'));
            confirmDelete.modal('show');
        });

        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
