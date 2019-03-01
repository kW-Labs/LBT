<?= $this->Html->css('building.css'); ?>
<?= $this->Html->css('pretty-checkbox.min.css'); ?>
<?= $this->Html->css('charts.css'); ?>

<div id="banner2">
    <img src="/img/lbt_title.png" class="lbt_title" alt="LBT">
</div>

<a href="<?= $this->Url->build(["controller" => "users", "controller" => "buildings"]); ?>"
   class="btn btn-default pull-right mt-0"><?= __("Cancel"); ?></a>
<?php if (!empty($building)): ?>
    <h2 class="m-2"><?= __("New Data Year"); ?></h2>
<?php else: ?>
    <h2 class="m-2"><?= __("New Building"); ?></h2>
<?php endif; ?>

<?php

if (!isset($label)) {
    $label = [];
}

if (!isset($mm)) {
    $mm = [];
}

if (!isset($control)) {
    $control = [];
}

$this->Form->templates([
    'inputContainer' => '<div class="form-group row">
                             <label class="col-md-4 control-label" for="{{id}}">{{label}}</label>
                             <div class="col-md-4">
                            <span class="hidden pull-right clear" id="{{id}}Link"
                               data-id="{{id}}"><i class="glyphicon glyphicon-remove"></i> ' . __("clear") . '</span>

                            <div class="yes_no_toggle"  data-toggle="tooltip" data-placement="top" title="{{tooltip}}">
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
                            </div>
                        </div>'
]);

$fume_hood_automatic_sash_closers = $this->Form->control('fume_hood_automatic_sash_closers', $control['fume_hood_automatic_sash_closers']);
$fume_hood_unoccupied_airflow_setback = $this->Form->control('fume_hood_unoccupied_airflow_setback', $control['fume_hood_unoccupied_airflow_setback']);
$supply_air_temperature_reset = $this->Form->control('supply_air_temperature_reset', $control['supply_air_temperature_reset']);
$supply_static_pressure_reset = $this->Form->control('supply_static_pressure_reset', $control['supply_static_pressure_reset']);
$exhaust_static_pressure_reset = $this->Form->control('exhaust_static_pressure_reset', $control['exhaust_static_pressure_reset']);
$lab_unoccupied_airflow_setback = $this->Form->control('lab_unoccupied_airflow_setback', $control['lab_unoccupied_airflow_setback']);
$lab_unoccupied_temperature_setback = $this->Form->control('lab_unoccupied_temperature_setback', $control['lab_unoccupied_temperature_setback']);
$pump_head_reset = $this->Form->control('pump_head_reset', $control['pump_head_reset']);
$exhaust_fan_wind_speed_response = $this->Form->control('exhaust_fan_wind_speed_response', $control['exhaust_fan_wind_speed_response']);
$lab_chemical_sensing_airflow_response = $this->Form->control('lab_chemical_sensing_airflow_response', $control['lab_chemical_sensing_airflow_response']);
$geothermal_heat_pump = $this->Form->control('geothermal_heat_pump', $control['geothermal_heat_pump']);
$heat_recovery_chiller = $this->Form->control('heat_recovery_chiller', $control['heat_recovery_chiller']);
$air_side_low_pressure_drop_design = $this->Form->control('air_side_low_pressure_drop_design', $control['air_side_low_pressure_drop_design']);
$water_side_low_pressure_drop_design = $this->Form->control('water_side_low_pressure_drop_design', $control['water_side_low_pressure_drop_design']);
$true_variable_air_volume_exhaust = $this->Form->control('true_variable_air_volume_exhaust', $control['true_variable_air_volume_exhaust']);
$high_efficiency_ultra_low_temperature_freezers = $this->Form->control('high_efficiency_ultra_low_temperature_freezers', $control['high_efficiency_ultra_low_temperature_freezers']);
$cascade_air_use = $this->Form->control('cascade_air_use', $control['cascade_air_use']);
$water_cooled_lab_equipment = $this->Form->control('water_cooled_lab_equipment', $control['water_cooled_lab_equipment']);
$hazard_assessment_minimum_air_change_rate = $this->Form->control('hazard_assessment_minimum_air_change_rate', $control['hazard_assessment_minimum_air_change_rate']);
$imag_equipment = $this->Form->control('imag_equipment', $control['imag_equipment']);

$this->Form->templates([
    'inputContainer' => '<div class="form-group row">
                                        <label class="col-md-4 control-label" for="{{name}}">{{label}}</label>
                                        <div class="col-md-3">                                          
                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon {{icon}}" data-toggle="tooltip" data-placement="left" title="{{title}}"></span>
                                                </span>
                                                <input id="{{id}}" name="{{id}}"
                                                       type="number" class="form-control input-md" min="0"
                                                       {{required}}>
                                             </div>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control bigger" id="{{id}}_units"
                                                    name="{{id}}_units" >
                                                {{option1}}
                                                {{option2}}
                                                {{option3}}
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="row">
                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="{{id}}_measured" value="true" checked id="{{id}}_measured_measured">
                                                    <div class="state p-warning">
                                                        <label>Measured</label>
                                                    </div>
                                                </div>

                                                <div class="pretty p-default p-round">
                                                    <input type="radio" name="{{id}}_measured" value="false" id="{{id}}_measured_estimated">
                                                    <div class="state p-warning">
                                                        <label>Estimated</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>'
]);


$annual_electric_use = $this->Form->control('annual_electric_use', $control['annual_electric_use']);
$natural_gas_eui = $this->Form->control('natural_gas_eui', $control['natural_gas_eui']);
$fuel_oil_eui = $this->Form->control('fuel_oil_eui', $control['fuel_oil_eui']);
$other_fuel_eui = $this->Form->control('other_fuel_eui', $control['other_fuel_eui']);
$district_chilled_water_eui = $this->Form->control('district_chilled_water_eui', $control['district_chilled_water_eui']);
$district_hot_water_eui = $this->Form->control('district_hot_water_eui', $control['district_hot_water_eui']);
$district_steam_eui = $this->Form->control('district_steam_eui', $control['district_steam_eui']);
$water_use_intensity = $this->Form->control('water_use_intensity', $control['water_use_intensity']);

$this->Form->templates([
    'inputContainer' => '<div class="form-group {{type}}{{required}}">{{content}}</div>',
    'select' => '<div class="col-md-8"><div class="input-group"><span class="input-group-addon"><span class="glyphicon {{attrs.icon}}" data-toggle="tooltip" data-placement="left" title="{{attrs.title}}"></span></span><select class="form-control" name="{{name}}" {{attrs}}>{{content}}</select></div></div>',
    'input' => '<div class="col-md-8"><div class="input-group"><span class="input-group-addon"><span class="glyphicon {{attrs.icon}}" data-toggle="tooltip" data-placement="left" title="{{attrs.title}}"></span></span><input class="form-control"  name="{{name}}" type="{{type}}" {{attrs}}/></div></div>'
]);

$active_utility = isset($building) ? ' active' : '';
$building_details = isset($building) ? '' : ' active';
?>

<div class="card">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?= $building_details; ?>"><a href="#buildingDetailsTab"
                                                                     aria-controls="buildingDetailsTab"
                                                                     role="tab"
                                                                     data-toggle="tab"><?= __("Building Details"); ?>
                <i id="buildingDetailsTabAlert" class="text-danger glyphicon glyphicon-alert hidden"></i> </a>
        </li>
        <li role="presentation"><a href="#labAreaTab" aria-controls="labAreaTab" role="tab"
                                   data-toggle="tab"><?= __("Lab Area"); ?> <i id="labAreaTabAlert"
                                                                               class="text-danger glyphicon glyphicon-alert hidden"></i>
            </a></li>
        <li role="presentation"><a href="#buildingSystemTab" aria-controls="buildingSystemTab" role="tab"
                                   data-toggle="tab"><?= __("Building Systems"); ?> <i id="buildingSystemTabAlert"
                                                                                       class="text-danger glyphicon glyphicon-alert hidden"></i>
            </a></li>
        <li role="presentation" class="<?= $active_utility; ?>"><a href="#utilityUsageTab"
                                                                   aria-controls="utilityUsageTab" role="tab"
                                                                   data-toggle="tab"><?= __("Utility Usage"); ?> <i
                        id="utilityUsageTabAlert" class="text-danger glyphicon glyphicon-alert hidden"></i> </a>
        </li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?= $building_details; ?>" id="buildingDetailsTab">
            <div class="seminor-login-form">
                <form class="form-horizontal" id="buildingDetails" data-next-id="labAreaTab" novalidate>
                    <?php
                    echo $this->Form->control('name', $control['name']);
                    echo $this->Form->control('year_built', $control['year_built']);
                    echo $this->Form->control('existing_building', $control['existing_building']);
                    echo $this->Form->control('location', $control['location']);
                    echo $this->Form->control('state', $control['state']);
                    echo $this->Form->control('zip_code', $control['zip_code']);
                    echo $this->Form->control('organization_type', $control['organization_type']);
                    echo $this->Form->control('lab_type', $control['lab_type']);
                    echo $this->Form->control('number_of_people', $control['number_of_people']);
                    echo $this->Form->control('operating_hours', $control['operating_hours']);
                    ?>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary next"><?= __("Next"); ?></button>
                    </div>

                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="labAreaTab">
            <div class="seminor-login-form">
                <form class="form-horizontal" id="labArea" data-next-id="buildingSystemTab" novalidate>

                    <?php
                    echo $this->Form->control('number_of_buildings', $control['number_of_buildings']);
                    echo $this->Form->control('floor_area', $control['floor_area']);
                    echo $this->Form->control('net_floor_area', $control['net_floor_area']);
                    echo $this->Form->control('total_lab_area', $control['total_lab_area']);
                    ?>

                    <div class="well mb-2">
                        <div class="pull-right mt-2 mr-3"><?= __("Lab Area Not yet Assigned: "); ?><u
                                    id="not_assigned">0</u><?= __(" sf"); ?>
                        </div>

                        <h4 class="mb-3"><?= __("Component Lab Areas"); ?>
                            <i class="glyphicon glyphicon-info-sign"
                               data-toggle="tooltip"
                               data-placement="top"
                               title="Enter breakdown of lab space between different types of lab. Must add up to the total lab area entered above."></i>
                        </h4>

                        <?php
                        echo $this->Form->control('biological_lab_area', $control['biological_lab_area']);
                        echo $this->Form->control('chemical_lab_area', $control['chemical_lab_area']);
                        echo $this->Form->control('physical_lab_area', $control['physical_lab_area']);
                        echo $this->Form->control('vivarium_area', $control['vivarium_area']);
                        echo $this->Form->control('dry_lab_area', $control['dry_lab_area']);
                        echo $this->Form->control('other_lab_area', $control['other_lab_area']);
                        ?>
                    </div>

                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading bg-info" role="tab" id="headingTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed accordion-toggle" role="button" data-toggle="collapse"
                                       data-parent="#accordion"
                                       href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <span class="glyphicon glyphicon-plus"></span>
                                        <?= __("Specialty Lab Types"); ?> <i class="glyphicon glyphicon-info-sign"
                                                                             data-toggle="tooltip"
                                                                             data-placement="top"
                                                                             title="Enter areas of specialty lab types listed below, if present. These areas should ALSO be included in the Component Lab Areas entered above; areas entered under Specialty Lab Types will not be added to the Component Lab Areas."></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <?php
                                    echo $this->Form->control('biosafety_lab_area', $control['biosafety_lab_area']);
                                    echo $this->Form->control('cleanroom_iso5_area', $control['cleanroom_iso5_area']);
                                    echo $this->Form->control('cleanroom_iso6_area', $control['cleanroom_iso6_area']);
                                    echo $this->Form->control('cleanroom_iso7_area', $control['cleanroom_iso7_area']);
                                    echo $this->Form->control('ult_freezers', $control['ult_freezers']);
                                    echo $imag_equipment;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pull-left">
                        <button type="button" class="btn btn-secondary previous" data-next="building"
                                data-previous-id="buildingDetailsTab"><?= __("Previous"); ?>
                        </button>
                    </div>

                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary next"><?= __("Next"); ?></button>
                    </div>

                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="buildingSystemTab">
            <div class="seminor-login-form">
                <form class="form-horizontal" id="buildingSystem" data-next-id="utilityUsageTab" novalidate>

                    <?php
                    echo $this->Form->control('hvac_type', $control['hvac_type']);
                    echo $this->Form->control('hvac_control', $control['hvac_control']);
                    echo $this->Form->control('cooling', $control['cooling']);
                    echo $this->Form->control('heating', $control['heating']);
                    echo $this->Form->control('exhaust_air_energy_recovery', $control['exhaust_air_energy_recovery']);
                    echo $this->Form->control('building_level_combined_heat_power', $control['building_level_combined_heat_power']);
                    echo $this->Form->control('on_site_renewable_energy_type', $control['on_site_renewable_energy_type']);
                    echo $geothermal_heat_pump;
                    echo $heat_recovery_chiller;
                    echo $this->Form->control('data_center_load', $control['data_center_load']);
                    ?>

                    <div class="panel-group" id="accordionFumeHoods" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading bg-info" role="tab" id="headingThree">
                                <h4 class="panel-title">
                                    <a class="collapsed accordion-toggle" role="button" data-toggle="collapse"
                                       data-parent="#accordion"
                                       href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <span class="glyphicon glyphicon-minus"></span>
                                        <?= __("Fume Hoods and Ventilation Rates"); ?>
                                        <i class="glyphicon glyphicon-info-sign"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Enter data about the building's fume hoods and assigned ventilation rates."></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="headingThree">
                                <div class="panel-body">
                                    <?php
                                    echo $this->Form->control('number_of_ducted_fume_hoods', $control['number_of_ducted_fume_hoods']);
                                    echo $this->Form->control('number_of_filtering_fume_hoods', $control['number_of_filtering_fume_hoods']);
                                    echo $this->Form->control('total_fume_hood_length', $control['total_fume_hood_length']);
                                    echo $this->Form->control('fume_hood_sash_height', $control['fume_hood_sash_height']);
                                    echo $this->Form->control('fume_hood_face_velocity', $control['fume_hood_face_velocity']);
                                    echo $this->Form->control('fume_hood_control', $control['fume_hood_control']);
                                    echo $fume_hood_automatic_sash_closers;
                                    echo $fume_hood_unoccupied_airflow_setback;
                                    echo $this->Form->control('occupied_required_air_change_rate', $control['occupied_required_air_change_rate']);
                                    echo $this->Form->control('unoccupied_required_air_change_rate', $control['unoccupied_required_air_change_rate']);
                                    echo $hazard_assessment_minimum_air_change_rate;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-group" id="accordionSystemControls" role="tablist"
                         aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading bg-info" role="tab" id="accordionSystemControlsHeading">
                                <h4 class="panel-title">
                                    <a class="collapsed accordion-toggle" role="button" data-toggle="collapse"
                                       data-parent="#accordion"
                                       href="#accordionSystemControlsPanel" aria-expanded="false"
                                       aria-controls="accordionSystemControlsPanel">
                                        <span class="glyphicon glyphicon-plus"></span>
                                        <?= __("Building Controls Features"); ?>
                                        <i class="glyphicon glyphicon-info-sign"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Indicate whether the building has any of these other controls features."></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="accordionSystemControlsPanel" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="accordionSystemControlsHeading">
                                <div class="panel-body">
                                    <?php
                                    echo $supply_air_temperature_reset;
                                    echo $supply_static_pressure_reset;
                                    echo $exhaust_static_pressure_reset;
                                    echo $lab_unoccupied_airflow_setback;
                                    echo $lab_unoccupied_temperature_setback;
                                    echo $pump_head_reset;
                                    echo $exhaust_fan_wind_speed_response;
                                    echo $lab_chemical_sensing_airflow_response;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-group" id="accordionOther" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default">
                            <div class="panel-heading bg-info" role="tab" id="accordionOtherHeading">
                                <h4 class="panel-title">
                                    <a class="collapsed accordion-toggle" role="button" data-toggle="collapse"
                                       data-parent="#accordion"
                                       href="#accordionOtherPanel" aria-expanded="false"
                                       aria-controls="accordionOtherPanel">
                                        <span class="glyphicon glyphicon-plus"></span>
                                        <?= __("Other Design Features"); ?>
                                        <i class="glyphicon glyphicon-info-sign"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="Indicate whether the building has any of these other assorted design features."></i>
                                    </a>
                                </h4>
                            </div>
                            <div id="accordionOtherPanel" class="panel-collapse collapse" role="tabpanel"
                                 aria-labelledby="accordionOtherHeading">
                                <div class="panel-body">
                                    <?php
                                    echo $air_side_low_pressure_drop_design;
                                    echo $water_side_low_pressure_drop_design;
                                    echo $true_variable_air_volume_exhaust;
                                    echo $high_efficiency_ultra_low_temperature_freezers;
                                    echo $cascade_air_use;
                                    echo $water_cooled_lab_equipment;
                                    echo $this->Form->control('cooling_plant_capacity', $control['cooling_plant_capacity']);
                                    echo $this->Form->control('installed_lighting_intensity', $control['installed_lighting_intensity']);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pull-left">
                        <button type="button" class="btn btn-secondary previous"
                                data-previous-id="labAreaTab"><?= __("Previous"); ?>
                        </button>
                    </div>

                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary next"><?= __("Next"); ?>
                        </button>
                    </div>

                    <div class="clearfix"></div>
                </form>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane <?= $active_utility; ?>" id="utilityUsageTab">
            <div class="seminor-login-form">
                <?= $this->Form->create(false, ["id" => "utilityUsage", "class" => "form-horizontal", "novalidate" => true]); ?>
                <div class="panel-group" id="accordionEnergyUseOverall" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading bg-info" role="tab" id="headingFour">
                            <h4 class="panel-title">
                                <a class="collapsed accordion-toggle" role="button" data-toggle="collapse"
                                   data-parent="#accordion"
                                   href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    <span class="glyphicon glyphicon-minus"></span>
                                    <?= __("Overall Utility Usage and Cost"); ?>
                                    <i class="glyphicon glyphicon-info-sign"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="Enter whole-building energy usage and cost data."></i>
                                </a>
                            </h4>
                        </div>
                    </div>

                    <div id="collapseFour" class="panel-collapse collapse in" role="tabpanel"
                         aria-labelledby="headingFour">
                        <div class="panel-body">
                            <?php
                            echo $this->Form->control('year', $control['year']);
                            echo $annual_electric_use;
                            echo $natural_gas_eui;
                            echo $fuel_oil_eui;
                            echo $other_fuel_eui;
                            echo $district_chilled_water_eui;
                            echo $district_hot_water_eui;
                            echo $district_steam_eui;
                            ?>
                            <?php
                            echo $this->Form->control('total_utility_cost_intensity', $control['total_utility_cost_intensity']);
                            ?>
                            <div class="well">
                                <h4><?= __("Water usage"); ?></h4>
                                <?php
                                echo $water_use_intensity;
                                echo $this->Form->control('water_sewer_cost_intensity', $control['water_sewer_cost_intensity']);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-group" id="accordionEnergyUseOverall" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading bg-info" role="tab" id="headingFive">
                        <h4 class="panel-title">
                            <a class="collapsed accordion-toggle" role="button" data-toggle="collapse"
                               data-parent="#accordion"
                               href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <span class="glyphicon glyphicon-minus"></span>
                                <?= __("System Level Energy Usage and Related Data"); ?>
                                <i class="glyphicon glyphicon-info-sign"
                                   data-toggle="tooltip"
                                   data-placement="top"
                                   title="Breakdown of energy usage by building systems, plus some other system-level metrics."></i>
                            </a>
                        </h4>
                    </div>
                </div>

                <div id="collapseFive" class="panel-collapse collapse in" role="tabpanel"
                     aria-labelledby="headingFive">
                    <div class="panel-body">
                        <?php
                        echo $this->Form->control('peak_electric_demand_intensity', $control['peak_electric_demand_intensity']);
                        ?>
                        <div class="well">
                            <h4><?= __("System-Level Electricity Usage"); ?></h4>
                            <?php
                            echo $this->Form->control('process_plug_electric_eui', $control['process_plug_electric_eui']);
                            echo $this->Form->control('ventilation_electric_eui', $control['ventilation_electric_eui']);
                            echo $this->Form->control('cooling_plant_electric_eui', $control['cooling_plant_electric_eui']);
                            echo $this->Form->control('lighting_electric_eui', $control['lighting_electric_eui']);
                            echo $this->Form->control('on_site_renewable_electric_eui', $control['on_site_renewable_electric_eui']);
                            ?>
                        </div>

                        <div class="well">
                            <h4><?= __("System-Level Peak Demand Data"); ?></h4>
                            <?php
                            echo $this->Form->control('process_plug_peak_electric_demand_intensity', $control['process_plug_peak_electric_demand_intensity']);
                            echo $this->Form->control('ventilation_peak_electric_demand_intensity', $control['ventilation_peak_electric_demand_intensity']);
                            echo $this->Form->control('cooling_plant_peak_electric_demand_intensity', $control['cooling_plant_peak_electric_demand_intensity']);
                            echo $this->Form->control('lighting_peak_electric_demand_intensity', $control['lighting_peak_electric_demand_intensity']);
                            echo $this->Form->control('ventilation_peak_airflow', $control['ventilation_peak_airflow']);
                            echo $this->Form->control('cooling_plant_peak_load_intensity', $control['cooling_plant_peak_load_intensity']);
                            ?>
                        </div>
                    </div>
                </div>
            </div>

                <div class="pull-left">
                    <button type="button" class="btn btn-secondary previous"
                            data-previous-id="buildingSystemTab"><?= __("Previous"); ?>
                    </button>
                </div>

                <div class="pull-right">
                    <button type="submit" class="btn btn-success"><?= __("Submit"); ?></button>
                </div>

                <div class="clearfix"></div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if(!empty($building)):?>
    var values = <?=json_encode($building);?>;
    var invalid_years = <?= json_encode($myBuildingYears);?>;
    <?php endif;?>
</script>

<?= $this->Html->script('building_add.js'); ?>
