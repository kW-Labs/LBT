<?= $this->Html->css('pretty-checkbox.min.css'); ?>
<?= $this->Html->css('bootstrap-tagsinput.css'); ?>
<?= $this->Html->css('bootstrap-tagsinput-typeahead.css'); ?>
<?= $this->Html->css('charts.css'); ?>

<?php

if (!isset($label)) {
    $label = [];
}

if (!isset($metrics)) {
    $metrics = [];
}

if (!isset($metrics_no_data)) {
    $metrics_no_data = [];
}

if (!isset($category)) {
    $category = [];
}

if (!isset($tooltip)) {
    $tooltip = [];
}

$predominant_fume_type_id = $this->Form->control('fume_hood_control', ['id' => 'fume_hood_control', 'label' => $label['fume_hood_control'], 'options' => $category['fume_hood_control']]);
$predominant_hvac_system_type_id = $this->Form->control('hvac_type', ['id' => 'hvac_type', 'label' => $label['hvac_type'], 'options' => $category['hvac_type']]);
$predominant_hvac_control_type_id = $this->Form->control('hvac_control', ['id' => 'hvac_control', 'label' => $label['hvac_control'], 'options' => $category['hvac_control']]);
$exhaust_air_energy_recovery_id = $this->Form->control('exhaust_air_energy_recovery', ['id' => 'exhaust_air_energy_recovery', 'label' => $label['exhaust_air_energy_recovery'], 'options' => $category['exhaust_air_energy_recovery']]);
$cooling_system_type_id = $this->Form->control('cooling', ['label' => $label['cooling'], 'options' => $category['cooling']]);
$heating_system_type_id = $this->Form->control('heating', ['label' => $label['heating'], 'options' => $category['heating']]);
$building_level_combined_heat_power = $this->Form->control('building_level_combined_heat_power', ['id' => 'building_level_combined_heat_power', 'label' => $label['building_level_combined_heat_power'], 'options' => $category['building_level_combined_heat_power']]);
$on_site_renewable_energy_type = $this->Form->control('on_site_renewable_energy_type', ['id' => 'on_site_renewable_energy_type', 'label' => $label['on_site_renewable_energy_type'], 'options' => $category['on_site_renewable_energy_type']]);

// Add a template for checkboxes
$bigInputCheckbox = [
    'inputContainer' => '<div class="custom-checkbox">
                                    <div class="pretty p-icon p-curve" id="{{id}}_curve">
                                        <input type="checkbox" class="form-check-input {{id}} {{class}}" id="{{id}}" name="{{id}}"  data-toggle="tooltip" data-placement="top" title="{{tooltip}}">
                                        <div class="state p-warning">
                                            <i class="icon glyphicon glyphicon-ok"></i>
                                            <label for="{{id}}">{{label}}</label>
                                        </div>
                                    </div>
                                   </div>',
];

$templateSlider = [
    'inputContainer' => '<div class="slider-card">
                                <h4 id="{{id}}_h4" data-toggle="tooltip" data-placement="top" title="{{tooltip}}">{{label}}</h4>       
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div id="{{id}}"></div>
                                    </div>
                                </div>
                                <div class="row slider-labels">
                                    <div class="col-xs-5 caption">
                                        <strong>' . __("Min:") . '</strong> <span id="{{id}}-value-1"></span>
                                        <input type="hidden" name="{{id}}_min_max" min="" max="" id="{{id}}_min_max">
                                    </div>
                                    <div class="col-xs-5 pull-right text-right caption">
                                        <strong>' . __("Max:") . '</strong> <span id="{{id}}-value-2"></span>
                                    </div>
                                </div>
                            </div>',
];

$yesNoToggleTemplate = [
    'inputContainer' => '<div>
                            <h5 id="{{id}}_h5">{{label}}</h5>
                            <span class="hidden pull-right clear" id="{{id}}Link"
                               data-id="{{id}}"><i class="glyphicon glyphicon-remove"></i> ' . __("clear") . '</span>

                            <div  id="{{id}}_yes_no_toggle" class="yes_no_toggle" data-toggle="tooltip" data-placement="top" title="{{tooltip}}">
                                <span class="yes_span"></span>
                                <span class="no_span"></span>
                                <b class="toggle_icon happy" aria-hidden="true" data-id="{{id}}"
                                   id="{{id}}Happy">&nbsp;' . __("Y") . '</b>
                                <b class="toggle_icon sad" aria-hidden="true" data-id="{{id}}"
                                   id="{{id}}Sad">' . __("N") . '&nbsp;</b>
                                <div class="toggle" id="{{id}}Toggle"></div>
                                <div class="clearfix"></div>
                            </div>

                            <div class="yes_no_wrap">
                                <label for="{{id}}Yes" class="hidden">' . __("Yes") . '</label>
                                <input type="radio" id="{{id}}Yes"  value="true" name="{{id}}"/>' . __("Yes") . '
                                <label for="{{id}}No" class="hidden">' . __("No") . '</label>
                                <input type="radio" id="{{id}}No" value="false" name="{{id}}"/>' . __("No") . '
                            </div>
                        </div>',
];

$this->Form->setTemplates($bigInputCheckbox);
?>

<div class="container">

    <div id="banner1">
        <img src="/img/lbt_title.png" class="lbt_title" alt="LBT">
    </div>

    <div class="col-lg-8 col-md-8 col-sm-12">
        <h3><?= __('Benchmark Analysis'); ?>
            <i class="glyphicon glyphicon-info-sign glyphicon-color-grey" data-toggle="tooltip" data-placement="right"
               title="Use the filters at right to select buildings with properties of interest (e.g. properties similar to your own buildings). Only buildings fitting the filtering criteria will be shown on the plots below. Charts include only the most recent year of data for each building."></i>
        </h3>
        <i class="text-muted"><?= __("By default, all buildings are shown on the charts. Use the filters to narrow down your peer group."); ?>
        </i>

        <div class="panel-group" id="accordionScatter" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading bg-info" id="headingOne">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionScatter"
                           href="#collapseAccordionScatter">
                            <span class="glyphicon glyphicon-minus"></span>
                            <?= __('Scatter Plot'); ?>
                            <i class="glyphicon glyphicon-info-sig " data-toggle="tooltip" data-placement="top"
                               title="Show selected buildings' data on an xy scatter plot"></i>
                        </a>
                    </h4>
                </div>

                <div id="collapseAccordionScatter" class="panel-collapse collapse in" role="tabpanel"
                     aria-labelledby="headingOne">
                    <div class="panel-body">
                        <div class="echart" id="scatter"
                             style="min-width:100px;  min-height:400px; max-width: 800px;  max-height:600px;"></div>
                        <div class="container-fluid">

                            <hr>
                            <h4><?= __("Select Quantities to Plot"); ?></h4>
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label for="xaxis"
                                               class="col-sm-5 col-form-label axis_label"><?= __("Horizontal Axis"); ?></label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="xaxis">
                                                <optgroup label="--- Contains Peer Data --">
                                                    <?php
                                                    foreach ($metrics as $k => $v) :
                                                        ?>
                                                        <option value="<?= $k; ?>" <?php if ($k === "total_lab_area") {
                                                            echo " selected";
                                                        } ?>> <?= $v; ?></option>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                                </optgroup>
                                                <optgroup label="--- No Peer Data --">
                                                    <?php
                                                    foreach ($metrics_no_data as $k => $v) :
                                                        ?>
                                                        <option value="<?= $k; ?>"> <?= $v; ?></option>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label for="xunits" class="col-sm-3 col-form-label"><?= __("Units"); ?></label>
                                        <div class="col-sm-3">
                                            <div id="xunits">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label for="yaxis"
                                               class="col-sm-5 col-form-label axis_label"><?= __("Vertical Axis"); ?>
                                            <span class="text-muted">*</span> </label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="yaxis">
                                                <optgroup label="--- Contains Peer Data --">
                                                    <?php
                                                    foreach ($metrics as $k => $v) :
                                                        ?>
                                                        <option value="<?= $k; ?>" <?php if ($k === "source_eui") {
                                                            echo " selected";
                                                        } ?>> <?= $v; ?></option>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                                </optgroup>
                                                <optgroup label="--- No Peer Data --">
                                                    <?php
                                                    foreach ($metrics_no_data as $k => $v) :
                                                        ?>
                                                        <option value="<?= $k; ?>"> <?= $v; ?></option>
                                                    <?php
                                                    endforeach;
                                                    ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <label for="yunits" class="col-sm-3 col-form-label"><?= __("Units"); ?></label>
                                        <div class="col-sm-3">
                                            <div id="yunits">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <hr>
                            <h4 class="mb-3">
                                <?= __("Summary Statistics for Selected Peer Group Buildings"); ?>
                            </h4>
                            <p id="peerSummary">
                            </p>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel-group" id="accordionHistogram" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default">
                <div class="panel-heading bg-info" id="headingTwo">
                    <h4 class="panel-title">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionHistogram"
                           href="#collapseAccordionHistogram">
                            <span class="glyphicon glyphicon-minus"></span>
                            <?= __('Histogram and Sorted Column Plot'); ?>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top"
                               title="Show building data on a histogram or sorted column-type plot."></i>
                        </a>
                    </h4>

                </div>

                <div id="collapseAccordionHistogram" class="panel-collapse collapse in" role="tabpanel"
                     aria-labelledby="headingTwo">
                    <div class="panel-body">
                        <p class="text-muted"><?= __("* Use Scatter Plot Vertical Axis dropdown menu to select quantity to plot."); ?></p>
                        <div class="panel-body">

                            <ul class="nav nav-tabs" role="tablist">
                                <li class="active"><a href="#histogram_tab" data-toggle="tab">
                                        <?= __('Histogram'); ?>
                                    </a></li>
                                <li><a href="#sorted_tab" data-toggle="tab">
                                        <?= __('Sorted Column'); ?>
                                    </a></li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="histogram_tab">
                                    <div class="echart" id="histogram"
                                         style="min-width:100px; min-height:400px; max-width: 800px;  max-height:600px;"></div>
                                </div>
                                <div class="tab-pane" id="sorted_tab">

                                    <div class="echart" id="sorted"
                                         style="min-width:100px;  min-height:400px; max-width: 800px;  max-height:600px;"></div>
                                </div>

                            </div>

                            <div class="clearfix"></div>
                            <br>

                            <div class="container-fluid hidden">
                                <h4 class="mb-3">
                                    <?= __('Your Buildings'); ?>
                                </h4>
                                <table class="table table-bordered hidden">
                                    <thead>
                                    <tr class="font-weight-bold">
                                        <td><?= __('Building Name'); ?></td>
                                        <td><?= __('Source Energy Usage'); ?></td>
                                        <td><?= __('Percentile Rank'); ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="col-lg-4 col-md-4 col-sm-12">

        <button class="btn btn-sm btn-danger pull-right" id="clear_all">
            <?= __('Clear All'); ?>
        </button>

        <h3><?= __('Filters '); ?><i class="glyphicon glyphicon-info-sign glyphicon-color-grey" data-toggle="tooltip" data-placement="right"
                                    title="Use the filters below to select buildings of interest. Note that some filters have been disabled because they don't (yet) contain any data. These are marked with flags in the filter popup windows."></i>
        </h3>
        <i class="text-muted"><?= __("Start with Basic Filters if you're a new user."); ?></i>

        <div class="panel-group" id="filterAccordionBasic">
            <div class="panel panel-default">
                <div class="panel-heading bg-info">
                    <h4 class="panel-title">
                        <button class="btn btn-sm btn-default pull-right edit" data-toggle="modal"
                                data-target="#basicFiltersPopup" data-backdrop="static"
                                data-keyboard="false"><?= __("Edit"); ?></button>
                        <button class="btn btn-sm btn-danger pull-right clearFilter" id="clear_basic">
                            <?= __("Clear"); ?></button>
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#filterAccordionBasic"
                           href="#collapsefilterAccordionBasic">
                            <span class="glyphicon glyphicon-minus"></span>
                            <?= __('Basic Filters'); ?>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top"
                               title="The most commonly used filters. Start here if you are a new user."></i>
                        </a>
                    </h4>
                </div>
                <div id="collapsefilterAccordionBasic" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div id="basicFilterSummary">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-group" id="filterAccordionClimateZones">
            <div class="panel panel-default">
                <div class="panel-heading bg-info">
                    <h4 class="panel-title">
                        <button class="btn btn-sm btn-default pull-right edit" data-toggle="modal"
                                data-target="#buildingPropertiesPopup" data-backdrop="static"
                                data-keyboard="false"><?= __("Edit"); ?></button>
                        <button class="btn btn-sm btn-danger pull-right clearFilter" id="clear_building_properties">
                            <?= __("Clear"); ?></button>
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#filterAccordionClimateZones"
                           href="#collapsefilterAccordionClimateZones">
                            <span class="glyphicon glyphicon-minus"></span>
                            <?= __('Building Properties'); ?>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top"
                               title="Filters for organization type, lab use types, occupancy, and year of construction."></i>
                        </a>
                    </h4>
                </div>
                <div id="collapsefilterAccordionClimateZones" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div id="buildingPropertiesSummary">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-group" id="filterAccordionBuildingProperties">
            <div class="panel panel-default">
                <div class="panel-heading bg-info">
                    <h4 class="panel-title">
                        <button class="btn btn-sm btn-default pull-right edit" data-toggle="modal"
                                data-target="#buildingSystemPopup" data-backdrop="static"
                                data-keyboard="false"><?= __("Edit"); ?></button>
                        <button class="btn btn-sm btn-danger pull-right clearFilter" id="clear_building_system">
                            <?= __("Clear"); ?></button>
                        <a class="accordion-toggle" data-toggle="collapse"
                           data-parent="#filterAccordionBuildingProperties"
                           href="#collapsefilterAccordionBuildingProperties">
                            <span class="glyphicon glyphicon-minus"></span>
                            <?= __('Building Systems'); ?>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top"
                               title="Filters for building HVAC system types and controls features."></i>
                        </a>
                    </h4>
                </div>
                <div id="collapsefilterAccordionBuildingProperties" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div id="buildingSystemSummary"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-group" id="filterAccordionFumeHood">
            <div class="panel panel-default">
                <div class="panel-heading bg-info">
                    <h4 class="panel-title">
                        <button class="btn btn-sm btn-default pull-right edit" data-toggle="modal"
                                data-target="#fumeHoodsPopup" data-backdrop="static"
                                data-keyboard="false"><?= __("Edit"); ?></button>
                        <button class="btn btn-sm btn-danger pull-right clearFilter" id="clear_fume_hoods">
                            <?= __("Clear"); ?></button>
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#filterAccordionFumeHood"
                           href="#collapsefilterAccordionFumeHood">
                            <span class="glyphicon glyphicon-minus"></span>
                            <?= __('Fume Hoods'); ?>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top"
                               title="Filters for fume hood counts and control strategies, and for methods used to assign lab ventilation rates."></i>
                        </a>
                    </h4>
                </div>
                <div id="collapsefilterAccordionFumeHood" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div id="fumeHoodsSummary">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-group" id="filterAccordionLabTypes">
            <div class="panel panel-default">
                <div class="panel-heading bg-info">
                    <h4 class="panel-title">
                        <button class="btn btn-sm btn-default pull-right edit" data-toggle="modal"
                                data-target="#labTypesPopup" data-backdrop="static"
                                data-keyboard="false"><?= __("Edit"); ?></button>
                        <button class="btn btn-sm btn-danger pull-right clearFilter" id="clear_lab_types">
                            <?= __("Clear"); ?></button>
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#filterAccordionLabTypes"
                           href="#collapsefilterAccordionLabTypes">
                            <span class="glyphicon glyphicon-minus"></span>
                            <?= __('Lab Types'); ?>
                            <i class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top"
                               title="Advanced filters for lab types by area, plus filtering by specialty lab types and equipment."></i>
                        </a>
                    </h4>
                </div>
                <div id="collapsefilterAccordionLabTypes" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <div id="labTypesSummary">

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Basic Filters -->
<div class="modal modal-fullscreen fade" tabindex="-1" role="dialog" aria-labelledby="basicFiltersModal"
     id="basicFiltersPopup" aria-hidden="true">
    <div class="modal-dialog" id="forgotDialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only"><?= __("Cancel"); ?></span></button>
                <h2 class="modal-title" id="basicFiltersModal"><?= __("Basic Filters"); ?></h2>
            </div>
            <div class="modal-body">
                <form id="basicFilter">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3 data-toggle="tooltip" data-placement="left"
                                    title="<?= $tooltip['lab_use']; ?>"><?= __('Lab Type'); ?></h3>
                                <p id="lab_notice" class="hidden"><?= __(" Lab Type is overridden by % Lab Type. Please see Lab
                                    Types filters."); ?></p>
                                <?php
                                foreach ($category['lab_types'] as $k => $vars) {
                                    echo $this->Form->control($k, ['templateVars' => $vars]);
                                }
                                ?>
                            </div>

                            <?php
                            $this->Form->setTemplates($templateSlider);
                            echo $this->Form->control('total_lab_area', ['templateVars' => ['id' => 'total_lab_area', 'label' => $label['total_lab_area'], 'tooltip' => $tooltip['total_lab_area']]]);
                            $this->Form->setTemplates($bigInputCheckbox);
                            ?>
                        </div>
                        <div class="col-xs-12 col-md-4">

                            <div class="slider-card">
                                <h3 class='my-tool-tip' data-toggle="tooltip" data-placement="top"
                                    title="<?= $tooltip['existing_building']; ?>"><?= __("Building Status"); ?></h3>
                                <?php
                                foreach ($category['building_status'] as $k => $vars) {
                                    echo $this->Form->control($k, ['templateVars' => $vars]);
                                }
                                ?>
                            </div>

                            <div class="slider-card">
                                <h3 data-toggle="tooltip" data-placement="top"
                                    title="<?= $tooltip['data_type']; ?>"><?= __("Data Type"); ?></h3>
                                <?php
                                foreach ($category['data_type'] as $k => $vars) {
                                    echo $this->Form->control($k, ['templateVars' => $vars]);
                                }
                                ?>
                            </div>

                            <?php
                            $this->Form->setTemplates($templateSlider);
                            echo $this->Form->control('year', ['templateVars' => ['id' => 'year', 'label' => $label['year'], 'tooltip' => $tooltip['year']]]);
                            $this->Form->setTemplates($bigInputCheckbox);
                            ?>

                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <i class="material-icons pull-right" data-toggle="tooltip-map"
                                   data-img="/img/climate_zone.png">map</i>

                                <h3 data-toggle="tooltip" data-placement="top"
                                    title="<?= $tooltip['climate']; ?>"><?= __('Climate Zones'); ?></h3>
                                <?php
                                foreach ($category['climate_zones'] as $k => $vars) {
                                    echo $this->Form->control($k, ['templateVars' => $vars]);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default pull-left" data-dismiss="modal"><?= __("Cancel"); ?></button>
                <button class="btn btn-primary pull-right apply" type="submit" data-modal-id="basicFiltersPopup"
                        data-summary-id="basicFilterSummary" data-id="basicFilter"><?= __("Apply"); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Basic Filters-->

<!-- Building Properties -->
<div class="modal modal-fullscreen fade" tabindex="-1" role="dialog" aria-labelledby="buildingPropertiesModal"
     id="buildingPropertiesPopup" aria-hidden="true">
    <div class="modal-dialog" id="forgotDialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only"><?= __("Cancel"); ?></span></button>
                <h2 class="modal-title" id="buildingPropertiesModal"><?= __("Building Properties"); ?></h2>
            </div>
            <div class="modal-body">
                <form id="buildingProperties">

                    <div class="row">
                        <div class="col-xs-12 col-md-4">

                            <div class="slider-card">
                                <h3 data-toggle="tooltip" data-placement="top"
                                    title="<?= $tooltip['organization_type']; ?>"><?= __('Organization Types'); ?></h3>
                                <?php
                                foreach ($category['organization_type'] as $k => $obj) {

                                    if (empty($obj['id'])) {

                                        foreach ($obj as $kk => $vars) {

                                            if (strpos($vars['id'], '_all') !== false) {
                                                echo "<h4>" . $vars['sub_section'] . "</h4>\n";
                                            }

                                            if (strpos($vars['sub_section'], 'Health') !== false || strpos($vars['sub_section'], 'Other') !== false || strpos($vars['sub_section'], 'Unknown') !== false) {
                                                echo "<h4>" . $vars['sub_section'] . "</h4>\n";
                                            }

                                            echo $this->Form->control($kk, ['templateVars' => $vars]);
                                        }
                                    } else {
                                        echo $this->Form->control($k, ['templateVars' => $obj]);
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">

                            <div class="slider-card">
                                <h3 data-toggle="tooltip" data-placement="top"
                                    title="<?= $tooltip['lab_type']; ?>"><?= __('Predominant Lab Use Types'); ?></h3>
                                <?php
                                foreach ($category['lab_type'] as $k => $vars) {
                                    echo $this->Form->control($k, ['templateVars' => $vars]);
                                }
                                ?>
                            </div>

                            <?php
                            $this->Form->setTemplates($templateSlider);
                            echo $this->Form->control('floor_area', ['templateVars' => ['id' => 'floor_area', 'label' => $label['floor_area'], 'tooltip' => $tooltip['floor_area']]]);
                            echo $this->Form->control('operating_hours', ['templateVars' => ['id' => 'operating_hours', 'label' => $label['operating_hours'], 'tooltip' => $tooltip['operating_hours']]]);
                            echo $this->Form->control('number_of_people', ['templateVars' => ['id' => 'number_of_people', 'label' => $label['number_of_people'], 'tooltip' => $tooltip['number_of_people']]]);
                            echo $this->Form->control('year_built', ['templateVars' => ['id' => 'year_built', 'label' => $label['year_built'], 'tooltip' => $tooltip['year_built']]]);
                            $this->Form->setTemplates($bigInputCheckbox);
                            ?>

                            <div class="slider-card">
                                <h4 data-toggle="tooltip" data-placement="top"
                                    title="<?= $tooltip['state']; ?>"><?= __('States'); ?></h4>
                                <input type="text" id="state" name="state" placeholder="Enter state(s), e.g. CA, FL."
                                       data-role="tagsinput"/>
                                <script>
                                    var stateNames = new Bloodhound({
                                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                                        prefetch: {
                                            url: '<?= $this->Url->build(["controller" => "buildings", "action" => "states"], true); ?>.json',
                                            filter: function (list) {
                                                return $.map(list, function (stateName) {
                                                    return {name: stateName};
                                                });
                                            }
                                        }
                                    });
                                    stateNames.initialize();

                                    $(function () {
                                        $('#state').tagsinput({
                                            typeaheadjs: {
                                                name: 'stateNames',
                                                displayKey: 'name',
                                                valueKey: 'name',
                                                source: stateNames.ttAdapter()
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default pull-left" data-dismiss="modal"><?= __("Cancel"); ?></button>
                <button class="btn btn-primary pull-right apply" type="submit" data-modal-id="buildingPropertiesPopup"
                        data-summary-id="buildingPropertiesSummary"
                        data-id="buildingProperties"><?= __("Apply"); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Basic Filters -->

<!-- Building Systems -->
<div class="modal modal-fullscreen fade" tabindex="-1" role="dialog" aria-labelledby="buildingSystemModal"
     id="buildingSystemPopup" aria-hidden="true">
    <div class="modal-dialog" id="forgotDialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only"><?= __("Cancel"); ?></span></button>
                <h2 class="modal-title" id="buildingSystemModal"><?= __("Building Systems and Controls"); ?></h2>
            </div>
            <div class="modal-body">
                <form id="buildingSystem">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("System Types"); ?></h3>
                                <?php
                                echo $predominant_hvac_system_type_id;
                                echo $predominant_hvac_control_type_id;
                                echo $exhaust_air_energy_recovery_id;
                                echo $cooling_system_type_id;
                                echo $heating_system_type_id;
                                echo $building_level_combined_heat_power;
                                echo $on_site_renewable_energy_type;
                                ?>
                            </div>

                            <div class="slider-card">

                                <?php
                                $this->Form->setTemplates($yesNoToggleTemplate);
                                echo $this->Form->control('geothermal_heat_pump', ['templateVars' => ['id' => 'geothermal_heat_pump', 'label' => $label['geothermal_heat_pump'], 'tooltip' => $tooltip['geothermal_heat_pump']]]);
                                echo $this->Form->control('heat_recovery_chiller', ['templateVars' => ['id' => 'heat_recovery_chiller', 'label' => $label['heat_recovery_chiller'], 'tooltip' => $tooltip['heat_recovery_chiller']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("System Features"); ?></h3>

                                <?php
                                $this->Form->setTemplates($yesNoToggleTemplate);
                                echo $this->Form->control('air_side_low_pressure_drop_design', ['templateVars' => ['id' => 'air_side_low_pressure_drop_design', 'label' => $label['air_side_low_pressure_drop_design'], 'tooltip' => $tooltip['air_side_low_pressure_drop_design']]]);
                                echo $this->Form->control('water_side_low_pressure_drop_design', ['templateVars' => ['id' => 'water_side_low_pressure_drop_design', 'label' => $label['water_side_low_pressure_drop_design'], 'tooltip' => $tooltip['water_side_low_pressure_drop_design']]]);
                                echo $this->Form->control('true_variable_air_volume_exhaust', ['templateVars' => ['id' => 'true_variable_air_volume_exhaust', 'label' => $label['true_variable_air_volume_exhaust'], 'tooltip' => $tooltip['true_variable_air_volume_exhaust']]]);
                                echo $this->Form->control('high_efficiency_ultra_low_temperature_freezers', ['templateVars' => ['id' => 'high_efficiency_ultra_low_temperature_freezers', 'label' => $label['high_efficiency_ultra_low_temperature_freezers'], 'tooltip' => $tooltip['high_efficiency_ultra_low_temperature_freezers']]]);
                                echo $this->Form->control('cascade_air_use', ['templateVars' => ['id' => 'cascade_air_use', 'label' => $label['cascade_air_use'], 'tooltip' => $tooltip['cascade_air_use']]]);
                                echo $this->Form->control('water_cooled_lab_equipment', ['templateVars' => ['id' => 'water_cooled_lab_equipment', 'label' => $label['water_cooled_lab_equipment'], 'tooltip' => $tooltip['water_cooled_lab_equipment']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>

                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("Controls"); ?></h3>

                                <?php
                                $this->Form->setTemplates($yesNoToggleTemplate);
                                echo $this->Form->control('supply_air_temperature_reset', ['templateVars' => ['id' => 'supply_air_temperature_reset', 'label' => $label['supply_air_temperature_reset'], 'tooltip' => $tooltip['supply_air_temperature_reset']]]);
                                echo $this->Form->control('supply_static_pressure_reset', ['templateVars' => ['id' => 'supply_static_pressure_reset', 'label' => $label['supply_static_pressure_reset'], 'tooltip' => $tooltip['supply_static_pressure_reset']]]);
                                echo $this->Form->control('exhaust_static_pressure_reset', ['templateVars' => ['id' => 'exhaust_static_pressure_reset', 'label' => $label['exhaust_static_pressure_reset'], 'tooltip' => $tooltip['exhaust_static_pressure_reset']]]);
                                echo $this->Form->control('lab_unoccupied_airflow_setback', ['templateVars' => ['id' => 'lab_unoccupied_airflow_setback', 'label' => $label['lab_unoccupied_airflow_setback'], 'tooltip' => $tooltip['lab_unoccupied_airflow_setback']]]);
                                echo $this->Form->control('lab_unoccupied_temperature_setback', ['templateVars' => ['id' => 'lab_unoccupied_temperature_setback', 'label' => $label['lab_unoccupied_temperature_setback'], 'tooltip' => $tooltip['lab_unoccupied_temperature_setback']]]);
                                echo $this->Form->control('pump_head_reset', ['templateVars' => ['id' => 'pump_head_reset', 'label' => $label['pump_head_reset'], 'tooltip' => $tooltip['pump_head_reset']]]);
                                echo $this->Form->control('exhaust_fan_wind_speed_response', ['templateVars' => ['id' => 'exhaust_fan_wind_speed_response', 'label' => $label['exhaust_fan_wind_speed_response'], 'tooltip' => $tooltip['exhaust_fan_wind_speed_response']]]);
                                echo $this->Form->control('lab_chemical_sensing_airflow_response', ['templateVars' => ['id' => 'lab_chemical_sensing_airflow_response', 'label' => $label['lab_chemical_sensing_airflow_response'], 'tooltip' => $tooltip['lab_chemical_sensing_airflow_response']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default pull-left" data-dismiss="modal"><?= __("Cancel"); ?></button>
                <button class="btn btn-primary pull-right apply" type="submit" data-modal-id="buildingSystemPopup"
                        data-summary-id="buildingSystemSummary" data-id="buildingSystem"><?= __("Apply"); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Building Systems -->

<!-- Fume Hoods -->
<div class="modal modal-fullscreen fade" tabindex="-1" role="dialog" aria-labelledby="fumeHoodsModal"
     id="fumeHoodsPopup" aria-hidden="true">
    <div class="modal-dialog" id="forgotDialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only"><?= __("Cancel"); ?></span></button>
                <h2 class="modal-title" id="fumeHoodsModal"><?= __("Fume Hoods and Ventilation Rates"); ?></h2>
            </div>
            <div class="modal-body">
                <form id="fumeHoods">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("Number of Hoods"); ?></h3>
                                <?php
                                $this->Form->setTemplates($templateSlider);
                                echo $this->Form->control('number_of_ducted_fume_hoods', ['templateVars' => ['id' => 'number_of_ducted_fume_hoods', 'label' => $label['number_of_ducted_fume_hoods'], 'tooltip' => $tooltip['number_of_ducted_fume_hoods']]]);
                                echo $this->Form->control('number_of_filtering_fume_hoods', ['templateVars' => ['id' => 'number_of_filtering_fume_hoods', 'label' => $label['number_of_filtering_fume_hoods'], 'tooltip' => $tooltip['number_of_filtering_fume_hoods']]]);
                                echo $this->Form->control('total_fume_hood_length', ['templateVars' => ['id' => 'total_fume_hood_length', 'label' => $label['total_fume_hood_length'], 'tooltip' => $tooltip['total_fume_hood_length']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("Hood Design"); ?></h3>
                                <?php
                                $this->Form->setTemplates($templateSlider);
                                echo $this->Form->control('fume_hood_sash_height', ['templateVars' => ['id' => 'fume_hood_sash_height', 'label' => $label['fume_hood_sash_height'], 'tooltip' => $tooltip['fume_hood_sash_height']]]);
                                echo $this->Form->control('fume_hood_face_velocity', ['templateVars' => ['id' => 'fume_hood_face_velocity', 'label' => $label['fume_hood_face_velocity'], 'tooltip' => $tooltip['fume_hood_face_velocity']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                            <div class="slider-card">
                                <h3><?= __("Hood Controls"); ?></h3>
                                <?php
                                echo $predominant_fume_type_id;
                                $this->Form->setTemplates($yesNoToggleTemplate);
                                echo $this->Form->control('fume_hood_automatic_sash_closers', ['templateVars' => ['id' => 'fume_hood_automatic_sash_closers', 'label' => $label['fume_hood_automatic_sash_closers'], 'tooltip' => $tooltip['fume_hood_automatic_sash_closers']]]);
                                echo $this->Form->control('fume_hood_unoccupied_airflow_setback', ['templateVars' => ['id' => 'fume_hood_unoccupied_airflow_setback', 'label' => $label['fume_hood_unoccupied_airflow_setback'], 'tooltip' => $tooltip['fume_hood_unoccupied_airflow_setback']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("Ventilation Rates"); ?></h3>
                                <?php
                                $this->Form->setTemplates($templateSlider);
                                echo $this->Form->control('occupied_required_air_change_rate', ['templateVars' => ['id' => 'occupied_required_air_change_rate', 'label' => $label['occupied_required_air_change_rate'], 'tooltip' => $tooltip['occupied_required_air_change_rate']]]);
                                echo $this->Form->control('unoccupied_required_air_change_rate', ['templateVars' => ['id' => 'unoccupied_required_air_change_rate', 'label' => $label['unoccupied_required_air_change_rate'], 'tooltip' => $tooltip['unoccupied_required_air_change_rate']]]);
                                $this->Form->setTemplates($yesNoToggleTemplate);
                                echo $this->Form->control('hazard_assessment_minimum_air_change_rate', ['templateVars' => ['id' => 'hazard_assessment_minimum_air_change_rate', 'label' => $label['hazard_assessment_minimum_air_change_rate'], 'tooltip' => $tooltip['hazard_assessment_minimum_air_change_rate']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default pull-left" data-dismiss="modal"><?= __("Cancel"); ?></button>
                <button class="btn btn-primary pull-right apply" type="submit" data-modal-id="fumeHoodsPopup"
                        data-summary-id="fumeHoodsSummary" data-id="fumeHoods"><?= __("Apply"); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Fume Hoods -->

<!-- Lab Types -->
<div class="modal modal-fullscreen fade" tabindex="-1" role="dialog" aria-labelledby="labTypesModal"
     id="labTypesPopup" aria-hidden="true">
    <div class="modal-dialog" id="forgotDialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only"><?= __("Cancel"); ?></span></button>
                <h2 class="modal-title" id="labTypesModal"><?= __("Lab Types"); ?></h2>
            </div>
            <div class="modal-body">
                <form id="labTypes">
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("Lab Type: % of total lab area"); ?></h3>
                                <?php
                                echo $this->Form->control('use_sliders', ['templateVars' => ['id' => 'use_sliders', 'label' => __("Use Sliders (Overrides selections in Basic Filters)")]]);

                                $this->Form->setTemplates($templateSlider);
                                echo $this->Form->control('biological_lab_area', ['templateVars' => ['id' => 'biological_lab_area', 'label' => $label['biological_lab_area'], 'tooltip' => $tooltip['biological_lab_area']]]);
                                echo $this->Form->control('chemical_lab_area', ['templateVars' => ['id' => 'chemical_lab_area', 'label' => $label['chemical_lab_area'], 'tooltip' => $tooltip['chemical_lab_area']]]);
                                echo $this->Form->control('physical_lab_area', ['templateVars' => ['id' => 'physical_lab_area', 'label' => $label['physical_lab_area'], 'tooltip' => $tooltip['physical_lab_area']]]);
                                echo $this->Form->control('vivarium_area', ['templateVars' => ['id' => 'vivarium_area', 'label' => $label['vivarium_area'], 'tooltip' => $tooltip['vivarium_area']]]);
                                echo $this->Form->control('dry_lab_area', ['templateVars' => ['id' => 'dry_lab_area', 'label' => $label['dry_lab_area'], 'tooltip' => $tooltip['dry_lab_area']]]);
                                echo $this->Form->control('other_lab_area', ['templateVars' => ['id' => 'other_lab_area', 'label' => $label['other_lab_area'], 'tooltip' => $tooltip['other_lab_area']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("Specialty Equipment"); ?></h3>
                                <?php
                                $this->Form->setTemplates($templateSlider);
                                echo $this->Form->control('data_center_load', ['templateVars' => ['id' => 'data_center_load', 'label' => $label['data_center_load'], 'tooltip' => $tooltip['data_center_load']]]);
                                echo $this->Form->control('ult_freezers', ['templateVars' => ['id' => 'ult_freezers', 'label' => $label['ult_freezers'], 'tooltip' => $tooltip['ult_freezers']]]);
                                $this->Form->setTemplates($yesNoToggleTemplate);
                                echo $this->Form->control('imag_equipment', ['templateVars' => ['id' => 'imag_equipment', 'label' => $label['imag_equipment'], 'tooltip' => $tooltip['imag_equipment']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="slider-card">
                                <h3><?= __("Specialty Lab Types"); ?></h3>
                                <?php
                                $this->Form->setTemplates($yesNoToggleTemplate);
                                echo $this->Form->control('biosafety_lab_area', ['templateVars' => ['id' => 'biosafety_lab_area', 'label' => $label['biosafety_lab_area'], 'tooltip' => $tooltip['biosafety_lab_area']]]);
                                echo $this->Form->control('cleanroom_iso5_area', ['templateVars' => ['id' => 'cleanroom_iso5_area', 'label' => $label['cleanroom_iso5_area'], 'tooltip' => $tooltip['cleanroom_iso5_area']]]);
                                echo $this->Form->control('cleanroom_iso6_area', ['templateVars' => ['id' => 'cleanroom_iso6_area', 'label' => $label['cleanroom_iso6_area'], 'tooltip' => $tooltip['cleanroom_iso6_area']]]);
                                echo $this->Form->control('cleanroom_iso7_area', ['templateVars' => ['id' => 'cleanroom_iso7_area', 'label' => $label['cleanroom_iso7_area'], 'tooltip' => $tooltip['cleanroom_iso7_area']]]);
                                $this->Form->setTemplates($bigInputCheckbox);
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default pull-left" data-dismiss="modal"><?= __("Cancel"); ?></button>
                <button class="btn btn-primary pull-right apply" type="submit" data-modal-id="labTypesPopup"
                        data-summary-id="labTypesSummary" data-id="labTypes"><?= __("Apply"); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Lab Types -->
<script>
    var logged_in = <?php if ($this->Session->check('Auth.User')) {
        echo "true";
    } else {
        echo "false";
    };?>;
</script>

<?= $this->Html->script('charts.js'); ?>
<?= $this->Html->script('bootstrap-tagsinput.min.js'); ?>
<?= $this->Html->script('filters'); ?>
