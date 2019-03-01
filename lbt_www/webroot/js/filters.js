$(function () {

    var fields = [];
    var filterIds = [];
    var clearIds = [];
    var resetBasicFilterSummary = false;
    var searchParams  = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results==null){
            return null;
        }
        else{
            return decodeURI(results[1]) || 0;
        }
    };
    var lab_types = searchParams('lab_types');
    var climate_zones = searchParams('climate_zones');
    var $clear = $(".clear");
    var $icon = $(".toggle_icon");
    var y_axis = $('#yaxis').val();
    var x_axis = $('#xaxis').val();
    var x_axis_units = $('#xunits');
    var y_axis_units = $('#yunits');

    var formObj = {};
    formObj.filters = {};
    formObj["x-axis"] = x_axis;
    formObj["y-axis"] = y_axis;

    var metrics =[{"id":"source_eui", "label": "Source EUI", "units":"kBtu/sf/yr", "alternative_units":[]},
        {"id":"site_eui", "label": "Site EUI", "units":"kBtu/sf/yr", "alternative_units":[]},
        {"id":"electric_eui", "label": "Electric EUI", "units":"kWh/sf/yr", "alternative_units":[]},
        {"id":"fuel_eui", "label": "Fuels EUI", "units":"kBtu/sf/yr", "alternative_units":[]},
        {"id":"peak_electric_demand_intensity", "label": "Peak Electric Deman", "units":"W/sf", "alternative_units":[]},
        {"id":"total_utility_cost_intensity", "label": "Energy Cost Intensity", "units":"$/sf/yr", "alternative_units":[]},
        {"id":"water_use_intensity", "label": "Water Intensity", "units":"gal/sf/yr", "alternative_units":[]},
        {"id":"water_sewer_cost_intensity", "label": "Water Cost Intensity", "units":"$/sf/yr", "alternative_units":[]},
        {"id":"ghg_intensity", "label": "GHG Intensity", "units":"lbs/sf/yr", "alternative_units":[]},
        {"id":"ventilation_electric_eui", "label": "Ventilation EUI", "units":"kWh/sf/yr", "alternative_units":[]},
        {"id":"ventilation_peak_electric_demand_intensity", "label": "Ventilation Peak Intensity", "units":"W/sf", "alternative_units":[]},
        {"id":"occupied_required_air_change_rate", "label": "Ventilation Rate", "units":"ACH", "alternative_units":[]},
        {"id":"ventilation_peak_airflow", "label": "Peak Airflow Intensity", "units":"cfm/sf", "alternative_units":[]},
        {"id":"cooling_plant_electric_eui", "label": "Cooling EUI", "units":"kWh/sf/yr", "alternative_units":[]},
        {"id":"cooling_plant_peak_electric_demand_intensity", "label": "Cooling Peak Elec Intensity", "units":"W/sf", "alternative_units":[]},
        {"id":"cooling_plant_capacity", "label": "Cooling Capacity", "units":"sf/ton", "alternative_units":[]},
        {"id":"cooling_plant_peak_load_intensity", "label": "Cooling Peak Load Intensity", "units":"sf/ton", "alternative_units":[]},
        {"id":"lighting_electric_eui", "label": "Lighting EUI", "units":"kWh/sf/yr", "alternative_units":[]},
        {"id":"lighting_peak_electric_demand_intensity", "label": "Lighting Peak Intensity", "units":"W/sf", "alternative_units":[]},
        {"id":"installed_lighting_intensity", "label": "Lighting Installed Intensity", "units":"W/sf", "alternative_units":[]},
        {"id":"process_plug_electric_eui", "label": "Plug EUI", "units":"kWh/sf/yr", "alternative_units":[]},
        {"id":"process_plug_peak_electric_demand_intensity", "label": "Plug Peak Intensity", "units":"W/sf", "alternative_units":[]},
        {"id":"total_lab_area", "label": "Lab Area", "units":"%", "alternative_units":[]}
    ];
    var decimal_places={"unoccupied_required_air_change_rate":2, "number_of_ducted_fume_hoods":4,"number_of_filtering_fume_hoods":3, "total_fume_hood_length":3,"number_of_people":4,"source_eui":0,"site_eui":0,"electric_eui":0,"fuel_eui":0,"peak_electric_demand_intensity":1,"total_utility_cost_intensity":2,"water_use_intensity":1,"water_sewer_cost_intensity":2,"ghg_intensity":1,"ventilation_electric_eui":1,"ventilation_peak_electric_demand_intensity":2,"occupied_required_air_change_rate":2,"ventilation_peak_airflow":2,"cooling_plant_electric_eui":1,"cooling_plant_peak_electric_demand_intensity":2,"cooling_plant_capacity":0,"cooling_plant_peak_load_intensity":0,"lighting_electric_eui":1,"lighting_peak_electric_demand_intensity":2,"installed_lighting_intensity":2,"process_plug_electric_eui":1,"process_plug_peak_electric_demand_intensity":2,"total_lab_area":0};

    // Polyfill for includes for IE
    if (!Array.prototype.includes) {
        Object.defineProperty(Array.prototype, 'includes', {
            value: function(searchElement, fromIndex) {

                if (this == null) {
                    throw new TypeError('"this" is null or not defined');
                }

                var o = Object(this);
                // tslint:disable-next-line:no-bitwise
                var len = o.length >>> 0;

                if (len === 0) {
                    return false;
                }
                // tslint:disable-next-line:no-bitwise
                var n = fromIndex | 0;
                var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

                while (k < len) {
                    if (o[k] === searchElement) {
                        return true;
                    }
                    k++;
                }
                return false;
            }
        });
    }

    /****** Units *****/
    changeUnits();
    function changeUnits() {
        $.each(metrics, function (k, v) {
            if (x_axis === v['id']) {
                x_axis_units.html(v['units']);
            }

            if (y_axis === v['id']) {
                y_axis_units.html(v['units']);
            }

        });
    }

    if (lab_types !== null) {
        setLabType(lab_types);
    }

    if (climate_zones !== null) {
        setClimateZones(climate_zones);
    }

    $( "#xaxis, #yaxis" ).change(function() {
        y_axis = $('#yaxis').val();
        x_axis = $('#xaxis').val();
        formObj["x-axis"] = x_axis;
        formObj["y-axis"] = y_axis;
        changeUnits();
        showPlots();
    });

    function checkSpecialtyLabs(obj) {
       if('biosafety_lab_area' in obj['filters']){
           if(obj['filters']['biosafety_lab_area'] === "true") {
               obj['filters']['biosafety_lab_area'] = {"min": 0.1, "max": 1000000};
           }else{
               obj['filters']['biosafety_lab_area'] = {"min": 0, "max": 0.1};
           }
       }

       if('cleanroom_iso5_area' in obj['filters']){
           if(obj['filters']['cleanroom_iso5_area'] === "true") {
               obj['filters']['cleanroom_iso5_area'] = {"min": 0.1, "max": 1000000};
           }else{
               obj['filters']['cleanroom_iso5_area'] = {"min": 0, "max": 0.1};
           }
       }

       if('cleanroom_iso6_area' in obj['filters']){
           if(obj['filters']['cleanroom_iso6_area'] === "true") {
               obj['filters']['cleanroom_iso6_area'] = {"min": 0.1, "max": 1000000};
           }else{
               obj['filters']['cleanroom_iso6_area'] = {"min": 0, "max": 0.1};
           }
       }

       if('cleanroom_iso7_area' in obj['filters']){
           if(obj['filters']['cleanroom_iso7_area'] === "true") {
               obj['filters']['cleanroom_iso7_area'] = {"min": 0.1, "max": 1000000};
           }else{
               obj['filters']['cleanroom_iso7_area'] = {"min": 0, "max": 0.1};
           }
       }
    }
    
    function showPlots(){

        // Draw Scatter Plot
        var limitedFormObj = jQuery.extend(true, {}, formObj);
        checkSpecialtyLabs(limitedFormObj);
        if(!(x_axis in limitedFormObj['filters'])) {
            limitedFormObj['filters'][x_axis] = {"min": 0.0000000001, "max": 7000000};
        }
        if(!(y_axis in limitedFormObj['filters'])) {
            limitedFormObj['filters'][y_axis] = {"min": 0.0000000001, "max": 7000000};
        }

        logged_in = logged_in || false;
        var obj = {lab_types: lab_types, class: 'scatter', x_axis: x_axis, y_axis: y_axis, logged_in: logged_in };
        var url = '/buildings/bpd.json?type=scatter&data='+ JSON.stringify(limitedFormObj);
        scatter(url, obj);

        // Draw Histogram Plot
        var obj2 = {lab_types: lab_types, class: 'histogram', x_axis: x_axis, y_axis: y_axis, logged_in: logged_in };
        var xOnlyFormObj = jQuery.extend(true, {}, formObj);
        checkSpecialtyLabs(xOnlyFormObj);
        var tmp_y = xOnlyFormObj['y-axis'];
        xOnlyFormObj['x-axis'] = tmp_y;
        delete xOnlyFormObj['y-axis'];
        if(!(y_axis in xOnlyFormObj['filters'])) {
            xOnlyFormObj['filters'][tmp_y] = {"min": 0.0000000001, "max": 7000000};
        }
        var url2 = '/buildings/bpd.json?type=histogram&data='+ JSON.stringify(xOnlyFormObj);
        histogram(url2, obj2);

        // Draw Sorted Plot
        var obj3 = {lab_types: lab_types, class: 'sorted', x_axis: x_axis, y_axis: y_axis, logged_in: logged_in };
        var url3 = '/buildings/bpd.json?type=sorted&data='+ JSON.stringify(xOnlyFormObj);
        sorted(url3, obj3);
    }

    function decimalPlaces(float,length) {
        ret = "";
        str = float.toString();
        array = str.split(".");
        if(array.length==2) {
            ret += array[0] + ".";
            for(i=0;i<length;i++) {
                if(i>=array[1].length) ret += '0';
                else ret+= array[1][i];
            }
        }
        else if(array.length == 1) {
            ret += array[0] + ".";
            for(i=0;i<length;i++) {
                ret += '0'
            }
        }

        return ret;
    }


    function slider(id, min, max, step, float, update) {

        // Slider
        update = update || false;
        var rangeSlider = document.getElementById(id);
        var moneyFormat = wNumb({
            suffix: ''
        });

        if(update){
            rangeSlider.noUiSlider.updateOptions({
                start: [min, max],
                step: step,
                range: {
                    'min': [min],
                    'max': [max]
                },
                format: moneyFormat,
                connect: true
            });
        }else {
            noUiSlider.create(rangeSlider, {
                start: [min, max],
                step: step,
                range: {
                    'min': [min],
                    'max': [max]
                },
                format: moneyFormat,
                connect: true
            });
        }

        // Set visual min and max values and also update value hidden form inputs
        rangeSlider.noUiSlider.on('update', function (values) {

            var fixedDecimalPlace = 0;
            $.each(decimal_places, function(valKey,valObj) {
                if(id === valKey){fixedDecimalPlace = valObj;}
            });

            var myMin = parseFloat(values[0]).toFixed(fixedDecimalPlace);
            var myMax = parseFloat(values[1]).toFixed(fixedDecimalPlace);
            document.getElementById(id + '-value-1').innerHTML = myMin;
            document.getElementById(id + '-value-2').innerHTML = myMax;
            $('#' + id + '_min_max').attr({"min": myMin, "max": myMax});
        });

    }


    function removeOldFilters(form_inputs) {
        form_inputs.each(function () {
            var input_id = $(this).attr('id');
            var isChecked = $(this).is(':checked');
            var hasText = false;
            var type = getType(input_id);

            if(type === 'option'){
                var selectedText = $(this).val();
                hasText = selectedText.length > 0 && selectedText !== "0";
            }

            if(!hasText)
            {
                if (!isChecked && filterIds.includes(input_id)) {
                    var field_id = getFieldId(input_id);
                    var filter = isFilter(input_id);

                    if (filter) {
                        if (field_id in formObj['filters']) {
                            delete formObj['filters'][field_id]
                        }
                    } else {
                        if (field_id in formObj) {
                            delete formObj[field_id];
                        }
                    }
                }
            }

        });
    }

    function removeClearIds(){
        if(clearIds.length > 0){
            clearIds.forEach(function (input_id) {
                var field_id = getFieldId(input_id);
                var filter = isFilter(input_id);

                if (filter) {
                    if (field_id in formObj['filters']) {
                        delete formObj['filters'][field_id]
                    }
                } else {
                    if (field_id in formObj) {
                        delete formObj[field_id];
                    }
                }
            });
        }
    }

    function getSliderInfo(id) {
        var slider = {};
        fields.forEach(function (fieldObj) {
            if (fieldObj.id === id || fieldObj.field_id === id) {
                if (fieldObj.slider === true) {
                    slider = {
                        min: fieldObj.min,
                        max: fieldObj.max,
                        step: fieldObj.step,
                        multiplier: fieldObj.multiplier
                    };
                    return true;
                }
            }
        });

        return slider;
    }

    function getFieldId(id) {
        var found_id = '';
        var found = false;

        fields.forEach(function (fieldObj) {

            if (fieldObj.id === id) {
                if ('field_id' in fieldObj) {
                    found_id = fieldObj.field_id;
                    found = true;
                    return true;
                }
            }

            // Match field_id in category values
            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.id === id) {
                        if ('field_id' in valObj) {
                            found_id = valObj.field_id;
                            found = true;
                            return true;
                        }else{
                            found_id = fieldObj.field_id;
                            found = true;
                            return true;
                        }
                    }
                });
            }

            if (found) {
                return true;
            }

        });

        return found_id;
    }

    function getType(id) {
        var type = '';
        var found = false;
        fields.forEach(function (fieldObj) {

            if (fieldObj.id === id || fieldObj.field_id === id) {
                type = fieldObj.type;
                found = true;
                return true;
            }

            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.field_id === id || valObj.id === id) {
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

    function getSection(id) {
        var section = '';
        var found = false;
        fields.forEach(function (fieldObj) {

            if (fieldObj.id === id || fieldObj.field_id === id) {
                section = fieldObj.section;
                found = true;
                return true;
            }

            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.field_id === id || valObj.id === id) {
                        section = fieldObj.section;
                        found = true;
                        return true;
                    }
                });

                if (found) {
                    return true;
                }
            }

        });

        return section;
    }

    function getOriginalLabel(oldLabel) {
        oldLabel = oldLabel.replace("&amp;", "and");
        var label = oldLabel;
        var found = false;
        fields.forEach(function (fieldObj) {
            /** @namespace fieldObj */
            /** @namespace fieldObj.alternative_label */
            if (fieldObj.alternative_label === oldLabel) {
                if('summary_label' in fieldObj) {
                    label = fieldObj.summary_label;
                }else{
                    label = fieldObj.label;
                }

                found = true;
                return true;
            }

            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if(!found) {
                        if (valObj.alternative_label === oldLabel) {

                            if ('summary_label' in valObj) {
                                label = valObj.summary_label;
                            } else {
                                if ('alternative_label' in valObj) {
                                    label = valObj.label;
                                    found = true;
                                }
                            }
                        }
                    }
                });

            }

        });

        return label;
    }

    function getLabel(id, getCategoryLabel) {
        getCategoryLabel = getCategoryLabel || false;
        var label = '';
        var found = false;

        fields.forEach(function (fieldObj) {

            if (fieldObj.id === id || fieldObj.field_id === id) {
                if('summary_label' in fieldObj) {
                    label = fieldObj.summary_label;
                }else{
                    label = fieldObj.label;
                }
                found = true;
                return true;
            }

            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.field_id === id || valObj.id === id) {
                        /** @namespace fieldObj */
                        /** @namespace fieldObj.summary_label */
                        if(getCategoryLabel) {
                            if('summary_label' in valObj) {
                                label = valObj.summary_label;
                            }else{
                                label = valObj.label;
                            }
                        }else{
                            if('summary_label' in valObj) {
                                label = fieldObj.summary_label;
                            }else{
                                label = fieldObj.label;
                            }
                        }

                        found = true;
                        return true;
                    }
                });

                if (found) {
                    return true;
                }
            }

        });

        return label;
    }

    function getCategoricalValues(id){
        var values = {};
        var found = true;
        var sub_section = '';
        fields.forEach(function (fieldObj) {
            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {

                    if (fieldObj.id === id) {
                        if(valObj.label !== "ALL") {
                            if ('values' in values) {
                                values['values'] += "," + valObj.id;
                            } else {
                                values['values'] = valObj.id;
                            }
                        }
                    }

                    if(valObj.id === id){
                        if('sub_section' in valObj){
                            sub_section = valObj.sub_section;
                        }
                    }

                    if(sub_section !== ''){
                        if('sub_section' in valObj) {

                            if(valObj.sub_section === sub_section) {
                                if ('values' in values) {
                                    values['values'] += "," + valObj.id;
                                } else {
                                    values['values'] = valObj.id;
                                }
                            }
                        }
                    }
                });

                if (found) {
                    return true;
                }
            }
        });

        return values;
    }
    
    function getCategoricalValue(id) {
        var value = '';
        var found = true;
        fields.forEach(function (fieldObj) {
            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.id === id) {

                        if ('value' in valObj) {
                            value = valObj.value
                        } else {
                            if ('alternative_label' in valObj) {
                                value = valObj.alternative_label;
                            } else {
                                value = valObj.label;
                            }
                        }

                        found = true;
                        return true;
                    }
                });

                if (found) {
                    return true;
                }
            }

        });

        return value;
    }

    function isSlider(id) {
        var found = false;
        fields.forEach(function (fieldObj) {
            if (fieldObj.id === id || fieldObj.field_id === id) {
                if (fieldObj.slider === true) {
                    found = true;
                    return true;
                }
            }
        });

        return found;
    }

    function isFilter(id) {
        var found = false;
        fields.forEach(function (fieldObj) {
            if (fieldObj.id === id || fieldObj.field_id === id) {
                if (fieldObj.filter === true) {
                    found = true;
                    return true;
                }
            }

            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.id === id) {
                        if (fieldObj.filter === true) {
                            found = true;
                            return true;
                        }
                    }
                });
            }

            if(found){
                return true;
            }
        });

        return found;
    }

    function isCategorical(id) {
        var found = false;
        fields.forEach(function (fieldObj) {
            if (fieldObj.id === id || fieldObj.field_id === id) {
                if (fieldObj.categorical === true) {
                    found = true;
                    return true;
                }
            }

            if ('values' in fieldObj) {
                fieldObj.values.forEach(function (valObj) {
                    if (valObj.id === id) {
                        if (fieldObj.categorical === true) {
                            found = true;
                            return true;
                        }
                    }
                });
            }

            if(found){
                return true;
            }

        });

        return found;
    }

    function showSummary(id){
        var htmlObj = [];
        $.each(formObj, function(fieldKey,fieldObj){

            if(fieldKey === "filters"){
                $.each(fieldObj, function(valKey,valObj){
                    var label = getLabel(valKey);
                    var type = getType(valKey);
                    var section = getSection(valKey);

                    if(section === id) {
                        if (type === "booleanArray") {
                            htmlObj[valKey] = htmlElement(valKey, valObj);
                        } else {
                            htmlObj[valKey] = htmlHeader(label) + htmlElement(valKey, valObj);
                        }
                    }

                });
            }else {
                var label = getLabel(fieldKey);
                var section = getSection(fieldKey);

                if(section === id) {
                    if (fieldKey === "include_measured" || fieldKey === "include_estimated") {
                        if ('data_type' in htmlObj) {
                            htmlObj['data_type'] += htmlElement(fieldKey, fieldObj);
                        } else {
                            htmlObj['data_type'] = htmlHeader(label) + htmlElement(fieldKey, fieldObj);
                        }
                    } else {
                        htmlObj[fieldKey] = htmlHeader(label) + htmlElement(fieldKey, fieldObj);
                    }
                }
            }
        });

        var html = '';
        for (var key in htmlObj) {
            html += htmlObj[key];
        }

        $('#' + id).html(html);
    }

    function toTitleCase(str) {
        return str.replace(/(?:^|\s)\w/g, function(match) {
            return match.toUpperCase();
        });
    }

    function htmlHeader(label){
        return "<h5>" + label + "</h5>";
    }

    function htmlElement(id, obj){
        var type = getType(id);
        var html = "";
        var label = getLabel(id, true);

        if (type === "array" || type === "arrayString") {
            obj.forEach(function (item) {
                item = item.replace("RandD","R&amp;D").replace("and Gas", "&amp; Gas");
                html += "<span class='badge badge-info'>" + toTitleCase(getOriginalLabel(item)) +"</span> ";
            });
            html += "<br>";

        } else if (type === "boolean") {
            html += "<span class='badge badge-info'>" + toTitleCase(label) + "</span><br> ";
        }else if (type === "booleanArray") {
            html += "<br>";
            if(obj[0] === "true") {
                html += toTitleCase(label) +"<i class='glyphicon glyphicon-ok'></i><br>";
            }else{
                html += toTitleCase(label) +"<i class='glyphicon glyphicon-remove'></i><br>";
            }
        } else if (type === "numeric") {
            var fixedDecimalPlace = 0;
            $.each(decimal_places, function(valKey,valObj) {
                if(id === valKey){fixedDecimalPlace = valObj;}
            });

            var area = ['total_lab_area', 'other_lab_area','dry_lab_area','vivarium_area','physical_lab_area','chemical_lab_area','biological_lab_area'];
            var myMin = obj.min * 100;
            var myMax = obj.max * 100;
            var a = (jQuery.inArray(id, area) !== -1) ? myMin.toFixed(2):obj.min.toFixed(fixedDecimalPlace);
            var b = (jQuery.inArray(id, area) !== -1) ? myMax.toFixed(2):obj.max.toFixed(fixedDecimalPlace);
            var c = (jQuery.inArray(id, area) !== -1) ? '%':'';
            var myHtml =  "<span class='badge badge-info'>" + a + "</span> to <span class='badge badge-info'>" + b + "</span> " + c + "<br>";
            html += myHtml;
        }else if (type === "option"){
            html += "<span class='badge badge-info'>" + toTitleCase(getOriginalLabel(obj[0])) + "</span><br> ";
        }
        
        return html;
    }

    $(".apply").click(function (e) {

        var id = $(this).data('id');
        var modal_id = $(this).data('modal-id');
        var summary_id = $(this).data('summary-id');
        var inputs = $('#' + id).serializeArray();
        var form_inputs = $('#' + id + ' :input');

        checkLabTypes();
        removeOldFilters(form_inputs);
        removeClearIds();

        $.each(inputs, function (i, input) {

            var skip = false;
            var $input = $('#' + input.name);
            var key = getFieldId(input.name);
            var val = input.value;
            var myObj;

            if (key === ""){
                skip = true;
            }

            if (isSlider(input.name)) {
                var min = parseFloat($input.attr('min'));
                var max = parseFloat($input.attr('max'));
                var sliderInfo = getSliderInfo(input.name);
                var sliderMin = parseFloat(sliderInfo.min);
                var sliderMax = parseFloat(sliderInfo.max);
                var multiplier = parseFloat(sliderInfo.multiplier);

                if ((min >= sliderMin) && (max <= sliderMax) && !((min === sliderMin) && (max === sliderMax))) {
                    var myMin = parseFloat($input.attr('min'));
                    var myMax = parseFloat($input.attr('max'));

                    if (multiplier) {
                        myMin *= multiplier;
                        myMax *= multiplier;
                    }

                    // Overwrite default value with min max obj
                    val = {"min": myMin, "max": myMax};
                } else {
                    // Slider is at min / max
                    skip = true;
                }
            }

            if (!skip) {

                if (isFilter(input.name)) {
                    myObj = formObj['filters'];
                } else {
                    myObj = formObj;
                }

                if (isCategorical(input.name)) {
                    var type = getType(input.name);
                    val = getCategoricalValue(input.name);

                    if (val !== "All") {
                        if (key in myObj) {
                            // Do not include if already in array
                            if (type === "array") {
                                if (!myObj[key].includes(val)) {
                                    myObj[key].push(val);
                                }
                            }else if (type === "booleanArray"){
                                var valArr = [];
                                valArr.push(input.value);
                                myObj[key] = valArr
                            }else if (type === "option"){
                                if(input.value.length > 0 && input.value !== "0") {
                                    var valArr6 = [];
                                    valArr6.push(input.value);
                                    myObj[key] = valArr6
                                }
                            } else {
                                myObj[key] = val;
                            }
                        } else {
                            if (type === "array") {
                                var valArr4 = [];
                                valArr4.push(val);
                                myObj[key] = valArr4;
                            }else if (type === "boolean"){
                                myObj[key] = val;
                            }else if (type === "booleanArray"){
                                var valArr2 = [];
                                valArr2.push(input.value);
                                myObj[key] = valArr2
                            }else if (type === "arrayString"){
                                if(input.value.length > 0) {
                                    var valArr5;
                                    if (input.value.indexOf(',') > -1) {
                                        valArr5 = input.value.split(',');
                                        myObj[key] = valArr5;
                                    } else {
                                        valArr5 = [];
                                        valArr5.push(input.value);
                                        myObj[key] = valArr5;
                                    }
                                }
                            }else if (type === "option"){
                                if(input.value.length > 0 && input.value !== "0") {
                                    var valArr3 = [];
                                    valArr3.push(input.value);
                                    myObj[key] = valArr3
                                }
                            }else{
                                myObj[key] = val;
                            }
                        }
                    }
                } else {
                    myObj[key] = val;
                }

                // Track filters
                if (!filterIds.includes(input.name)) {
                    filterIds.push(input.name);
                }
            }

        });

        // If both Measured and Estimated then both offset each other
        if('include_estimated' in formObj && 'include_measured' in formObj){
            formObj['include_estimated'] = true;
            formObj['include_measured'] = true;
        }

        showPlots();
        e.preventDefault();
        $('#' + modal_id).modal('toggle');
        showSummary(summary_id);

        if(resetBasicFilterSummary){
            showSummary('basicFilterSummary');
            resetBasicFilterSummary = false;
        }
    });

    /**
     * @typedef {Object} data
     * @property {string} fields
     * @property {string} slider
     * @property {string} field_id
     * @property {string} section
     * @property {string} categorical
     */
    $.getJSON("/json/fields.json", function (data) {
        if ('fields' in data) {

            /** @type {data} */
            fields = data.fields;

            // Generate Sliders
            fields.forEach(function (fieldObj) {
                if ('slider' in fieldObj) {
                    if(fieldObj.field_id === "year" || fieldObj.field_id === "year_built" ){
                        fieldObj.max = new Date().getFullYear() ;
                    }

                    if (fieldObj.slider === true) {
                        slider(fieldObj.field_id, fieldObj.min, fieldObj.max, fieldObj.step, fieldObj.float);
                        if('disabled' in fieldObj){
                            $('#'+fieldObj.field_id).attr('disabled', true);
                            var $h4 = $('#'+fieldObj.field_id +"_h4");
                            $h4.append(' <i id="'+fieldObj.field_id+'_alert" class="glyphicon glyphicon-flag"></i>');
                            $h4.addClass('disabled');
                            $h4.attr('data-toggle', 'tooltip');
                            $h4.attr('data-placement', 'top');
                            $h4.attr('title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                            $h4.attr('data-original-title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                            $h4.tooltip();
                        }
                    }
                }

                if ('values' in fieldObj) {
                    fieldObj.values.forEach(function (valObj) {
                        if('disabled' in valObj){
                            var $forId = $('[for="'+valObj.id+'"]');
                            $forId.append(' <i id="'+valObj.id+'_alert" class="glyphicon glyphicon-flag shift-left"></i>');
                            $forId.addClass('disabled');
                            $forId.removeAttr('data-toggle');
                            $forId.removeAttr('data-placement');
                            $forId.removeAttr('title');
                            $forId.removeAttr('data-original-title');
                            var $curve = $('#'+valObj.id+'_curve');
                            $curve.attr('data-toggle', 'tooltip');
                            $curve.attr('data-placement', 'top');
                            $curve.attr('title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                            $curve.attr('data-original-title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                            $curve.tooltip();
                        }
                    });
                }

                if('disabled' in fieldObj){
                    var $h5 = $('#' + fieldObj.field_id + "_h5");
                    if($h5.length > 0) {
                        $h5.addClass('disabled');
                        $h5.append(' <i id="'+fieldObj.field_id+'_alert" class="glyphicon glyphicon-flag"></i>');
                        $h5.addClass('disabled');
                        $h5.attr('data-toggle', 'tooltip');
                        $h5.attr('data-placement', 'top');
                        $h5.attr('title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                        $h5.attr('data-original-title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                        $h5.tooltip();

                        // Remove tooltip from yes_no_toggle that is disabled
                        var $yesNoToggle = $('#'+fieldObj.field_id +"_yes_no_toggle");
                        $yesNoToggle.attr('data-toggle', null);
                        $yesNoToggle.attr('data-placement', null);
                        $yesNoToggle.attr('title',null);
                        $yesNoToggle.attr('data-original-title',null);
                    }else {
                        var $forFieldId = $('[for="'+fieldObj.field_id+'"]');
                        $forFieldId.append(' <i id="'+fieldObj.field_id+'_alert" class="glyphicon glyphicon-flag"></i>');
                        $forFieldId.addClass('disabled');
                        $forFieldId.attr('data-toggle', 'tooltip');
                        $forFieldId.attr('data-placement', 'top');
                        $forFieldId.attr('title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                        $forFieldId.attr('data-original-title','The peer group does not yet contain data for this category. Please log in and enter data for your own buildings -- this will help us to populate the peer group!');
                        $forFieldId.tooltip();
                    }
                    $('#'+fieldObj.field_id).attr('disabled', true);
                }

            });

            // Disable these sliders by default
            enableDisableLabTypeSliders(true);
        }
    });

    function checkLabTypes(){
        var isChecked = $('#use_sliders').is(':checked');
        var lab_types = ['lab_use_all','biology', 'chemistry','vivairum','physics','maker','biochem','3-way','other','c-other'];

        if(isChecked) {

            $.each(lab_types, function(key,id){
                var $id = $('#'+id);
                var isChecked = $id.is(':checked');
                if(isChecked) {
                    $id.prop('checked', false);
                }
                $id.attr('disabled', true);
            });

            clearIds.push('lab_use');
            resetBasicFilterSummary = true;
            $('#lab_notice').removeClass('hidden');
        }else{

            $.each(lab_types, function(key,id){
                $('#'+id).attr('disabled', false);
            });
            $('#lab_notice').addClass("hidden");
        }
    }

    function enableDisableLabTypeSliders(toggle){
        var lab_types = ['biological_lab_area','chemical_lab_area','physical_lab_area','vivarium_area','dry_lab_area','other_lab_area'];

        $.each(lab_types, function(key,id){
            $('#'+id).attr('disabled', toggle);
        });

        if(toggle){
            $.each(lab_types, function(key,id){
                slider(id, 0, 100,1,false, true);
            });
        }
    }

    $('#use_sliders').on("click", function () {
        var isChecked = $(this).is(':checked');
        if(isChecked) {
            enableDisableLabTypeSliders(false);
        }else{
            enableDisableLabTypeSliders(true);
        }
    });

    $('.all').on("click", function () {
        var name = $(this).attr('id');
        var new_name = name.replace('_all','');
        var results = getCategoricalValues(new_name);
        var values = results.values.split(",");
        var isChecked = $(this).is(':checked');

        values.forEach(function (item) {
            if(isChecked) {
                $('#' + item).prop("checked", true);
            }else{
                $('#' + item).prop("checked", false);
            }
        });

    });

    $icon.on("click", function () {
        var $this = $(this);
        var id = $(this).data('id');
        var no = $('#' + id + 'Sad');
        var yes = $('#' + id + 'Happy');
        var yes_input = $('#' + id + 'Yes');
        var no_input = $('#' + id + 'No');
        var link = $('#' + id + 'Link');
        var toggle = $('#' + id + 'Toggle');
        var h5 = $('#' + id + '_h5');

        if (h5.hasClass("disabled")) {
            return true;
        }

        if ($this.hasClass("happy")) {
            link.removeClass('hidden');
            no.removeClass("selected");
            yes.addClass("selected");
            toggle.removeClass("sad");
            toggle.addClass("happy");
            yes_input.prop("checked", "checked");

            toggle.animate({
                left: "0px"
            }, {
                queue: false,
                ease: 'easeInSine'
            });
        }
        else {
            link.removeClass('hidden');
            no_input.prop("checked", "checked");
            no.addClass("selected");
            yes.removeClass("selected");
            toggle.addClass("sad");
            toggle.removeClass("happy");

            toggle.animate({
                left: "56px"
            }, {
                queue: false,
                ease: 'easeInSine'
            });
        }
    });

    function clearYesNo(id){
        var no = $('#' + id + 'Sad');
        var yes = $('#' + id + 'Happy');
        var yes_input = $('#' + id + 'Yes');
        var no_input = $('#' + id + 'No');
        var toggle = $('#' + id + 'Toggle');
        var link = $('#' + id + 'Link');

        link.addClass('hidden');
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

        if(!clearIds.includes(id)){
            clearIds.push(id);
        }
    }

    $clear.on("click", function () {
        var $this = $(this);
        var id = $this.data('id');
        clearYesNo(id);
    });

    $("#clear_all").click(function (e) {
        e.preventDefault();
        formObj['filters'] = {};

        if('include_estimated' in formObj){
            delete formObj['include_estimated'];
        }

        if('include_measured' in formObj){
            delete formObj['include_measured'];
        }

        showPlots();
        showSummary('basicFilterSummary');
        showSummary('buildingPropertiesSummary');
        showSummary('buildingSystemSummary');
        showSummary('fumeHoodsSummary');
        showSummary('labTypesSummary');

        $('#basicFilter').trigger("reset");
        $('#buildingProperties').trigger("reset");
        $('#buildingSystem').trigger("reset");
        $('#fumeHoods').trigger("reset");
        $('#labTypes').trigger("reset");

        // Reset Sliders
        fields.forEach(function (fieldObj) {
            if ('slider' in fieldObj) {
                if (fieldObj.slider === true) {
                    slider(fieldObj.field_id, fieldObj.min, fieldObj.max, fieldObj.step, fieldObj.float, true);
                }
            }

            if (fieldObj.type === 'booleanArray') {
                clearYesNo(fieldObj.field_id);
            }
        });
        enableDisableLabTypeSliders(true);

        resetBasicFilterSummary = false;
    });

    $("#clear_basic").click(function (e) {
        e.preventDefault();
        if('include_estimated' in formObj){
            delete formObj['include_estimated'];
        }

        if('include_measured' in formObj){
            delete formObj['include_measured'];
        }

        clearSection('basicFilterSummary');
        showPlots();
        showSummary('basicFilterSummary');
        $('#basicFilter').trigger("reset");
        resetBasicFilterSummary = false;
    });

    $("#clear_building_properties").click(function (e) {
        e.preventDefault();
        clearSection('buildingPropertiesSummary');
        showPlots();
        showSummary('buildingPropertiesSummary');
        $('#buildingProperties').trigger("reset");
    });

    $("#clear_building_system").click(function (e) {
        e.preventDefault();
        clearSection('buildingSystemSummary');
        showPlots();
        showSummary('buildingSystemSummary');
        $('#buildingSystem').trigger("reset");
    });

    $("#clear_fume_hoods").click(function (e) {
        e.preventDefault();
        clearSection('fumeHoodsSummary');
        showPlots();
        showSummary('fumeHoodsSummary');
        $('#fumeHoods').trigger("reset");
    });

    $("#clear_lab_types").click(function (e) {
        e.preventDefault();
        clearSection('labTypesSummary');
        showPlots();
        showSummary('labTypesSummary');
        $('#labTypes').trigger("reset");
        checkLabTypes();
        enableDisableLabTypeSliders(true);
    });

    function clearSection(id){
        // Clear fields
        fields.forEach(function (fieldObj) {
            if (fieldObj.section === id) {
                if (fieldObj.id in formObj['filters']) {
                    delete formObj['filters'][fieldObj.id];
                }
            }
        });

        // Reset Sliders
        fields.forEach(function (fieldObj) {
            if(fieldObj.section === id) {
                if ('slider' in fieldObj) {
                    if (fieldObj.slider === true) {
                        slider(fieldObj.field_id, fieldObj.min, fieldObj.max, fieldObj.step, fieldObj.float, true);

                        if (fieldObj.field_id in formObj['filters']) {
                            delete formObj['filters'][fieldObj.field_id];
                        }
                    }
                }

                if (fieldObj.type === 'booleanArray') {
                    clearYesNo(fieldObj.field_id);
                }
            }
        });
    }

    // Change icon for accordion
    $('.collapse').on('shown.bs.collapse', function () {
        $(this).parent().find(".glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
    }).on('hidden.bs.collapse', function () {
        $(this).parent().find(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
    });

    showPlots();
    $('[data-toggle="tooltip"]').tooltip();

    $('[data-toggle="tooltip-map"]').popover({
        html: true,
        trigger: 'hover',
        placement: 'left',
        title: "<strong>Climate Zone</strong> Map",
        content: function(){return '<img src="'+$(this).data('img') + '" class="climate_zone" alt="Climate Zone Map"/>';}
    });

    window.onresize = function() {
        $(".echart").each(function(){
            var id = $(this).attr('_echarts_instance_');
            window.echarts.getInstanceById(id).resize();
        });
    };

});