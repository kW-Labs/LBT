$(function () {

    var fields = [];
    var skipLabPopup = false;
    $.getJSON("/json/fields.json", function (data) {
        if ('fields' in data) {

            /** @type {data} */
            fields = data.fields;

            fields.forEach(function (fieldObj) {
                if (fieldObj.field_id === "year" || fieldObj.field_id === "year_built") {
                    fieldObj.max = new Date().getFullYear();
                }
            });

            if (typeof values !== 'undefined') {
                $.each(values, function (k, v) {

                    if (v !== null) {

                        if (k === "electric_eui_measured") {
                            k = "annual_energy_use_measured";
                        }

                        var type = getType(k);
                        var new_k = k.replace(/_/g, "-");

                        if (type === "booleanArray") {
                            if (v === "true") {
                                if ($("#" + new_k).length > 0) {
                                    toggleYesNo($('#' + new_k + "Happy"), true);
                                } else {
                                    toggleYesNo($('#' + k + "Happy"), true);
                                }
                            } else if (v === "false") {
                                if ($("#" + new_k).length > 0) {
                                    toggleYesNo($('#' + new_k + "Sad"), false);
                                } else {
                                    toggleYesNo($('#' + k + "Sad"), false);
                                }
                            }
                        } else {

                            var k_id = $("#" + new_k);
                            if (k_id.length > 0) {
                                k_id.val(v);
                            } else {
                                if (k.indexOf("_measured") >= 0) {
                                    var k_measured = $('#' + k + '_measured');
                                    if( k_measured.length >0) {
                                        if (v === "true") {
                                            k_measured.prop('checked', true);
                                        } else {
                                            $('#' + k + '_estimated').prop('checked', true);
                                        }
                                    }
                                } else {
                                    $('#' + k).val(v);
                                }

                            }
                        }

                    }
                });
            }
        }
    });

    function getType(id) {
        var type = '';
        var found = false;
        /** @namespace fieldObj */
        /** @namespace fieldObj.type */
        fields.forEach(function (fieldObj) {

            if (fieldObj.id === id || fieldObj.field_id === id) {
                if ('add_type' in fieldObj) {
                    type = fieldObj.add_type;
                } else {
                    type = fieldObj.type;
                }

                found = true;
                return true;
            }

            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.field_id === id || valObj.id === id) {
                        if ('add_type' in fieldObj) {
                            type = fieldObj.add_type;
                        } else {
                            type = fieldObj.type;
                        }

                        type = fieldObj.type;
                        found = true;
                        return true;
                    }
                });

                if (found) {
                    return true;
                }
            }

        });

        return type;
    }

    function goToTab(tab) {
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    }

    $("#buildingDetails, #labArea, #buildingSystem, #utilityUsage").submit(function (event) {

        var $formObj = {};
        var inputs = $(this).serializeArray();
        var id = $(this).attr('id');
        var next_id = $(this).attr('data-next-id');

        var checkDucted = false;
        if(id === "buildingSystem"){
            var ductValue = $('#number-of-ducted-fume-hoods').val();
            checkDucted = !(ductValue.length > 0 || ductValue.length == null);
            if(checkDucted) {
                var $c3 = $('#collapseThree');
                $c3.collapse('show');
            }

            checkBuildingSystemsTab();
        }

        if(id === "labArea"){
            checkLabAreaTab();
        }

        $.each(inputs, function (i, input) {
            var type = getType(input.name);

            if (input.value.length > 0) {
                if (type === "numeric") {
                    $formObj[input.name] = parseFloat(input.value);
                } else {
                    $formObj[input.name] = input.value;
                }
            }
        });

        // Trigger Colors on tab now
        if (!colorErrors[id]) {
            colorErrors[id] = true;
        }

        var results = 0;
        if (id === "utilityUsage" || id === "labArea") {
            results = checkRequiredFields([id], true);
        } else {
            results = checkRequiredFields([id]);
        }

        if (results === 0) {
            goToTab(next_id);
        }else{
            if(id !== "utilityUsage") {
                new PNotify({
                    title: 'Error',
                    text: 'Please fix error(s) on this tab'
                });
            }
            event.preventDefault();
        }

        if (id === "utilityUsage") {
            checkUtilityUsageTab();

            // Save other tabs
            var form_ids = ['buildingDetails', 'labArea', 'buildingSystem'];
            form_ids.forEach(function (form_id) {
                var inputs = $('#' + form_id).serializeArray();

                $.each(inputs, function (i, input) {
                    var type = getType(input.name);
                    if (input.value.length > 0) {
                        if (type === "numeric") {
                            $formObj[input.name] = parseFloat(input.value);
                        } else {
                            $formObj[input.name] = input.value;
                        }
                    }
                });

            });


            skipLabPopup = true;
            var validRequiredField = checkRequiredFields();

            if (validRequiredField === 0) {
                if (!('name' in $formObj)) {
                    $formObj['name'] = $('#name').val();
                }

                $('<input>').attr({
                    type: 'hidden',
                    id: 'data',
                    name: 'data',
                    value: JSON.stringify($formObj)
                }).appendTo('#utilityUsage');
            } else {
                event.preventDefault();
                new PNotify({
                    title: 'Error',
                    text: 'Fix error(s). Please check all tabs'
                });
            }
        } else {
            event.preventDefault();
        }

    });

    var floorArea = $('#floor-area');
    var netArea = $('#net-floor-area');
    var labArea = $('#total-lab-area');
    var biological = $('#biological-lab-area');
    var vivarium = $('#vivarium-area');
    var chemical = $('#chemical-lab-area');
    var physics = $('#physical-lab-area');
    var maker = $('#dry-lab-area');
    var other = $('#other-lab-area');
    var notAssigned = $('#not_assigned');
    var labAreaTabAlert = $('#labAreaTabAlert');
    var number_of_ducted_fume_hoods = $('#number-of-ducted-fume-hoods');
    var facility_name = $('#name');
    var year_built = $('#year-built');
    var zip_code = $('#zip-code');
    var operating_hours = $('#operating-hours');
    var year = $('#year');
    var annual_electric_use = $('#annual_electric_use');
    var natural_gas_eui = $('#natural_gas_eui');
    var fuel_oil_eui = $('#fuel_oil_eui');
    var other_fuel_eui = $('#other_fuel_eui');
    var district_chilled_water_eui = $('#district_chilled_water_eui');
    var district_hot_water_eui = $('#district_hot_water_eui');
    var district_steam_eui = $('#district_steam_eui');
    var colorErrors = {};

    if (typeof mode !== 'undefined') {
        colorErrors.buildingDetails = true;
        colorErrors.buildingSystem = true;
        colorErrors.labArea = true;
        colorErrors.utilityUsage = true;
    } else {
        colorErrors.buildingDetails = false;
        colorErrors.buildingSystem = false;
        colorErrors.labArea = false;
        colorErrors.utilityUsage = false;
    }

    floorArea.on('keyup input propertychange paste change', function () {
        checkLab($(this), false, false);
    });
    netArea.on('keyup input propertychange paste change', function () {
        checkLab($(this), true);
    });
    labArea.on('keyup input propertychange paste change', function () {
        checkLab($(this), true);
    });
    biological.on('keyup input propertychange paste change', function () {
        checkLab($(this));
    });
    vivarium.on('keyup input propertychange paste change', function () {
        checkLab($(this));
    });
    chemical.on('keyup input propertychange paste change', function () {
        checkLab($(this));
    });
    physics.on('keyup input propertychange paste change', function () {
        checkLab($(this));
    });
    maker.on('keyup input propertychange paste change', function () {
        checkLab($(this));
    });
    other.on('keyup input propertychange paste change', function () {
        checkLab($(this));
    });

    // Check input for change
    $(':input[type="number"]').on('keyup input propertychange paste change', function () {
        var input = {};
        input.id = $(this).attr('id');
        input.min = $(this).attr('min');
        input.max = $(this).attr('max');
        input.type = $(this).attr('type');
        input.value = $(this).val();
        checkInput(input);
    });

    facility_name.on('keyup input propertychange paste change', function () {
        if (colorErrors['buildingDetails']) {
            checkRequiredFields(['buildingDetails']);
        }
    });
    year_built.on('keyup input propertychange paste change', function () {
        if (colorErrors['buildingDetails']) {
            checkRequiredFields(['buildingDetails']);
        }
    });
    zip_code.on('keyup input propertychange paste change', function () {
        if (colorErrors['buildingDetails']) {
            checkRequiredFields(['buildingDetails']);
        }
    });
    operating_hours.on('keyup input propertychange paste change', function () {
        if (colorErrors['buildingDetails']) {
            checkRequiredFields(['buildingDetails'],false);
        }
    });
    number_of_ducted_fume_hoods.on('keyup input propertychange paste change', function () {
        if (colorErrors['buildingSystem']) {
            checkRequiredFields(['buildingSystem']);
        }
    });
    year.on('keyup input propertychange paste change', function () {
        if ($(this).val().length === 4) {
            checkYears($(this).val());
        }
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });
    annual_electric_use.on('keyup input propertychange paste change', function () {
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });
    natural_gas_eui.on('keyup input propertychange paste change', function () {
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });
    fuel_oil_eui.on('keyup input propertychange paste change', function () {
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });
    other_fuel_eui.on('keyup input propertychange paste change', function () {
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });
    district_chilled_water_eui.on('keyup input propertychange paste change', function () {
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });
    district_hot_water_eui.on('keyup input propertychange paste change', function () {
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });
    district_steam_eui.on('keyup input propertychange paste change', function () {
        if (colorErrors['utilityUsage']) {
            checkRequiredFields(['utilityUsage']);
        }
    });

    function checkLab($this, skipLabArea, checkGrossAreaLab) {
        skipLabArea = skipLabArea || false;
        checkGrossAreaLab = checkGrossAreaLab || false;
        var response = checkFloorAreas(checkGrossAreaLab);
        if (response) {
            if (skipLabArea) {
                var totalLabArea = labArea.val() !== "" ? parseInt(labArea.val()) : 0;
                var floorArea_val = floorArea.val() !== "" ? parseInt(floorArea.val()) : 0;
                if (totalLabArea > floorArea_val) {
                    $this.parent().parent().addClass('has-error');
                }
            } else {
                if ($this.attr('id') !== "total-lab-area") {
                    $this.parent().parent().addClass('has-error');
                } else {
                    if ($this.val() === "") {
                        $this.parent().parent().addClass('has-error');
                    }
                }
            }
        } else {
            $this.parent().parent().removeClass('has-error');
            if (typeof mode !== 'undefined') {
                if (!checkGrossAreaLab) {
                    checkRequiredFields(['labArea'], true);
                } else {
                    checkRequiredFields(['labArea']);
                }
            }
        }
    }

    function checkYears(val) {
        if (typeof invalid_years !== 'undefined') {
            if (jQuery.inArray(parseInt(val), invalid_years) !== -1) {
                new PNotify({
                    title: 'Error',
                    text: val + ' is already used. Please use another building year.'
                });

                year.val("");
            }
        }
    }

    var showNetFloorError = false;
    var showGrossFloorError = false;
    function checkFloorAreas(checkGrossAreaLab) {
        checkGrossAreaLab = checkGrossAreaLab || false;
        var totalLabArea = labArea.val() !== "" ? parseInt(labArea.val()) : 0;
        var biological_val = biological.val() !== "" ? parseInt(biological.val()) : 0;
        var chemical_val = chemical.val() !== "" ? parseInt(chemical.val()) : 0;
        var physics_val = physics.val() !== "" ? parseInt(physics.val()) : 0;
        var vivarium_val = vivarium.val() !== "" ? parseInt(vivarium.val()) : 0;
        var maker_val = maker.val() !== "" ? parseInt(maker.val()) : 0;
        var other_val = other.val() !== "" ? parseInt(other.val()) : 0;
        var floorArea_val = floorArea.val() !== "" ? parseInt(floorArea.val()) : 0;
        var netArea_val = floorArea.val() !== "" ? parseInt(netArea.val()) : 0;
        var componentTotalLabArea = biological_val + chemical_val + physics_val + maker_val + vivarium_val + other_val;
        var diff = parseInt(totalLabArea - componentTotalLabArea);
        var error = 0;
        var $floor_area = $('#floor-area');
        var $net_floor_area = $('#net-floor-area');
        var $total_floor_area = $('#total-lab-area');

        notAssigned.html(diff);

        $lab_area_error = false;
        if (totalLabArea > floorArea_val) {
            if (checkGrossAreaLab) {
                labAreaTabAlert.removeClass('hidden');
                if(!showGrossFloorError) {
                    showGrossFloorError = true;
                    new PNotify({
                        title: 'Error',
                        text: 'Lab Area must be less than Gross Area!'
                    });
                }

                setTimeout(function(){
                    showGrossFloorError = false;
                }, 1500);

            }
            $lab_area_error = true;
            $floor_area.parent().parent().addClass('has-error');
            error++;
        }

        min = parseInt($floor_area.attr('min'));
        max = parseInt($floor_area.attr('max'));
        if ($floor_area.val() < min || $floor_area.val() > max) {
            $floor_area.parent().parent().addClass('has-error');
            if(!$lab_area_error){
                error++;
            }
        }

        $net_area_error = false;
        if (floorArea_val < netArea_val) {
            if (checkGrossAreaLab) {
                labAreaTabAlert.removeClass('hidden');
                if(!showNetFloorError) {
                    showNetFloorError = true;
                    new PNotify({
                        title: 'Error',
                        text: 'Net Floor Area must be less than Gross Area!'
                    });
                }

                setTimeout(function(){
                    showNetFloorError = false;
                }, 1500);
            }
            $net_area_error = true;
            $net_floor_area.parent().parent().addClass('has-error');
            error++;
        }

        min = parseInt($net_floor_area.attr('min'));
        max = parseInt($net_floor_area.attr('max'));
        if($net_floor_area.val() !== "") {
            if ($net_floor_area.val() < min || $floor_area.val() > max) {
                $net_floor_area.parent().parent().addClass('has-error');
                if (!$net_area_error) {
                    error++;
                }


            }
        }

        $total_floor_error = false;
        if (componentTotalLabArea === 0) {
            if (checkGrossAreaLab) {
                new PNotify({
                    title: 'Error',
                    text: 'At least one component lab area must be entered.'
                });
            }
            $total_floor_error = true;
            error++;
        }

        var min = parseInt($total_floor_area.attr('min'));
        var max = parseInt($total_floor_area.attr('max'));
        if ($total_floor_area.val() < min || $total_floor_area.val() > max) {
            $total_floor_area.parent().parent().addClass('has-error');
            if(!$total_floor_error){
                error++;
            }
        }

        if(error === 0){
            $floor_area.parent().parent().removeClass('has-error');
            $net_floor_area.parent().parent().removeClass('has-error');
        }

        if (diff < 0 || diff !== 0) {
            notAssigned.addClass('text-danger');
            if (labAreaTabAlert.hasClass('hidden')) {
                labAreaTabAlert.removeClass('hidden');
            }
            if (checkGrossAreaLab) {
                new PNotify({
                    title: 'Error',
                    text: 'Lab Area Not yet Assigned Must Equal 0.'
                });
            }
            error++;
        } else {
            notAssigned.removeClass('text-danger');
        }

        if(error === 0){
            labAreaTabAlert.addClass('hidden');
        }

        return error;
    }

    function checkInput(input) {
        var id = input.id;
        var type = input.type;
        var val = input.value;
        if (id.length ) {
            var $id = $('#' + id);
            if (type === "number") {
                if (val !== "") {
                    val = parseInt(val);

                    var myFields = ['floor-area', 'total-lab-area','net-floor-area'];
                    if (jQuery.inArray(id, myFields) < 0) {

                        if (!$.isNumeric(val)) {
                            $id.parent().parent().addClass('has-error');
                            return true;
                        } else {
                            if (typeof input.min !== 'undefined') {
                                if (!isNaN(parseInt(input.min))) {
                                    var min = parseInt(input.min);
                                    var max = parseInt(input.max);
                                    if (val < min || val > max) {
                                        $id.parent().parent().addClass('has-error');
                                        return true;
                                    } else {
                                        if ($id.parent().parent().hasClass('has-error')) {
                                           $id.parent().parent().removeClass('has-error');
                                        }
                                    }
                                }
                            }
                        }
                    }

                }else{
                    if ($id.parent().parent().hasClass('has-error')) {
                        $id.parent().parent().removeClass('has-error');
                    }
                }

            }

            if (id === "zip-code") {

                if(val.match(/^\d+$/)) {

                    if (val.length !== 5) {
                        $id.parent().parent().addClass('has-error');
                        return true;
                    } else {
                        if ($id.parent().parent().hasClass('has-error')) {
                            $id.parent().parent().removeClass('has-error');
                        }
                    }
                }else{
                    $id.parent().parent().addClass('has-error');
                    return true;
                }
            }
        }

        return false;

    }

    function checkRequiredFields(form_ids, skipLabPopup) {
        form_ids = form_ids || ['buildingDetails', 'labArea', 'buildingSystem', 'utilityUsage'];
        skipLabPopup = skipLabPopup || false;
        var totalRequiredFieldCount = 0;
        var floorAreaErrors = 0;
        if (skipLabPopup) {
            floorAreaErrors = checkFloorAreas(true);
        } else {
            floorAreaErrors = checkFloorAreas();
        }

        form_ids.forEach(function (form_id) {
            // Check all Required fields
            var inputs = document.querySelectorAll('#' + form_id + ' input');
            var requiredFields = [];

            for (var i = 0; i < inputs.length; i++) {
                var field = inputs[i];
                if (field.hasAttribute('required')) {
                    requiredFields.push(field);
                }
            }

            var allFields = [];
            for (var x = 0; x < inputs.length; x++) {
                var my_field = inputs[x];
                allFields.push(my_field);
            }

            var requiredFieldCount = 0;

            requiredFields.forEach(function (input) {
                var id = input.id;
                var $id = $('#' + id);
                if (input.value === "") {
                    $id.parent().parent().addClass('has-error');
                    requiredFieldCount++;
                } else {
                    if ($id.parent().parent().hasClass('has-error')) {
                        if(floorAreaErrors === 0) {
                            $id.parent().parent().removeClass('has-error');
                        }
                    }
                }
            });

            allFields.forEach(function (input) {
                if (checkInput(input)) {
                    requiredFieldCount++;
                }
            });

            if (form_id === "labArea") {
                requiredFieldCount += floorAreaErrors;
            }

            if (requiredFieldCount > 0) {

                if(form_ids.length === 1 && form_id === "labArea"){
                    checkLabAreaTab();
                }

                if(form_ids.length === 1 && form_id === "buildingSystemTab"){
                    checkBuildingSystemsTab();
                }

                totalRequiredFieldCount += requiredFieldCount;
                $('#' + form_id + 'TabAlert').removeClass('hidden');
            } else {
                $('#' + form_id + 'TabAlert').addClass('hidden');
            }

        });

        return totalRequiredFieldCount;
    }

    $(".previous").click(function () {
        var tab = $(this).attr('data-previous-id');
        goToTab(tab);
    });


    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if(target === "#buildingSystemTab") {
            checkBuildingSystemsTab()
        }

        if(target === "#labAreaTab") {
            checkLabAreaTab();
        }

        if(target === "#utilityUsageTab") {
            checkUtilityUsageTab();
        }
    });

    function checkBuildingSystemsTab(){
        var form_ids= ['total-fume-hood-length','number-of-filtering-fume-hoods','number-of-ducted-fume-hoods','fume-hood-sash-height','fume-hood-face-velocity','unoccupied-required-air-change-rate','occupied-required-air-change-rate'];
        var c3_opened = false;
        form_ids.forEach(function (form_id) {
            var $id = $('#' + form_id);
            var val = $id.val();
            var min = parseInt($id.attr('min'));
            var max = parseInt($id.attr('max'));
            var check = !(val.length > 0 || val.length == null);
            if (!check) {
                check = check ? true : !(val >= min && val <= max);
                if (check) {
                    if (!c3_opened) {
                        $('#collapseThree').collapse('show');
                        c3_opened = true;
                    }
                }
            }
        });

        var form_ids2= ['installed-lighting-intensity','cooling-plant-capacity'];
        var accordionOtherPanel_open = false;
        form_ids2.forEach(function (form_id) {
            var $id = $('#' + form_id);
            var val = $id.val();
            var min = parseInt($id.attr('min'));
            var max = parseInt($id.attr('max'));
            var check2 = !(val.length > 0 || val.length == null);
            if(!check2) {
                check2 = check2 ? true : !(val >= min && val <= max);
                if (check2) {
                    if (!accordionOtherPanel_open) {
                        $('#accordionOtherPanel').collapse('show');
                        accordionOtherPanel_open = true;
                    }
                }
            }
        });
    }

    function checkLabAreaTab(){
        var form_ids= ['biosafety-lab-area','cleanroom-iso5-area','cleanroom-iso6-area','cleanroom-iso7-area','ult-freezers'];
        var c2_opened = false;
        form_ids.forEach(function (form_id) {
            var $id = $('#' + form_id);
            var val = $id.val();
            var min = parseInt($id.attr('min'));
            var max = parseInt($id.attr('max'));
            var check = !(val.length > 0 || val.length == null);

            if(!check) {
                check = check ? true : !(val >= min && val <= max);
                if (check) {
                    if (!c2_opened) {
                        $('#collapseTwo').collapse('show');
                        c2_opened = true;
                    }
                }
            }
        });
    }

    function checkUtilityUsageTab(){
        var form_ids= ['annual_electric_use','annual_electric_use','natural_gas_eui','fuel_oil_eui','other_fuel_eui','district_chilled_water_eui','district_chilled_water_eui','district_steam_eui','total-utility-cost-intensity','water_use_intensity','water-sewer-cost-intensity'];
        var c4_opened = false;
        form_ids.forEach(function (form_id) {
            var $id = $('#' + form_id);
            var val = $id.val();
            var min = parseInt($id.attr('min'));
            var max = parseInt($id.attr('max'));
            var check = !(val.length > 0 || val.length == null);
            if (!check) {
                check = check ? true : !(val >= min && val <= max);
                if (check) {
                    if (!c4_opened) {
                        $('#collapseFour').collapse('show');
                        c4_opened = true;
                    }
                }
            }
        });

        var form_ids2= ['peak-electric-demand-intensity','process-plug-electric-eui','ventilation-electric-eui','cooling-plant-electric-eui','lighting-electric-eui','on-site-renewable-electric-eui','process-plug-peak-electric-demand-intensity','ventilation-peak-electric-demand-intensity','cooling-plant-peak-electric-demand-intensity','lighting-peak-electric-demand-intensity','ventilation-peak-airflow','cooling-plant-peak-load-intensity'];
        var c5_opened = false;
        form_ids2.forEach(function (form_id) {
            var $id = $('#' + form_id);
            var val = $id.val();
            var min = parseInt($id.attr('min'));
            var max = parseInt($id.attr('max'));
            var check2 = !(val.length > 0 || val.length == null);
            if(!check2) {
                check2 = check2 ? true : !(val >= min && val <= max);
                if (check2) {
                    if (!c5_opened) {
                        $('#collapseFive').collapse('show');
                        c5_opened = true;
                    }
                }
            }
        });
    }

    $('[data-toggle="tooltip"]').tooltip();

    var $clear = $(".clear");
    var $icon = $(".toggle_icon");
    var clearIds = [];

    function toggleYesNo($this, setTrue) {
        setTrue = setTrue || false;
        var id = $this.data('id');
        var no = $('#' + id + 'Sad');
        var yes = $('#' + id + 'Happy');
        var yes_input = $('#' + id + 'Yes');
        var no_input = $('#' + id + 'No');
        var link = $('#' + id + 'Link');
        var toggle = $('#' + id + 'Toggle');
        var condition;

        if (setTrue) {
            condition = true;
        } else {
            condition = $this.hasClass("happy");
        }

        if (condition) {
            link.removeClass('hidden');
            no.removeClass("selected");
            yes.addClass("selected");
            toggle.removeClass("sad");
            toggle.addClass("happy");
            yes_input.prop("checked", "checked");
            $('#' + id).val(true);

            toggle.animate({
                left: "0px"
            }, {
                queue: false,
                ease: 'easeInSine'
            });
        } else {
            link.removeClass('hidden');
            no_input.prop("checked", "checked");
            no.addClass("selected");
            yes.removeClass("selected");
            toggle.addClass("sad");
            toggle.removeClass("happy");
            $('#' + id).val(false);

            toggle.animate({
                left: "56px"
            }, {
                queue: false,
                ease: 'easeInSine'
            });
        }
    }

    $icon.on("click", function () {
        toggleYesNo($(this));
    });

    $clear.on("click", function () {
        var $this = $(this);
        var id = $this.data('id');
        var no = $('#' + id + 'Sad');
        var yes = $('#' + id + 'Happy');
        var yes_input = $('#' + id + 'Yes');
        var no_input = $('#' + id + 'No');
        var toggle = $('#' + id + 'Toggle');

        $this.addClass('hidden');
        no.removeClass("selected");
        yes.removeClass("selected");
        toggle.removeClass("sad");
        toggle.removeClass("happy");
        yes_input.prop("checked", "");
        no_input.prop("checked", "");

        toggle.animate({
            left: "0px"
        }, {
            queue: false,
            ease: 'easeInSine'
        });

        if (!clearIds.includes(id)) {
            clearIds.push(id);
        }
    });

    // Change icon for accordion
    $('.collapse').on('shown.bs.collapse', function () {
        $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
    }).on('hidden.bs.collapse', function () {
        $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
    });
});