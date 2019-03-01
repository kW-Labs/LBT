var schema = [
    {name: '% Lab Area', index: 0, text: '% Lab Area'},
    {name: 'Source EUI', index: 1, text: 'Source EUI'},
    {name: 'Data Year', index: 2, text: 'Data Year'},
    {name: 'Rank', index: 3, text: 'Rank'}
];

var metrics =[{"id":"source_eui", "label": "Source EUI", "units":"kBtu/sf/yr", "alternative_units":[]},
    {"id":"site_eui", "label": "Site EUI", "units":"kBtu/sf/yr", "alternative_units":[]},
    {"id":"electric_eui", "label": "Electric EUI", "units":"kWh/sf/yr", "alternative_units":[]},
    {"id":"fuel_eui", "label": "Fuels EUI", "units":"kBtu/sf/yr", "alternative_units":[]},
    {"id":"peak_electric_demand_intensity", "label": "Peak Electric Demand", "units":"W/sf", "alternative_units":[]},
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

var decimal_places={"source_eui":0,"site_eui":0,"electric_eui":0,"fuel_eui":0,"peak_electric_demand_intensity":1,"total_utility_cost_intensity":2,"water_use_intensity":1,"water_sewer_cost_intensity":2,"ghg_intensity":1,"ventilation_electric_eui":1,"ventilation_peak_electric_demand_intensity":2,"occupied_required_air_change_rate":2,"ventilation_peak_airflow":2,"cooling_plant_electric_eui":1,"cooling_plant_peak_electric_demand_intensity":2,"cooling_plant_capacity":0,"cooling_plant_peak_load_intensity":0,"lighting_electric_eui":1,"lighting_peak_electric_demand_intensity":2,"installed_lighting_intensity":2,"process_plug_electric_eui":1,"process_plug_peak_electric_demand_intensity":2,"total_lab_area":0};

function getLabel(id, label_only, parentheses){
    label_only = label_only || false;
    parentheses = parentheses || false;
    var label = '';
    $.each(metrics, function (k, v) {
        if (id === v['id']) {
            if(label_only) {
                label = v['label'];
            }else{
                if(parentheses) {
                    label = v['label'] + " (" + v['units'] + ")";
                }else{
                    label = v['label'] + " " + v['units'] + "";
                }
            }
            return false;
        }
    });

    return label;
}

function getUnits(id){
    var units = '';
    $.each(metrics, function (k, v) {
        if (id === v['id']) {
            units = v['units'];
            return false;
        }
    });

    return units;
}

function peerSummary(x, y, obj){

    var xLabel = getLabel(x, true);
    var yLabel = getLabel(y, true);
    var xUnits = getUnits(x, true);
    var yUnits = getUnits(y, true);
    var html = '';

    var xFixedDecimalPlace = 0;
    var yFixedDecimalPlace = 0;
    $.each(decimal_places, function(valKey,valObj) {
        if(x === valKey){xFixedDecimalPlace = valObj;}
        if(y === valKey){yFixedDecimalPlace = valObj;}
    });

    if (typeof obj !== 'undefined') {
        /** @namespace obj */
        /** @namespace obj.x_mean */
        /** @namespace obj.y_mean */
        /** @namespace obj.x_median */
        /** @namespace obj.y_median */
        /** @namespace obj.number_of_buildings_in_bpd */
        /** @namespace obj.number_of_matching_buildings */
        html += "<b>" + xLabel +"</b>: mean " + parseFloat((xLabel === "Lab Area") ? obj.x_mean*100:obj.x_mean).toFixed(xFixedDecimalPlace) + " " + xUnits + " |  median " + parseFloat((xLabel === "Lab Area") ? obj.x_median*100:obj.x_median).toFixed(xFixedDecimalPlace) + " " + xUnits + " <br>";
        html += "<b>" + yLabel +"</b>: mean " + parseFloat((yLabel === "Lab Area") ? obj.y_mean*100:obj.y_mean).toFixed(yFixedDecimalPlace) + " " + yUnits + " |  median " + parseFloat((yLabel === "Lab Area") ? obj.y_median*100:obj.y_median).toFixed(yFixedDecimalPlace) + " " + yUnits + " <br>";
        html += "<b>Number of Matching Buildings</b>: " + obj.number_of_matching_buildings  + "/" + obj.number_of_buildings_in_bpd + " <br>";

        $('#peerSummary').html(html);
    }else{
        html += "<h3 class=\"text-danger\">No Matching Buildings</h3>";
        $('#peerSummary').html(html);
    }
}

function setLabType(types) {
    var labTypes = types.split(',');
    labTypes.forEach(function (type) {
        $('#' + type).prop("checked", true);
    })

}

function setClimateZones(zones) {
    var climateZones = zones.split(',');
    climateZones.forEach(function (zone) {
        $('#' + zone).prop("checked", true);
        if (zone === 'all') {
            var cz = ['1a', '2a', '2b', '3a', '3b', '3c', '4a', '4b', '4c', '5a', '6a', '6b', '7', '8'];
            cz.forEach(function (z) {
                $('#' + z).prop("checked", true);
            });
        }
    })
}

function cleanBin(data){
    var new_data = [];
    $.each(data, function(valKey,valObj) {
        var tmp = valObj.split("-");
        new_data.push(tmp[0]);
    });
    return new_data;
}

function scatter(url, json) {
    var id = json.class;
    var app = echarts.init(document.getElementById(id));
    var legend = json.logged_in ? ['Peer Buildings', 'Your Buildings']: ['Peer Buildings'];
    app.clear();
    app.title = 'Scatter Chart';

    app.showLoading();
    $.getJSON(url, function (data) {
        app.hideLoading();

        peerSummary(json.x_axis, json.y_axis, data[2]);

        var option = {
            backgroundColor: '#fff',
            legend: {
                left: 80,
                padding: [15, 0, 0, 0],
                data: legend,
                textStyle:{
                    fontFamily:'Arial'
                }
            },
            tooltip: {},
            toolbox: {
                top:25,
                right: 60,
                show: true,
                itemSize: 20,
                itemGap: 17,
                orient: 'horizontal',
                feature: {
                    dataZoom: {title: {zoom: 'Area Zoom', back: 'Back'}},
                    dataView: {
                        title: 'Data',
                        optionToContent: function (opt) {
                            var series = opt.series;

                            var table = '<table class="table table-hover"><thead><tr>'
                                + '<th>' + getLabel(json.x_axis,true)  + ' ' + getLabel(json.x_axis).replace(getLabel(json.x_axis,true), "") +'</th>'
                                + '<th>' + getLabel(json.y_axis,true)  + ' ' + getLabel(json.y_axis).replace(getLabel(json.y_axis,true), "") + '</th>'
                                + '<th>' + schema[2].text + '</th>'
                                + '<th>' + 'Source' + '</th>'
                                + '</tr></thead>\n' +
                                '  <tbody>';

                            for (var i = 0, l = series[0].data.length; i < l; i++) {
                                table +='<tr>'
                                    +  '<td>' +  series[0].data[i][0] + '</td>'
                                    +  '<td>' +  series[0].data[i][1] + '</td>'
                                    +  '<td>' +  series[0].data[i][2] + '</td>'
                                    +  '<td>' +  series[0].name + '</td>'
                                    + '</td>';
                            }
                            for (var x = 0, ll = series[1].data.length; x < ll; x++) {
                                table +='<tr>'
                                    +  '<td>' +  series[1].data[x][0] + '</td>'
                                    +  '<td>' +   series[1].data[x][1] + '</td>'
                                    +  '<td>' +   series[1].data[x][2] + '</td>'
                                    +  '<td>' +   series[1].data[x][3] + '</td>'
                                    + '</td>';
                            }
                            table += '</tbody></table>';
                            return table;

                        },
                        contentToOption: function (){
                            window.location.href = url.replace(".json","/scatter.csv");
                            return true;
                        },
                        icon: 'image:///img/spreadsheet.svg',
                        lang: ['Data Source', 'Cancel', 'Download CSV']
                    },
                    restore: {title: 'Restore'},
                    saveAsImage: {title: 'Save', name: "Lab Benchmarking Chart"}
                }
            },

            xAxis: {
                splitLine: {
                    lineStyle: {
                        type: 'linear'
                    }
                },
                name: getLabel(json.x_axis, false, true),
                nameLocation: "middle",
                fontFamily:'Arial',
                nameTextStyle: {
                    fontSize: 16,
                    fontFamily: 'Arial',
                    padding: [15, 0, 0, 0]
                },
                min: 0
            },
            yAxis: {
                splitLine: {
                    lineStyle: {
                        type: 'linear'
                    }
                },
                min: 0,
                name: getLabel(json.y_axis, false, true),
                nameLocation: "middle",
                fontFamily:'Arial',
                nameTextStyle: {
                    fontSize: 16,
                    fontFamily: 'Arial',
                    padding: [0, 0, 35, 0]
                }
            },
            series: [{
                name: 'Peer Buildings',
                data: data[0],
                type: 'scatter',
                symbolSize: 15,
                tooltip: {
                    padding: 10,
                    backgroundColor: '#fff',
                    borderColor: '#eee',
                    borderWidth: 1,
                    formatter: function (obj) {
                        var value = obj.value;
                        var xFixedDecimalPlace = 0;
                        var yFixedDecimalPlace = 0;
                        $.each(decimal_places, function(valKey,valObj) {
                            if(json.x_axis === valKey){xFixedDecimalPlace = valObj;}
                            if(json.y_axis === valKey){yFixedDecimalPlace = valObj;}
                        });

                        return '<div class="color-black">'
                            + getLabel(json.x_axis,true) + '：' + value[0].toFixed(xFixedDecimalPlace) + getLabel(json.x_axis).replace(getLabel(json.x_axis,true), "") +'<br>'
                            + getLabel(json.y_axis,true) + '：' + value[1].toFixed(yFixedDecimalPlace) + getLabel(json.y_axis).replace(getLabel(json.y_axis,true), "") +' <br>'
                            + '</div>';
                    }
                },
                itemStyle: {
                    normal: {
                        color: '#D0CECE'
                    }
                }
            }, {
                name: 'Your Buildings',
                data: data[1],
                type: 'scatter',
                symbolSize: 15,
                tooltip: {
                    padding: 10,
                    backgroundColor: '#fff',
                    borderColor: '#eee',
                    borderWidth: 1,
                    formatter: function (obj) {
                        var value = obj.value;
                        var x_value = (value[0] !== "undefined")? value[0]:"";
                        var y_value = (value[1] !== "undefined")? value[1]:"";
                        var x = getLabel(json.x_axis);
                        var x_label = getLabel(json.x_axis,true);
                        var x_units = x.replace(x_label,"");
                        var y = getLabel(json.y_axis);
                        var y_label = getLabel(json.y_axis,true);
                        var y_units = y.replace(y_label,"");
                        return '<div class="color-black"><div class="tooltip-chart">'
                            + value[3]
                            + '</div>'
                            + x_label + '：' + x_value +  x_units +'<br>'
                            + y_label + '：' + y_value +  y_units +'<br>'
                            + schema[2].text + '：' + value[2] + '<br>'
                            + '</div>';
                    }
                },
                itemStyle: {
                    normal: {
                        color: '#69a88b'
                    }
                }
            }]
        };

        app.setOption(option);
    });
}

function histogramMarkPoint(bar, bins) {

    var mp = [];
    var counter = 0;
    bar.data.forEach(function (val) {
        if((val > 0)){
            mp.push({name: 'Your Building', value: val, xAxis: counter, yAxis: val});
        }
        counter++;
    });

    return {
        name: 'Your Buildings',
        type: 'bar',
        barWidth: '-80%',
        barGap:0,
        barCategoryGap:0,
        color: "#69a88b",
        data: bar.data,
        tooltip: {
            padding: 10,
            backgroundColor: '#fff',
            borderColor: '#eee',
            borderWidth: 1,
            formatter: function (obj) {
                var count = 0;
                var bin = '';
                $.each(bar.series, function(valKey,valObj){
                    /** @namespace valObj.bin */
                    var tmp = valObj.bin.split("-");
                    if(tmp[0] === obj.name){
                        bin = valObj.bin;
                        count++;
                    }
                });
                    return '<div class="color-black"><div class="tooltip-chart">'
                    + 'Your Buildings'
                    + '</div>'
                    + bin + '：' + count + '<br>'
                    + '</div>';
            }
        },

        markPoint: {
            data:mp,
            tooltip: {
                padding: 10,
                backgroundColor: '#fff',
                borderColor: '#eee',
                borderWidth: 1,
                formatter: function (obj) {
                    var count = obj.data.value;
                    var bin = bins[obj.data.xAxis];
                    return '<div class="color-black"><div class="tooltip-chart">'
                        + 'Your Buildings'
                        + '</div>'
                        + bin + '：' + count + '<br>'
                        + '</div>';
                }
            }
        }
    }   ;
}

function histogram(url, json) {
    var id = json.class;
    var app = echarts.init(document.getElementById(id));
    var legend = json.logged_in ? ['Peer Buildings', 'Your Buildings']: ['Peer Buildings'];
    app.clear();
    app.title = 'Histogram';


    app.showLoading();
    $.getJSON(url, function (data) {
        app.hideLoading();

        var option = {
            color: ['#3398DB'],
            legend: {
                left: 80,
                padding: [15, 0, 0, 0],
                data: legend,
                textStyle:{
                    fontFamily:'Arial'
                }
            },
            toolbox: {
                top:25,
                right: 60,
                show: true,
                itemSize: 20,
                itemGap: 17,
                orient: 'horizontal',
                feature: {
                    dataZoom: {title: {zoom: 'Area Zoom', back: 'Back'}},
                    dataView: {
                        title: 'Data',
                        optionToContent: function (opt) {
                            var series = opt.series;

                            var table = '<table class="table table-hover"><thead><tr>'
                                + '<th>' + getLabel(json.y_axis,true)  + ' ' + getLabel(json.y_axis).replace(getLabel(json.y_axis,true), "") + '</th>'
                                + '<th>' + 'Number of Buildings' +'</th>'
                                + '<th>' + schema[2].text + '</th>'
                                + '<th>' + 'Source' + '</th>'
                                + '</tr></thead>\n' +
                                '  <tbody>';

                            if(typeof series[0].data !== 'undefined') {
                                var xmax = series[0].data.length;
                                for (var i = 0, l = xmax; i < l; i++) {
                                    table +='<tr>'
                                        +  '<td>' +  data[0][i] + '</td>'
                                        +  '<td>' +   series[0].data[i] + '</td>'
                                        +  '<td>' +   '' + '</td>'
                                        +  '<td>' +   series[0].name + '</td>'
                                        + '</td>';
                                }
                            }

                            if(typeof series[1] !== 'undefined') {
                                for (var x = 0, ll = data[2][0].series.length; x < ll; x++) {
                                    var myData = data[2][0].series[x];
                                    table +='<tr>'
                                        +  '<td>' +  myData.bin + '</td>'
                                        +  '<td>' +  '1' + '</td>'
                                        +  '<td>' +  myData.year  + '</td>'
                                        +  '<td>' +  myData.name + '</td>'
                                        + '</td>';
                                }
                            }

                            table += "</tbody></textarea>";
                            return table;
                        },
                        contentToOption: function (){
                            window.location.href = url.replace(".json","/histogram.csv");
                            return true;
                        },
                        icon: 'image:///img/spreadsheet.svg',
                        lang: ['Data Source', 'Cancel', 'Download CSV']
                    },
                    restore: {title: 'Restore'},
                    saveAsImage: {title: 'Save', name: "Lab Benchmarking Chart"}
                }
            },
            grid: {show: true, borderColor: 'rgb(127,127,127)'},
            xAxis: [
                {
                    type: 'category',
                    name: getLabel(json.y_axis, false, true),
                    nameLocation: "middle",
                    fontFamily:'Arial',
                    nameTextStyle: {
                        fontSize: 16,
                        fontFamily: 'Arial',
                        padding: [15, 0, 0, 0]
                    },
                    data: cleanBin(data[0]),
                    axisTick: {
                        alignWithLabel: true
                    }
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: "# Buildings in bin",
                    nameLocation: "middle",
                    fontFamily:'Arial',
                    nameTextStyle: {
                        fontSize: 16,
                        fontFamily: 'Arial',
                        padding: [0, 0, 20, 0]
                    }
                }
            ],
            series: [
                {
                    name: 'Peer Buildings',
                    type: 'bar',
                    barWidth: '80%',
                    color: "rgb(127,127,127)",
                    data: data[1]
                }
            ],
            tooltip: {
                formatter: function (obj) {

                    var val = "Buildings<br>" + obj.name + ":" + obj.data;
                    $.each(data[0], function(valKey,valObj){
                            var tmp = valObj.split("-");
                            if (parseFloat(tmp[0]) === parseFloat(obj.name)) {
                                val = "Buildings<br>" + valObj + ":" + obj.data;
                            }
                    });

                    return val;
                }
            }
        };

        // Create Points for Histogram
        if("2" in data) {
            data[2].forEach(function (point) {
                option.series.push(histogramMarkPoint(
                    point,data[0]
                ));
            });
        }

        app.setOption(option);
    });

}

function sortedBar(bar, source, units,total){

    var mp = [];
    var counter = 0;
    bar.data.forEach(function (val) {
        if((val > 0)){

            $.each(bar.series, function(valKey,valObj){
                if(parseFloat(val) === parseFloat(valObj['value'])){
                    mp.push({name: valObj.name, year:valObj['year'], value: valObj['rank'], xAxis: counter, yAxis: val});
                }
            });
        }
        counter++;
    });

    return {
        name: "Your Buildings",
        year: bar.year,
        rank: bar.rank,
        y: bar.y,
        type: 'bar',
        stack: 'one',
        color: "#69a88b",
        data: bar.data,
        lineStyle: {
            normal: {
                color: '#69a88b',
                width: 15
            }
        },
        tooltip: {
            padding: 10,
            backgroundColor: '#fff',
            borderColor: '#eee',
            borderWidth: 1,
            formatter: function (obj) {

                var value = '';
                var year = '';
                var name = '';
                $.each(bar.series, function(valKey,valObj){
                   if(parseFloat(obj.name) === parseFloat(valObj['rank'])){
                       value  = valObj['value'];
                       year  = valObj['year'];
                       name  = valObj['name'];
                   }
                });

                return '<div class="color-black"><div class="tooltip-chart">'
                    + name
                    + '</div>'
                    + source + '：' + value + ' ' + units.replace(source, "") +'<br>'
                    + schema[2].text + '：' + year + '<br>'
                    + schema[3].text + '：' + obj.name + '/' + total  +'<br>'
                    + '</div>';
            }
        },
        markPoint: {
            data:mp,
            tooltip: {
                padding: 10,
                backgroundColor: '#fff',
                borderColor: '#eee',
                borderWidth: 1,
                formatter: function (obj) {
                    return '<div class="color-black"><div class="tooltip-chart">'
                        + obj.name
                        + '</div>'
                        + source + '：' + obj.data.yAxis + ' ' + units.replace(source, "") +'<br>'
                        + schema[2].text + '：' + obj.data.year + '<br>'
                        + schema[3].text + '：' + obj.data.value + '/' + total  +'<br>'
                        + '</div>';
                }
            }
        }
    };
}

function sorted(url, json) {
    var id = json.class;
    var app = echarts.init(document.getElementById(id));
    var legend = json.logged_in ? ['Peer Buildings', 'Your Buildings']: ['Peer Buildings'];
    app.clear();
    app.title = 'Sorted';

    app.showLoading();
    $.getJSON(url, function (data) {
        app.hideLoading();

        var option = {
            toolbox: {
                top:25,
                right: 60,
                show: true,
                itemSize: 20,
                itemGap: 17,
                orient: 'horizontal',
                feature: {
                    dataZoom: {title: {zoom: 'Area Zoom', back: 'Back'}},
                    dataView: {
                        title: 'Data',
                        optionToContent: function (opt) {
                            var series = opt.series;

                            var table = '<table class="table table-hover" id="tableRoster"><thead><tr>'
                                + '<th>' + 'Rank' +'</th>'
                                + '<th>' + getLabel(json.y_axis,true)  + ' ' + getLabel(json.y_axis).replace(getLabel(json.y_axis,true), "") + '</th>'
                                + '<th>' + schema[2].text + '</th>'
                                + '<th>' + 'Source' + '</th>'
                                + '</tr></thead>\n' +
                                '  <tbody>';

                            var $sortedData = [];
                            for (var i = 0, l = series[0].data.length; i < l; i++) {
                                if(series[0].data[i] > 0) {
                                    $sortedData[data[0][i]] = [data[0][i],  series[0].data[i],"",series[0].name];
                                }
                            }

                            if(typeof series[1] !== 'undefined') {
                                for (var x = 0, ll = data[2].series.length; x< ll; x++) {
                                    var myData = data[2].series[x];
                                    $sortedData[myData.rank] = [myData.rank, myData.value, myData.year, myData.name];
                                }
                            }

                            for (var y = 1, lll = $sortedData.length; y < lll; y++) {
                                table +='<tr>'
                                    +  '<td>' +  $sortedData[y][0] + '</td>'
                                    +  '<td>' +  $sortedData[y][1] + '</td>'
                                    +  '<td>' +  $sortedData[y][2]  + '</td>'
                                    +  '<td>' +  $sortedData[y][3] + '</td>'
                                    + '</td>';
                            }

                            table += "</tbody></textarea>";
                            return table;
                        },
                        contentToOption: function (){
                            window.location.href = url.replace(".json","/sorted.csv");
                            return true;
                        },
                        icon: 'image:///img/spreadsheet.svg',
                        lang: ['Data Source', 'Cancel', 'Download CSV']
                    },
                    restore: {title: 'Restore'},
                    saveAsImage: {title: 'Save', name: "Lab Benchmarking Chart"}
                }
            },
            legend: {
                left: 80,
                padding: [15, 0, 0, 0],
                data: legend,
                textStyle:{
                    fontFamily:'Arial'
                }
            },
            tooltip: {},
            grid: {show: true, borderColor: 'rgb(127,127,127)'},
            xAxis: {
                type: 'category',
                name: "Building Rank",
                nameLocation: "middle",
                fontFamily:'Arial',
                nameTextStyle: {
                    fontSize: 16,
                    fontFamily: 'Arial',
                    padding: [15, 0, 0, 0]
                },
                data: data[0]
            },
            yAxis: {
                type: 'value',
                name: getLabel(json.y_axis, false, true),
                nameLocation: "middle",
                fontFamily:'Arial',
                nameTextStyle: {
                    fontSize: 16,
                    fontFamily: 'Arial',
                    padding: [0, 0, 20, 0]
                }
            },
            series: [{
                name: 'Peer Buildings',
                type: 'bar',
                stack: 'one',
                color: "rgb(127,127,127)",
                data: data[1]
            }
            ]
        };

        // Create Points for Histogram
        if("2" in data) {
                option.series.push(sortedBar(
                    data[2],getLabel(json.y_axis,true),getLabel(json.y_axis),data[1].length,data[0]
                ));
        }

        app.setOption(option);
    });

    var loadedSortedColumn = false;
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");

        if(target === "#sorted_tab") {
            if(!loadedSortedColumn) {
                app.resize();
            }
        }
    });
}
