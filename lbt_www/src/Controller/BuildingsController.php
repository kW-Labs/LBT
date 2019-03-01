<?php

namespace App\Controller;

use Cake\Event\Event;

/**
 * Buildings Controller
 *
 * @property \App\Model\Table\UsersTable $Buildings
 * @property bool|object BPD
 * @property bool|object Histogram
 * @property bool|object Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BuildingsController extends AppController
{

    public $title = '';
    public $user_id = '';
    public $action = '';
    public $platform = 'web';
    public $table = 'Buildings';
    public $item_id = 0;
    public $error = false;
    public $results = '';

    public function isAuthorized($user = null)
    {
        $action = $this->request->getParam('action');
        if (in_array($action, ['add', 'edit', 'index', 'editName', 'delete'])) {
            return true;

        }

        return parent::isAuthorized($user);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['home', 'charts', 'bpd', 'states']);
    }

    public function initialize()
    {
        parent::initialize();
        try {
            $this->loadComponent('Histogram');
        } catch (\Exception $e) {
        }

        try {
            $this->loadComponent('BPD');
        } catch (\Exception $e) {
        }

        ini_set('memory_limit', '250M');
    }

    public function home()
    {
        $this->loadModel('Users');
        $user = $this->Users->newEntity();
        $loggedIn = !empty($this->request->getSession()->read('Auth.User.id')) ? true : false;

        $data = '{"filters":{},"x-axis":"total_lab_area","y-axis":"source_eui"}';

        $peer_scatter = json_decode($this->BPD->getScatterPlot($data), true);
        $number_of_buildings_in_bpd = isset($peer_scatter['totals']) ? $peer_scatter['totals']['number_of_buildings_in_bpd'] : 0;

        $this->set(compact('user', 'loggedIn', 'number_of_buildings_in_bpd'));
    }


    private function category_values($field, $options = false, $additional_checks = false)
    {
        $response = [];
        if ($options) {
            $response[] = "--- All ---";
        }

        foreach ($field as $item) {
            if (strpos($item['id'], '_all') !== false) {
                if (!empty($item['sub_section'])) {
                    $response[$item['id']][$item['sub_section']] = ['id' => $item['id'], 'class' => 'all', 'add_label' => empty($item['add_label']) ? '' : $item['add_label'], 'alternative_label' => empty($item['alternative_label']) ? '' : $item['alternative_label'], 'label' => $item['label'], 'sub_section' => !empty($item['sub_section']) ? $item['sub_section'] : ''];
                } else {
                    $response[$item['id']] = ['id' => $item['id'], 'class' => 'all', 'label' => $item['label']];
                }
            } else {
                if (!$additional_checks) {
                    if (isset($item['show_on_add'])) {
                        unset($item['ignore']);
                    }
                }

                if (!isset($item['ignore'])) {
                    if (!empty($item['sub_section'])) {
                        $response[$item['id']][$item['sub_section']] = ['id' => $item['id'], 'add_label' => empty($item['add_label']) ? '' : $item['add_label'], 'alternative_label' => empty($item['alternative_label']) ? '' : $item['alternative_label'], 'label' => $item['label'], 'sub_section' => !empty($item['sub_section']) ? $item['sub_section'] : ''];
                    } else {
                        if ($options) {
                            $label = !empty($item['alternative_label']) ? $item['alternative_label'] : $item['label'];
                            $response[$label] = $item['label'];
                        } else {
                            $response[$item['id']] = ['id' => $item['id'], 'add_label' => empty($item['add_label']) ? '' : $item['add_label'], 'alternative_label' => empty($item['alternative_label']) ? '' : $item['alternative_label'], 'label' => $item['label']];
                        }
                    }
                }
            }
        }

        return $response;
    }

    function getLabels($include_add_tooltip = true)
    {
        $string = file_get_contents(WWW_ROOT . '/json/fields.json');
        $json_a = json_decode($string, true);

        $labels = [];
        foreach ($json_a['fields'] as $field => $item) {
            $id = $item['field_id'];
            if ($include_add_tooltip) {
                $labels[$id] = !empty($item['add_label']) ? $item['add_label'] : (!empty($item['alternative_label']) ? $item['alternative_label'] : $item['label']);
            } else {
                $labels[$id] = !empty($item['label']) ? $item['label'] : '';
            }
        }

        return $labels;
    }

    function getTooltips($include_add_tooltip = true)
    {
        $string = file_get_contents(WWW_ROOT . '/json/fields.json');
        $json_a = json_decode($string, true);

        $tooltips = [];
        foreach ($json_a['fields'] as $field => $item) {
            $id = $item['field_id'];
            if ($include_add_tooltip) {
                $tooltips[$id] = !empty($item['add_tooltip']) ? $item['add_tooltip'] : (!empty($item['tooltip']) ? $item['tooltip'] : '');
            } else {
                $tooltips[$id] = !empty($item['tooltip']) ? $item['tooltip'] : '';
            }
        }

        return $tooltips;
    }

    function getRequiredFields()
    {
        $string = file_get_contents(WWW_ROOT . '/json/fields.json');
        $json_a = json_decode($string, true);

        $requiredFields = [];
        foreach ($json_a['fields'] as $field => $item) {
            $id = $item['field_id'];
            $requiredFields[$id] = !empty($item['required']) ? $item['required'] : false;
        }

        return $requiredFields;
    }

    function getMinMax()
    {
        $string = file_get_contents(WWW_ROOT . '/json/fields.json');
        $json_a = json_decode($string, true);

        $mm = [];
        foreach ($json_a['fields'] as $field => $item) {
            $id = $item['field_id'];
            $my_mm = !empty($item['add_max']) ? [$item['add_min'], $item['add_max'], $item['add_step']] : (!empty($item['max']) ? [$item['min'], $item['max'], $item['step']] : null);
            if ($my_mm) {
                $mm[$id] = $my_mm;
            }
        }

        return $mm;
    }

    function getCategories($options = false, $blank = false)
    {
        $string = file_get_contents(WWW_ROOT . '/json/fields.json');
        $json_a = json_decode($string, true);

        $category = [];
        foreach ($json_a['fields'] as $field => $item) {
            if ($item['id'] === 'climate') {
                $category['climate_zones'] = $this->category_values($item['values']);
                if ($blank) {
                    array_unshift($category['climate_zones'], ["" => ""]);
                }
            }
            if ($item['id'] === 'lab_use') {
                $category['lab_types'] = $this->category_values($item['values']);
                if ($blank) {
                    array_unshift($category['lab_types'], ["" => ""]);
                }
            }
            if ($item['id'] === 'existing_building') {
                $category['building_status'] = $this->category_values($item['values'], false, $options);
            }
            if ($item['id'] === 'data_type') {
                $category['data_type'] = $this->category_values($item['values']);
                if ($blank) {
                    array_unshift($category['data_type'], ["" => ""]);
                }
            }
            if ($item['id'] === 'organization_type') {
                $category['organization_type'] = $this->category_values($item['values']);
                if ($blank) {
                    array_unshift($category['organization_type'], ["" => ""]);
                }
            }
            if ($item['id'] === 'lab_type') {
                $category['lab_type'] = $this->category_values($item['values']);
                if ($blank) {
                    array_unshift($category['lab_type'], ["0" => ""]);
                }
            }
            if ($item['id'] === 'hvac_type') {
                $category['hvac_type'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['hvac_type'], ["0" => ""]);
                }
            }
            if ($item['id'] === 'hvac_control') {
                $category['hvac_control'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['hvac_control'], ["" => ""]);
                }
            }
            if ($item['id'] === 'exhaust_air_energy_recovery') {
                $category['exhaust_air_energy_recovery'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['exhaust_air_energy_recovery'], ["" => ""]);
                }
            }
            if ($item['id'] === 'cooling') {
                $category['cooling'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['cooling'], ["" => ""]);
                }
            }
            if ($item['id'] === 'heating') {
                $category['heating'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['heating'], ["" => ""]);
                }
            }
            if ($item['id'] === 'building_level_combined_heat_power') {
                $category['building_level_combined_heat_power'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['building_level_combined_heat_power'], ["" => ""]);
                }
            }
            if ($item['id'] === 'on_site_renewable_energy_type') {
                $category['on_site_renewable_energy_type'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['on_site_renewable_energy_type'], ["" => ""]);
                }
            }
            if ($item['id'] === 'fume_hood_control') {
                $category['fume_hood_control'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['fume_hood_control'], ["" => ""]);
                }
            }
            if ($item['id'] === 'existing_building') {
                $category['existing_building'] = $this->category_values($item['values'], $options);
                if ($blank) {
                    array_unshift($category['existing_building'], ["" => ""]);
                }
            }
        }

        return $category;
    }

    public function charts()
    {

        $category = $this->getCategories(true);
        $label = $this->getLabels(false);
        $tooltip = $this->getTooltips(false);

        $metrics = ["source_eui" => "Source EUI",
            "site_eui" => "Site EUI",
            "electric_eui" => "Electric EUI",
            "fuel_eui" => "Fuels EUI",
            "peak_electric_demand_intensity" => "Peak Electric Demand",
            "total_utility_cost_intensity" => "Energy Cost Intensity",
//            "water_use_intensity" => "Water Intensity",
//            "water_sewer_cost_intensity" => "Water Cost Intensity",
            "ghg_intensity" => "GHG Intensity",
            "ventilation_electric_eui" => "Ventilation EUI",
            "ventilation_peak_electric_demand_intensity" => "Ventilation Peak Intensity",
            "occupied_required_air_change_rate" => "Ventilation Rate",
            "ventilation_peak_airflow" => "Peak Airflow Intensity",
            "cooling_plant_electric_eui" => "Cooling EUI",
            "cooling_plant_peak_electric_demand_intensity" => "Cooling Peak Elec Intensity",
            "cooling_plant_capacity" => "Cooling Capacity",
            "cooling_plant_peak_load_intensity" => "Cooling Peak Load Intensity",
            "lighting_electric_eui" => "Lighting EUI",
            "lighting_peak_electric_demand_intensity" => "Lighting Peak Intensity",
//            "installed_lighting_intensity" => "Lighting Installed Intensity",
            "process_plug_electric_eui" => "Plug EUI",
            "process_plug_peak_electric_demand_intensity" => "Plug Peak Intensity",
            "total_lab_area" => "Lab Area"];

        $metrics_no_data = ["water_use_intensity" => "Water Intensity", "water_sewer_cost_intensity" => "Water Cost Intensity", "installed_lighting_intensity" => "Lighting Installed Intensity"];

        $this->set(compact('category', 'metrics', 'metrics_no_data', 'label', 'tooltip'));
    }

    public function calculateType($type, $val)
    {
        if ($type === 'source_eui' || $type === 'site_eui' || $type === 'fuel_eui') {
            return $val;
        } else if ($type === 'total_lab_area') {
            return $val * 100;
        } else {
            return $val;
        }

    }

    public function getHistogram($data)
    {

        $this->Histogram->setData($data, 25);
        $info = $this->Histogram->getHistogramInfo();
        $bins = $info['bins'];

        $my_bin_pair = [];
        $my_bin_max = [];
        foreach ($bins as $bin => $values) {
            $tmp = explode("-", $bin);
            $my_bin_pair[$tmp[0]] = count($values);
            $my_bin_max[$tmp[0]] = $tmp[1];
        }

        ksort($my_bin_pair);
        $my_bins = array_keys($my_bin_pair);

        // re-do key
        $new_bins = [];
        foreach ($my_bins as $id => $val) {
            $min_max = $val . "-" . $my_bin_max[$val];
            array_push($new_bins, $min_max);
        }

        $my_counts = array_values($my_bin_pair);

        return [$new_bins, $my_counts];
    }

    public function getBPD($params)
    {
        $type = $params['type'];
        $data = $params['data'];
        $data_json = json_decode($data, true);
        $x_axis_val = $data_json['x-axis'];
        $y_axis_val = isset($data_json['y-axis']) ? $data_json['y-axis'] : '';
        $decimal_places = ["source_eui" => 0,
            "site_eui" => 0,
            "electric_eui" => 0,
            "fuel_eui" => 0,
            "peak_electric_demand_intensity" => 1,
            "total_utility_cost_intensity" => 2,
            "water_use_intensity" => 1,
            "water_sewer_cost_intensity" => 2,
            "ghg_intensity" => 1,
            "ventilation_electric_eui" => 1,
            "ventilation_peak_electric_demand_intensity" => 2,
            "occupied_required_air_change_rate" => 2,
            "ventilation_peak_airflow" => 2,
            "cooling_plant_electric_eui" => 1,
            "cooling_plant_peak_electric_demand_intensity" => 2,
            "cooling_plant_capacity" => 0,
            "cooling_plant_peak_load_intensity" => 0,
            "lighting_electric_eui" => 1,
            "lighting_peak_electric_demand_intensity" => 2,
            "installed_lighting_intensity" => 2,
            "process_plug_electric_eui" => 1,
            "process_plug_peak_electric_demand_intensity" => 2,
            "total_lab_area" => 3
        ];

        // Only Get user Data
        $user_data = json_decode($data, true);
        if (is_array($user_data['filters'])) {
            if (empty($user_data['filters'])) {
                $user_data["filters"] = new \stdClass();
            }
        }
        $user_data["include_global"] = false;
        $user_data["fields"] = isset($user_data["y-axis"]) ? [$user_data["x-axis"], $user_data["y-axis"], "year", "facility_name"] : [$user_data["x-axis"], "year", "facility_name"];
        unset($user_data['x-axis']);
        unset($user_data['y-axis']);
        $user_data = json_encode($user_data);

        if ($type == 'scatter') {
            // BPD Histogram
            $peer_scatter = $this->BPD->getScatterPlot($data);

            $peer_scatter_data = json_decode($peer_scatter, true);
            $peer_scatter_data_arr = [];
            if (!empty($peer_scatter_data['scatterplot'])) {
                foreach ($peer_scatter_data['scatterplot'] as $id => $arr) {
                    $x_val = $this->calculateType($x_axis_val, $arr[0]);
                    $y_val = $this->calculateType($y_axis_val, $arr[1]);
                    $peer_scatter_data_arr[] = [round($x_val, $decimal_places[$x_axis_val]), round($y_val, $decimal_places[$y_axis_val]), ''];
                }
            }

            // Get User data
            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');
            $user_scatter = $this->BPD->getUserScatterPlot($email, $api_key, $user_data);
            $user_scatter_data = json_decode($user_scatter, true);
            $user_scatter_data_arr = [];
            if (!empty($user_scatter_data['table']['results'])) {
                foreach ($user_scatter_data['table']['results'] as $id => $arr) {
                    $x_val = $this->calculateType($x_axis_val, $arr[0]);
                    $y_val = $this->calculateType($y_axis_val, $arr[1]);
                    $user_scatter_data_arr[] = [round($x_val, $decimal_places[$x_axis_val]), round($y_val, $decimal_places[$y_axis_val]), $arr[2], $arr[3]];
                }
            }

            if (count($peer_scatter_data_arr) > 0 || count($user_scatter_data_arr) > 0) {
                $scatter = [$peer_scatter_data_arr,
                    $user_scatter_data_arr,
                    ['x_mean' => round($peer_scatter_data['totals']['mean'][0], $decimal_places[$x_axis_val]),
                        'y_mean' => round($peer_scatter_data['totals']['mean'][1], $decimal_places[$y_axis_val]),
                        'x_median' => round($peer_scatter_data['totals']['percentile_50'][0], $decimal_places[$x_axis_val]),
                        'y_median' => round($peer_scatter_data['totals']['percentile_50'][1], $decimal_places[$y_axis_val]),
                        'number_of_buildings_in_bpd' => $peer_scatter_data['totals']['number_of_buildings_in_bpd'],
                        'number_of_matching_buildings' => $peer_scatter_data['totals']['number_of_matching_buildings']
                    ]
                ];

                $data = $scatter;
            } else {
                $data = ['error' => true, 'msg' => 'No Results'];
            }
        } else if ($type == 'histogram') {
            // BPD Histogram
            $peer_scatter = $this->BPD->getScatterPlot($data);

            $peer_scatter_data = json_decode($peer_scatter, true);
            $peer_scatter_data_arr = [];
            if (!empty($peer_scatter_data['scatterplot'])) {
                foreach ($peer_scatter_data['scatterplot'] as $id => $arr) {
                    $x_val = $this->calculateType($x_axis_val, $arr[0]);
                    $peer_scatter_data_arr[] = round($x_val, $decimal_places[$x_axis_val]);
                }
            }

            // Get User data
            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');
            $user_scatter = $this->BPD->getUserScatterPlot($email, $api_key, $user_data);
            $user_scatter_data = json_decode($user_scatter, true);
            $user_scatter_data_arr = [];
            $user_scatter_data_arr_full = [];
            if (!empty($user_scatter_data['table']['results'])) {
                foreach ($user_scatter_data['table']['results'] as $id => $arr) {
                    $x_val = $this->calculateType($x_axis_val, $arr[0]);
                    $user_scatter_data_arr[] = (float)$x_val;
                    array_push($user_scatter_data_arr_full, ["index" => $x_val, "data" => round($x_val, $decimal_places[$x_axis_val]), "y" => round($x_val, $decimal_places[$x_axis_val]), "year" => $arr[1], "name" => $arr[2]]);
                }
            }

            if (isset($peer_scatter_data['scatterplot'])) {
                if (count($peer_scatter_data['scatterplot']) > 0) {
                    $myPeerHistogram = $this->getHistogram($peer_scatter_data_arr);
                    $peer_bins = $myPeerHistogram[0];
                    $peer_counts = $myPeerHistogram[1];
                } else {
                    $peer_bins = [];
                    $peer_counts = [];
                }

                // Add missing blank columns
                $peer_bin_gap = null;
                $peer_bin_min = null;
                $peer_bin_max = null;
                foreach ($peer_bins as $k => $bin) {
                    $myBin = explode("-", $bin);
                    $min = $myBin[0];
                    $max = $peer_bin_max = $myBin[1];
                    $peer_bin_gap = $max - $min;

                    if (!isset($peer_bin_min)) {
                        $peer_bin_min = $min;
                    }

                }

                if(isset($peer_bin_min)) {
                    if (($peer_bin_max - $peer_bin_min) > 0) {
                        $estimated_bin_count = ceil(($peer_bin_max - $peer_bin_min) / $peer_bin_gap);
                    } else {
                        $estimated_bin_count = 0;
                    }
                }else{
                    $estimated_bin_count = 0;
                }
                $actual_bin_count = count($peer_bins);

                $check_peer_bins = $peer_bins;
                $counter = 0;
                if ($estimated_bin_count > $actual_bin_count) {
                    foreach ($check_peer_bins as $k => $bin) {
                        $myBin = explode("-", $bin);
                        $min = $myBin[0];
                        $max = $myBin[1];

                        if (isset($check_peer_bins[$k + 1])) {
                            $myNextBin = explode("-", $check_peer_bins[$k + 1]);
                            $minNext = $myNextBin[0];
                            $maxNext = $myNextBin[1];
                            if ($minNext !== $max) {
                                $new_bin_count = ceil(($minNext - $max) / $peer_bin_gap);
                                $start = $max;
                                $index = $k + 1 + $counter;
                                for ($x = 0; $x < $new_bin_count; $x++) {
                                    $my_new_bin = $start . "-" . ($start + $peer_bin_gap);
                                    array_splice($peer_bins, $index, 0, $my_new_bin);
                                    array_splice($peer_counts, $index, 0, 0);
                                    $start += $peer_bin_gap;
                                    $index++;
                                    $counter++;
                                }
                            }

                        }

                    }
                }

                // Check for values outside normal bin range
                $outliers = [];
                $user_bin_data = [];
                $user_bins = [];
                $peer_bin_gap = null;
                $peer_bin_min = null;
                $peer_bin_max = null;
                foreach ($user_scatter_data_arr as $kk => $v) {
                    $matched_bin = false;
                    foreach ($peer_bins as $k => $bin) {
                        $myBin = explode("-", $bin);
                        $min = $myBin[0];
                        $max = $peer_bin_max = $myBin[1];
                        $peer_bin_gap = $max - $min;

                        if (!isset($peer_bin_min)) {
                            $peer_bin_min = $min;
                        }

                        // Value is in Bin so update count
                        if ($v >= $min && $v <= $max) {
                            $matched_bin = true;
                            $user_bin_data[$kk] = ['name' => $user_scatter_data_arr_full[$kk]['name'], 'value' => $user_scatter_data_arr_full[$kk]['index'], 'year' => $user_scatter_data_arr_full[$kk]['year'], 'bin' => $bin];

                            if (!in_array($bin, $user_bins)) {
                                array_push($user_bins, $bin);
                            }
                        }
                    }

                    if (!$matched_bin) {
                        $outliers[] = $v;
                        $user_bin_data[$kk] = ['name' => $user_scatter_data_arr_full[$kk]['name'], 'value' => $user_scatter_data_arr_full[$kk]['index'], 'year' => $user_scatter_data_arr_full[$kk]['year'], 'bin' => null];
                    }
                }

                // Count User Bins
                $user_bin_data_count = [];
                foreach ($user_bin_data as $k => $v) {
                    $id = $v['bin'];
                    if (!isset($user_bin_data_count[$v['bin']])) {
                        $user_bin_data_count["$id"] = 1;
                    } else {
                        $user_bin_data_count["$id"]++;
                    }
                }

                $user_counts = [];
                foreach ($peer_bins as $k => $bin) {
                    $user_counts[] = isset($user_bin_data_count["$bin"]) ? $user_bin_data_count["$bin"] : 0;
                }

                // Add Blank Column until you get to the outlier columns
                if (count($outliers) > 0) {

                    // Generate New bins
                    $outlier_bin_over = [];
                    $outlier_bin_under = [];
                    foreach ($outliers as $k => $outlier) {
                        if (($outlier - $peer_bin_max) > 0) {
                            if (($peer_bin_gap) > 0) {
                                $new_bin_count = ceil(($outlier - $peer_bin_max) / $peer_bin_gap);
                            } else {
                                $new_bin_count = 0;
                            }

                            $start = $peer_bin_max;
                            for ($x = 0; $x < $new_bin_count; $x++) {
                                $my_new_bin = $start . "-" . ($start + $peer_bin_gap);
                                if (!in_array($my_new_bin, $outlier_bin_over)) {
                                    $outlier_bin_over[] = $my_new_bin;
                                    $start += $peer_bin_gap;
                                }
                            }
                        } else {
                            $new_bin_count = ceil(($peer_bin_min - $outlier) / $peer_bin_gap);

                            $start = $peer_bin_min;
                            for ($x = 0; $x < $new_bin_count; $x++) {
                                $my_new_bin = ($start - $peer_bin_gap) . "-" . $start;
                                if (!in_array($my_new_bin, $outlier_bin_under)) {
                                    $outlier_bin_under[] = $my_new_bin;
                                    $start -= $peer_bin_gap;
                                }
                            }
                            $outlier_bin_under = array_reverse($outlier_bin_under);
                        }
                    }


                    // Fill Blank Count Over
                    $outlier_user_count_over = [];
                    $outlier_peer_count_over = [];
                    foreach ($outliers as $kk => $outlier) {

                        foreach ($outlier_bin_over as $k => $bin) {
                            $myBin = explode("-", $bin);
                            $min = $myBin[0];
                            $max = $myBin[1];
                            $outlier_peer_count_over[$k] = 0;
                            if ($outlier >= $min && $outlier <= $max) {

                                if (!isset($outlier_user_count_over[$k])) {
                                    $outlier_user_count_over[$k] = 1;
                                } else {
                                    $outlier_user_count_over[$k]++;
                                }
                            } else {
                                if (!isset($outlier_user_count_over[$k])) {
                                    $outlier_user_count_over[$k] = 0;
                                }
                            }
                        }
                    }

                    // Add New Bins and Counts that are Over the normal bin range
                    $peer_bins = array_merge($peer_bins, $outlier_bin_over);
                    $peer_counts = array_merge($peer_counts, $outlier_peer_count_over);
                    $user_counts = array_merge($user_counts, $outlier_user_count_over);

                    // Update User Data Bin
                    foreach ($outliers as $kk => $outlier) {
                        foreach ($outlier_bin_over as $k => $bin) {
                            $myBin = explode("-", $bin);
                            $min = $myBin[0];
                            $max = $myBin[1];
                            if ($outlier >= $min && $outlier <= $max) {
                                foreach ($user_bin_data as $k => $v) {
                                    if ((float)$v['value'] == (float)$outlier && is_null($v['bin'])) {
                                        $user_bin_data[$k]['bin'] = $bin;
                                    }
                                }
                            }
                        }
                    }

                    // Fill Blank Count Over
                    $outlier_user_count_under = [];
                    $outlier_peer_count_under = [];
                    foreach ($outliers as $kk => $outlier) {

                        foreach ($outlier_bin_under as $k => $bin) {
                            $myBin = explode("-", $bin);
                            $min = $myBin[0];
                            $max = $myBin[1];
                            $outlier_peer_count_under[$k] = 0;
                            if ($outlier >= $min && $outlier <= $max) {

                                if (!isset($outlier_user_count_under[$k])) {
                                    $outlier_user_count_under[$k] = 1;
                                } else {
                                    $outlier_user_count_under[$k]++;
                                }
                            } else {
                                if (!isset($outlier_user_count_under[$k])) {
                                    $outlier_user_count_under[$k] = 0;
                                }
                            }
                        }
                    }

                    // Add New Bins and Counts that are Under the normal bin range
                    $peer_bins = array_merge($outlier_bin_under, $peer_bins);
                    $peer_counts = array_merge($outlier_peer_count_under, $peer_counts);
                    $user_counts = array_merge($outlier_user_count_under, $user_counts);

                    // Update User Data Bin
                    foreach ($outliers as $kk => $outlier) {
                        foreach ($outlier_bin_under as $k => $bin) {
                            $myBin = explode("-", $bin);
                            $min = $myBin[0];
                            $max = $myBin[1];
                            if ($outlier >= $min && $outlier <= $max) {
                                foreach ($user_bin_data as $k => $v) {
                                    if ((float)$v['value'] == (float)$outlier && is_null($v['bin'])) {
                                        $user_bin_data[$k]['bin'] = $bin;
                                    }
                                }
                            }
                        }
                    }
                }

                if (count($peer_bins) > 0 || count($user_counts) > 0) {

                    $histogram = [$peer_bins, $peer_counts, [["data" => $user_counts, "series" => $user_bin_data]],
                        ['x_mean' => round($peer_scatter_data['totals']['mean'][0], $decimal_places[$x_axis_val]),
                            'y_mean' => !empty($y_axis_val) ? round($peer_scatter_data['totals']['mean'][1], $decimal_places[$y_axis_val]) : 0,
                            'x_median' => round($peer_scatter_data['totals']['percentile_50'][0], $decimal_places[$x_axis_val]),
                            'y_median' => !empty($y_axis_val) ? round($peer_scatter_data['totals']['percentile_50'][1], $decimal_places[$y_axis_val]) : 0,
                            'number_of_buildings_in_bpd' => $peer_scatter_data['totals']['number_of_buildings_in_bpd'],
                            'number_of_matching_buildings' => $peer_scatter_data['totals']['number_of_matching_buildings']
                        ]
                    ];
                    $data = $histogram;

                } else {
                    $data = ['error' => true, 'msg' => 'No Results'];
                }
            } else {
                $data = ['error' => true, 'msg' => 'No Results'];
            }

        } else {
            $peer_sorted = $this->BPD->getScatterPlot($data);
            $peer_scatter_data = json_decode($peer_sorted, true);
            $peer_scatter_data_arr = [];
            if (!empty($peer_scatter_data['scatterplot'])) {
                foreach ($peer_scatter_data['scatterplot'] as $id => $arr) {
                    $x_val = $this->calculateType($x_axis_val, $arr[0]);
                    array_push($peer_scatter_data_arr, round($x_val, $decimal_places[$x_axis_val]));
                }

                sort($peer_scatter_data_arr);
            }

            // Get User data
            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');
            $user_scatter = $this->BPD->getUserScatterPlot($email, $api_key, $user_data);
            $user_scatter_data = json_decode($user_scatter, true);
            $user_scatter_data_arr = [];
            $user_scatter_data_arr_full = [];
            $reverse_peer_sorted_data_arr_only = [];
            $reverse_peer_sorted_data_arr_full = [];
            if (!empty($user_scatter_data['table']['results'])) {
                foreach ($user_scatter_data['table']['results'] as $id => $arr) {
                    $x_val = $this->calculateType($x_axis_val, $arr[0]);
                    array_push($peer_scatter_data_arr, $x_val);
                    array_push($user_scatter_data_arr_full, !empty($y_axis_val) ? ["index" => $x_val, "value" => round($x_val, $decimal_places[$x_axis_val]), "year" => $arr[2], "name" => $arr[3]] : ["index" => $x_val, "value" => round($x_val, $decimal_places[$x_axis_val]), "year" => $arr[1], "name" => $arr[2]]);
                    array_push($user_scatter_data_arr, $x_val);
                }

                sort($peer_scatter_data_arr);
                sort($user_scatter_data_arr_full);
                sort($user_scatter_data_arr);

                // Count Values
                $user_scatter_data_count_arr = [];
                $user_scatter_data_arr_count = [];
                foreach ($user_scatter_data_arr as $k => $v) {
                    if (!in_array($v, $user_scatter_data_count_arr)) {
                        $user_scatter_data_arr_count[$v] = 1;
                        array_push($user_scatter_data_count_arr, $v);
                    } else {
                        $user_scatter_data_arr_count[$v]++;
                    }
                }

                $reverse_peer_sorted_data_only_count_arr = [];
                $reverse_peer_sorted_data_arr_only_count = [];
                foreach ($peer_scatter_data_arr as $kk => $vv) {
                    foreach ($user_scatter_data_arr as $k => $v) {
                        if ((float)$vv === (float)$v) {

                            // Check Value is only shown as much as the counted times
                            if (!in_array($v, $reverse_peer_sorted_data_only_count_arr)) {
                                $reverse_peer_sorted_data_arr_only[$kk] = round($v, $decimal_places[$x_axis_val]);
                                $reverse_peer_sorted_data_arr_only_count[$v] = 1;
                                array_push($reverse_peer_sorted_data_only_count_arr, $v);
                            } else {
                                if ($user_scatter_data_arr_count[$v] > $reverse_peer_sorted_data_arr_only_count[$v]) {
                                    $reverse_peer_sorted_data_arr_only[$kk] = round($v, $decimal_places[$x_axis_val]);
                                    $reverse_peer_sorted_data_arr_only_count[$v]++;
                                }
                            }
                        } else {
                            if (!isset($reverse_peer_sorted_data_arr_only[$kk])) {
                                $reverse_peer_sorted_data_arr_only[$kk] = 0;
                            }
                        }
                    }
                }

                // Create User Buildings
                foreach ($user_scatter_data_arr as $kk => $vv) {
                    $rank = 0;
                    foreach ($peer_scatter_data_arr as $k => $v) {
                        if ((float)$vv === (float)$v) {
                            $peer_scatter_data_arr[$k] = 0;
                            $rank = $k + 1;
                            break;
                        }
                    }

                    $reverse_peer_sorted_data_arr_full[$kk] = $user_scatter_data_arr_full[$kk];
                    $reverse_peer_sorted_data_arr_full[$kk]['rank'] = $rank;
                }
            }

            if (count($peer_scatter_data_arr) > 0 || count($user_scatter_data_arr) > 0) {

                $peer_ranks = array_keys($peer_scatter_data_arr);
                foreach ($peer_ranks as $rank) {
                    $peer_ranks[$rank] += 1;
                }

                $sorted = [$peer_ranks,
                    $peer_scatter_data_arr,
                    ['data' => $reverse_peer_sorted_data_arr_only, 'series' => $reverse_peer_sorted_data_arr_full],
                    ['x_mean' => round($peer_scatter_data['totals']['mean'][0], $decimal_places[$x_axis_val]),
                        'y_mean' => !empty($y_axis_val) ? round($peer_scatter_data['totals']['mean'][1], $decimal_places[$y_axis_val]) : 0,
                        'x_median' => round($peer_scatter_data['totals']['percentile_50'][0], $decimal_places[$x_axis_val]),
                        'y_median' => !empty($y_axis_val) ? round($peer_scatter_data['totals']['percentile_50'][1], $decimal_places[$y_axis_val]) : 0,
                        'number_of_buildings_in_bpd' => $peer_scatter_data['totals']['number_of_buildings_in_bpd'],
                        'number_of_matching_buildings' => $peer_scatter_data['totals']['number_of_matching_buildings']
                    ]];

                $data = $sorted;
            } else {
                $data = ['error' => true, 'msg' => 'No Results'];
            }
        }

        return $data;
    }

    public function bpd($csv = false)
    {
        $params = $this->request->getQueryParams();
        $data = $this->getBPD($params);
        if ($csv) {
            $type = $params['type'];
            $data_json = json_decode($params['data'], true);
            $metrics = [["id" => "source_eui", "label" => "Source EUI", "units" => "kBtu/sf/yr"],
                ["id" => "site_eui", "label" => "Site EUI", "units" => "kBtu/sf/yr"],
                ["id" => "electric_eui", "label" => "Electric EUI", "units" => "kWh/sf/yr"],
                ["id" => "fuel_eui", "label" => "Fuels EUI", "units" => "kBtu/sf/yr"],
                ["id" => "peak_electric_demand_intensity", "label" => "Peak Electric Demand", "units" => "W/sf"],
                ["id" => "total_utility_cost_intensity", "label" => "Energy Cost Intensity", "units" => "$/sf/yr"],
                ["id" => "water_use_intensity", "label" => "Water Intensity", "units" => "gal/sf/yr"],
                ["id" => "water_sewer_cost_intensity", "label" => "Water Cost Intensity", "units" => "$/sf/yr"],
                ["id" => "ghg_intensity", "label" => "GHG Intensity", "units" => "lbs/sf/yr"],
                ["id" => "ventilation_electric_eui", "label" => "Ventilation EUI", "units" => "kWh/sf/yr"],
                ["id" => "ventilation_peak_electric_demand_intensity", "label" => "Ventilation Peak Intensity", "units" => "W/sf"],
                ["id" => "occupied_required_air_change_rate", "label" => "Ventilation Rate", "units" => "ACH"],
                ["id" => "ventilation_peak_airflow", "label" => "Peak Airflow Intensity", "units" => "cfm/sf"],
                ["id" => "cooling_plant_electric_eui", "label" => "Cooling EUI", "units" => "kWh/sf/yr"],
                ["id" => "cooling_plant_peak_electric_demand_intensity", "label" => "Cooling Peak Elec Intensity", "units" => "W/sf"],
                ["id" => "cooling_plant_capacity", "label" => "Cooling Capacity", "units" => "sf/ton"],
                ["id" => "cooling_plant_peak_load_intensity", "label" => "Cooling Peak Load Intensity", "units" => "sf/ton"],
                ["id" => "lighting_electric_eui", "label" => "Lighting EUI", "units" => "kWh/sf/yr"],
                ["id" => "lighting_peak_electric_demand_intensity", "label" => "Lighting Peak Intensity", "units" => "W/sf"],
                ["id" => "installed_lighting_intensity", "label" => "Lighting Installed Intensity", "units" => "W/sf"],
                ["id" => "process_plug_electric_eui", "label" => "Plug EUI", "units" => "kWh/sf/yr"],
                ["id" => "process_plug_peak_electric_demand_intensity", "label" => "Plug Peak Intensity", "units" => "W/sf"],
                ["id" => "total_lab_area", "label" => "Lab Area", "units" => "%"]];

            $x_axis_val = $data_json['x-axis'];
            $x_axis_label = "";
            $y_axis_val = isset($data_json['y-axis']) ? $data_json['y-axis'] : '';
            $y_axis_label = "";

            foreach ($metrics as $metric) {
                if ($metric['id'] == $x_axis_val) {
                    $x_axis_label = $metric ['label'] . "(" . $metric['units'] . ")";
                }
                if (isset($data_json['y-axis'])) {
                    if ($metric['id'] == $y_axis_val) {
                        $y_axis_label = $metric ['label'] . "(" . $metric['units'] . ")";
                    }
                }
            }

            if ($type == 'scatter') {

                $myNewData = [];
                foreach ($data[0] as $obj) {
                    $myNewData[] = [$obj[0], $obj[1], "", "Peer Buildings"];
                }

                array_unshift($myNewData, [$x_axis_label, $y_axis_label, 'Data Year', 'Source']);

                $myNewUserData = [];
                foreach ($data[1] as $obj) {
                    $myNewUserData[] = [$obj[0], $obj[1], $obj[2], $obj[3]];
                }

                $myData = array_merge($myNewData, $myNewUserData);
            } else if ($type == 'histogram') {
                $myData = [];
                $myData[] = [$x_axis_label, 'Number of Buildings', 'Data Year', 'Source'];
                $x = 0;
                foreach ($data[0] as $bin) {
                    $myData[] = [$bin, $data[1][$x], "", "Peer Buildings"];
                    $x++;
                }

                foreach ($data[2][0]['series'] as $obj) {
                    $myData[] = [$obj['bin'], 1, $obj['year'], $obj['name']];
                }

            } else {
                $myData = [];
                $myData[] = ['Rank', $x_axis_label, 'Data Year', 'Source'];
                $x = 0;
                foreach ($data[0] as $bin) {
                    if ($data[1][$x] !== 0) {
                        $myData[] = [$bin, $data[1][$x], "", "Peer Building"];
                    }
                    $x++;
                }

                foreach ($data[2]['series'] as $obj) {
                    $myData[] = [$obj['rank'], $obj['value'], $obj['year'], $obj['name']];
                }

                sort($myData);
            }

            $_serialize = 'myData';
            $this->viewBuilder()->setClassName('CsvView.Csv');
            $this->set(compact('myData', '_serialize'));
        } else {
            $this->response->getBody()->write(json_encode($data));
            $this->response = $this->response->withType('json');
            return $this->response;
        }
    }

    public function getBuildings()
    {
        $this->loadModel('Users');
        $email = $this->Auth->user('email');
        $api_key = $this->Auth->user('api_key');

        $body = '{"filters":{}, "fields": ["id","facility_id","facility_name","year"], "include_global":"False", "include_all_years": "True"}';
        $buildings = json_decode($this->BPD->getBuildingListInfo($email, $api_key, $body), true);

        $myBuildings = [];
        $myBuildingFacility = [];
        $myBuildingYears = [];
        if (isset($buildings['table'])) {
            foreach ($buildings['table']['results'] as $building) {
                // 0 => id, 1 => facility_id, 2 => name, 3 => year
                $myBuildings[$building[1]][] = ['year' => $building[3], 'id' => $building[0]];
                $myBuildingYears[$building[1]][] = $building[3];

                // Get the last building id
                $myBuildingFacility[$building[1]] = ['id' => $building[1], 'name' => $building[2], 'last_building_id' => $building[0]];
            }
        }

        return [$myBuildings, $myBuildingFacility, $myBuildingYears];
    }

    public function index()
    {
        $buildingLists = $this->getBuildings();
        $myBuildings = $buildingLists[0];
        $myBuildingFacility = $buildingLists[1];

        $this->set(compact('myBuildings', 'myBuildingFacility'));
    }

    public function getStates($only_abbr = true)
    {
        $states = ["Unknown" => "None",
            "AL" => "Alabama",
            "AK" => "Alaska",
            "AZ" => "Arizona",
            "AR" => "Arkansas",
            "CA" => "California",
            "CO" => "Colorado",
            "CT" => "Connecticut",
            "DE" => "Delaware",
            "DC" => "District of Columbia",
            "FL" => "Florida",
            "GA" => "Georgia",
            "HI" => "Hawaii",
            "ID" => "Idaho",
            "IL" => "Illinois",
            "IN" => "Indiana",
            "IA" => "Iowa",
            "KS" => "Kansas",
            "KY" => "Kentucky",
            "LA" => "Louisiana",
            "ME" => "Maine",
            "MT" => "Montana",
            "NE" => "Nebraska",
            "NV" => "Nevada",
            "NH" => "New Hampshire",
            "NJ" => "New Jersey",
            "NM" => "New Mexico",
            "NY" => "New York",
            "NC" => "North Carolina",
            "ND" => "North Dakota",
            "OH" => "Ohio",
            "OK" => "Oklahoma",
            "OR" => "Oregon",
            "MD" => "Maryland",
            "MA" => "Massachusetts",
            "MI" => "Michigan",
            "MN" => "Minnesota",
            "MS" => "Mississippi",
            "MO" => "Missouri",
            "PA" => "Pennsylvania",
            "RI" => "Rhode Island",
            "SC" => "South Carolina",
            "SD" => "South Dakota",
            "TN" => "Tennessee",
            "TX" => "Texas",
            "UT" => "Utah",
            "VT" => "Vermont",
            "VA" => "Virginia",
            "WA" => "Washington",
            "WV" => "West Virginia",
            "WI" => "Wisconsin",
            "WY" => "Wyoming",
        ];

        if ($only_abbr) {
            unset($states['0']);
            return array_keys($states);
        } else {
            return $states;
        }
    }

    public function states($only_abbr = true)
    {
        $states = $this->getStates($only_abbr);
        $data = json_encode($states);
        $this->response->getBody()->write($data);
        $this->response = $this->response->withType('json');
        return $this->response;
    }

    public function operational()
    {

    }

    public function dataCleanup($ui_data)
    {
        $bpd_data = [];
        if ($ui_data['floor_area'] > 0) {

            /** U / Floor Area */
            $bpd_data['floor_area'] = $ui_data['floor_area'];
            $bpd_data['electric_eui'] = $ui_data['annual_electric_use'] / $bpd_data['floor_area'];
            $bpd_data['total_utility_cost_intensity'] = isset($ui_data['total_utility_cost_intensity']) ? $ui_data['total_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['water_use_intensity'] = isset($ui_data['water_use_intensity']) ? $ui_data['water_use_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['water_sewer_cost_intensity'] = isset($ui_data['water_sewer_cost_intensity']) ? $ui_data['water_sewer_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['ventilation_electric_eui'] = isset($ui_data['ventilation_electric_eui']) ? $ui_data['ventilation_electric_eui'] / $bpd_data['floor_area'] : null;
            $bpd_data['ventilation_peak_airflow'] = isset($ui_data['ventilation_peak_airflow']) ? $ui_data['ventilation_peak_airflow'] / $bpd_data['floor_area'] : null;
            $bpd_data['cooling_plant_electric_eui'] = isset($ui_data['cooling_plant_electric_eui']) ? $ui_data['cooling_plant_electric_eui'] / $bpd_data['floor_area'] : null;
            $bpd_data['lighting_electric_eui'] = isset($ui_data['lighting_electric_eui']) ? $ui_data['lighting_electric_eui'] / $bpd_data['floor_area'] : null;
            $bpd_data['process_plug_electric_eui'] = isset($ui_data['process_plug_electric_eui']) ? $ui_data['process_plug_electric_eui'] / $bpd_data['floor_area'] : null;
            $bpd_data['number_of_people'] = isset($ui_data['number_of_people']) ? $ui_data['number_of_people'] / $bpd_data['floor_area'] : null;
            $bpd_data['on_site_renewable_electric_eui'] = isset($ui_data['on_site_renewable_electric_eui']) ? $ui_data['on_site_renewable_electric_eui'] / $bpd_data['floor_area'] : null;
            $bpd_data['electric_utility_cost_intensity'] = isset($ui_data['electric_utility_cost_intensity']) ? $ui_data['electric_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['natural_gas_utility_cost_intensity'] = isset($ui_data['natural_gas_utility_cost_intensity']) ? $ui_data['natural_gas_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['fuel_oil_utility_cost_intensity'] = isset($ui_data['fuel_oil_utility_cost_intensity']) ? $ui_data['fuel_oil_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['other_fuel_utility_cost_intensity'] = isset($ui_data['other_fuel_utility_cost_intensity']) ? $ui_data['other_fuel_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['district_chilled_water_utility_cost_intensity'] = isset($ui_data['district_chilled_water_utility_cost_intensity']) ? $ui_data['district_chilled_water_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['district_hot_water_utility_cost_intensity'] = isset($ui_data['district_hot_water_utility_cost_intensity']) ? $ui_data['district_hot_water_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['district_steam_utility_cost_intensity'] = isset($ui_data['district_steam_utility_cost_intensity']) ? $ui_data['district_steam_utility_cost_intensity'] / $bpd_data['floor_area'] : null;
            $bpd_data['total_lab_area'] = isset($ui_data['total_lab_area']) ? $ui_data['total_lab_area'] / $bpd_data['floor_area'] : null;

            /** U / Total Lab Area **/
            $bpd_data['dry_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['dry_lab_area'])) ? $ui_data['dry_lab_area'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['biological_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['biological_lab_area'])) ? $ui_data['biological_lab_area'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['chemical_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['chemical_lab_area'])) ? $ui_data['chemical_lab_area'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['physical_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['physical_lab_area'])) ? $ui_data['physical_lab_area'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['vivarium_area'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['vivarium_area'])) ? $ui_data['vivarium_area'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['other_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['other_lab_area'])) ? $ui_data['other_lab_area'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['number_of_ducted_fume_hoods'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['number_of_ducted_fume_hoods'])) ? $ui_data['number_of_ducted_fume_hoods'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['number_of_filtering_fume_hoods'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['number_of_filtering_fume_hoods'])) ? $ui_data['number_of_filtering_fume_hoods'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;
            $bpd_data['total_fume_hood_length'] = ($bpd_data['total_lab_area'] > 0 && isset($ui_data['total_fume_hood_length'])) ? $ui_data['total_fume_hood_length'] / ($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : 0;

            /** U / Floor Area * 1000 **/
            $bpd_data['peak_electric_demand_intensity'] = isset($ui_data['peak_electric_demand_intensity']) ? $ui_data['peak_electric_demand_intensity'] / $bpd_data['floor_area'] * 1000 : null;
            $bpd_data['ventilation_peak_electric_demand_intensity'] = isset($ui_data['ventilation_peak_electric_demand_intensity']) ? $ui_data['ventilation_peak_electric_demand_intensity'] / $bpd_data['floor_area'] * 1000 : null;
            $bpd_data['cooling_plant_peak_electric_demand_intensity'] = isset($ui_data['cooling_plant_peak_electric_demand_intensity']) ? $ui_data['cooling_plant_peak_electric_demand_intensity'] / $bpd_data['floor_area'] * 1000 : null;
            $bpd_data['lighting_peak_electric_demand_intensity'] = isset($ui_data['lighting_peak_electric_demand_intensity']) ? $ui_data['lighting_peak_electric_demand_intensity'] / $bpd_data['floor_area'] * 1000 : null;
            $bpd_data['process_plug_peak_electric_demand_intensity'] = isset($ui_data['process_plug_peak_electric_demand_intensity']) ? $ui_data['process_plug_peak_electric_demand_intensity'] / $bpd_data['floor_area'] * 1000 : null;

            /** Floor Area / U **/
            $bpd_data['cooling_plant_capacity'] = (isset($ui_data['cooling_plant_capacity'])) ? (($ui_data['cooling_plant_capacity'] > 0) ? ($bpd_data['floor_area'] / $ui_data['cooling_plant_capacity']) : 0) : null;
            $bpd_data['cooling_plant_peak_load_intensity'] = (isset($ui_data['cooling_plant_peak_load_intensity'])) ? (($ui_data['cooling_plant_peak_load_intensity'] > 0) ? ($bpd_data['floor_area'] / $ui_data['cooling_plant_peak_load_intensity']) : 0) : null;

            /** Unit Conversations **/
            // Therms or MMBtu
            // U*1.037/floor_area*100
            $bpd_data['natural_gas_eui'] = isset($ui_data['fuel_oil_eui']) ? ($ui_data['natural_gas_eui_units'] === "therms" ? $ui_data['natural_gas_eui'] / $bpd_data['floor_area'] * 100 : ($ui_data['natural_gas_eui_units'] === "ccf" ? ($ui_data['natural_gas_eui'] * 1.037) / $bpd_data['floor_area'] * 100 : $ui_data['natural_gas_eui'] / $bpd_data['floor_area'] * 1000)): null;

            // Gal or MMBtu
            $bpd_data['fuel_oil_eui'] = isset($ui_data['fuel_oil_eui']) ? ($ui_data['fuel_oil_eui_units'] === "gal" ? $ui_data['fuel_oil_eui'] / $bpd_data['floor_area'] * 138 : $ui_data['fuel_oil_eui'] / $bpd_data['floor_area'] * 1000): null;

            // MMBtu
            $bpd_data['other_fuel_eui'] = isset($ui_data['other_fuel_eui']) ? ($ui_data['other_fuel_eui'] / $bpd_data['floor_area'] * 1000) : null;

            // ton-hours or MMBTu
            $bpd_data['district_chilled_water_eui'] = isset($ui_data['district_chilled_water_eui']) ? ($ui_data['district_chilled_water_eui_units'] === "ton-hours" ? $ui_data['district_chilled_water_eui'] / $bpd_data['floor_area'] * 12 : $ui_data['district_chilled_water_eui'] / $bpd_data['floor_area'] * 1000) : null;

            // MMBTu
            $bpd_data['district_hot_water_eui'] = isset($ui_data['district_hot_water_eui']) ? ($ui_data['district_hot_water_eui'] / $bpd_data['floor_area'] * 1000) : null;

            // klbs or MMBtu
            $bpd_data['district_steam_eui'] = isset($ui_data['district_steam_eui']) ? ($ui_data['district_steam_eui_units'] === "klbs" ? $ui_data['district_steam_eui'] / $bpd_data['floor_area'] * 1000 : $ui_data['district_steam_eui'] / $bpd_data['floor_area'] * 1000):null;

            // gal or ccf
            $bpd_data['water_use_intensity'] = isset($ui_data['water_use_intensity']) ? ($ui_data['water_use_intensity_units'] === "gal" ? $ui_data['water_use_intensity'] / $bpd_data['floor_area'] * 1000 : $ui_data['water_use_intensity'] / $bpd_data['floor_area'] * 748) : null;

            $bpd_data['source_eui'] = (2.80 * 3.412 * $bpd_data['electric_eui']) + (1.05 * $bpd_data['natural_gas_eui']) + (1.01 * $bpd_data['fuel_oil_eui']) + (1.00 * $bpd_data['other_fuel_eui']) + (0.91 * $bpd_data['district_chilled_water_eui']) + (1.20 * $bpd_data['district_hot_water_eui']) + (1.20 * $bpd_data['district_steam_eui']);
            $bpd_data['site_eui'] = (3.412 * $bpd_data['electric_eui']) + $bpd_data['natural_gas_eui'] + $bpd_data['fuel_oil_eui'] + $bpd_data['other_fuel_eui'] + $bpd_data['district_chilled_water_eui'] + $bpd_data['district_hot_water_eui'] + $bpd_data['district_steam_eui'];
            $bpd_data['fuel_eui'] = $bpd_data['natural_gas_eui'] + $bpd_data['fuel_oil_eui'] + $bpd_data['other_fuel_eui'] + $bpd_data['district_chilled_water_eui'] + $bpd_data['district_hot_water_eui'] + $bpd_data['district_steam_eui'];
            $bpd_data['ghg_intensity'] = (3.412 * 0.294 * $bpd_data['electric_eui']) + (0.117 * $bpd_data['natural_gas_eui']) + (0.164 * $bpd_data['fuel_oil_eui']) + (0.164 * $bpd_data['other_fuel_eui']) + (0.116 * $bpd_data['district_chilled_water_eui']) + (0.146 * $bpd_data['district_hot_water_eui']) + (0.146 * $bpd_data['district_steam_eui']);

            $bpd_data['electric_eui_measured'] = $ui_data['annual_electric_use_measured'];

            // Lab use
            if ($bpd_data['chemical_lab_area'] > 0.7) {
                $bpd_data['lab_use'] = "Chemistry";
            } else if ($bpd_data['physical_lab_area'] > 0.7) {
                $bpd_data['lab_use'] = "Physics/Engineering";
            } else if ($bpd_data['dry_lab_area'] > 0.7) {
                $bpd_data['lab_use'] = "Maker/Workshop";
            } else if ($bpd_data['biological_lab_area'] > 0.7) {
                $bpd_data['lab_use'] = "Biology";
            } else if ($bpd_data['vivarium_area'] > 0.7) {
                $bpd_data['lab_use'] = "Vivarium";
            } else if ($bpd_data['other_lab_area'] > 0.7) {
                $bpd_data['lab_use'] = "Other";
            } else if (($bpd_data['biological_lab_area'] + $bpd_data['chemical_lab_area'] + $bpd_data['vivarium_area']) > 0.7) {
                $bpd_data['lab_use'] = "Combination (Bio/Chem)";
            } else if (((($bpd_data['biological_lab_area'] > 0.25) ? 1 : 0 )+
                    (($bpd_data['chemical_lab_area'] > 0.25) ? 1 : 0 )+
                    (($bpd_data['vivarium_area'] > 0.25) ? 1 : 0 )+
                    (($bpd_data['dry_lab_area'] > 0.25) ? 1 : 0 )+
                    (($bpd_data['physical_lab_area'] > 0.25) ? 1 : 0) +
                    (($bpd_data['other_lab_area'] > 0.25) ? 1 : 0)) === 3) {
                $bpd_data['lab_use'] = "Combination (3-way)";
            } else {
                $bpd_data['lab_use'] = "Combination (Other)";
            }


        } else {
            $bpd_data['electric_eui'] = 0;
        }

        // Unset Ids
        $unset_ids = ['_method', '_csrfToken', '_Token[fields]', '_Token[debug]', 'annual_electric_use', 'annual_electric_use_measured', 'annual_electric_use_units', 'natural_gas_eui_units', 'fuel_oil_eui_units', 'other_fuel_eui_units', 'district_chilled_water_eui_units', 'district_hot_water_eui_units', 'district_steam_eui_units', 'annual_energy_use_units', 'water_use_intensity_units', 'annual_energy_use_measured'];

        foreach ($unset_ids as $obj) {
            if (isset($ui_data[$obj])) {
                unset($ui_data[$obj]);
            }
        }

        $bpd_data['climate'] = isset($this->climate($ui_data['zip_code'])[0]) ? $this->climate($ui_data['zip_code'])[0]: "Unknown";

        // Replace UI Data with BPD data
        $ui_data['lab_use'] = '';
        foreach ($bpd_data as $k => $v) {
            $ui_data[$k] = $bpd_data[$k];
        }

        // Keep that 0 in front
        if(strlen($ui_data['zip_code']) == 4){
            $ui_data['zip_code'] = '0' . $ui_data['zip_code'];
        }

        return $ui_data;
    }


    public function reverseCalculations($bpd_data)
    {
        /** U * Floor Area */
        $ui_data = [];
        $ui_data['floor_area'] = $bpd_data['floor_area'];
        $ui_data['annual_electric_use'] = floor($bpd_data['electric_eui'] * $bpd_data['floor_area']);
        $ui_data['total_utility_cost_intensity'] = isset($bpd_data['total_utility_cost_intensity']) ? floor($bpd_data['total_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['water_use_intensity'] = isset($bpd_data['water_use_intensity']) ? floor($bpd_data['water_use_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['water_sewer_cost_intensity'] = isset($bpd_data['water_sewer_cost_intensity']) ? floor($bpd_data['water_sewer_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['ventilation_electric_eui'] = isset($bpd_data['ventilation_electric_eui']) ? floor($bpd_data['ventilation_electric_eui'] * $bpd_data['floor_area']) : null;
        $ui_data['ventilation_peak_airflow'] = isset($bpd_data['ventilation_peak_airflow']) ? floor($bpd_data['ventilation_peak_airflow'] * $bpd_data['floor_area']) : null;
        $ui_data['cooling_plant_electric_eui'] = isset($bpd_data['cooling_plant_electric_eui']) ? floor($bpd_data['cooling_plant_electric_eui'] * $bpd_data['floor_area']) : null;
        $ui_data['lighting_electric_eui'] = isset($bpd_data['lighting_electric_eui']) ? floor($bpd_data['lighting_electric_eui'] * $bpd_data['floor_area']) : null;
        $ui_data['process_plug_electric_eui'] = isset($bpd_data['process_plug_electric_eui']) ? floor($bpd_data['process_plug_electric_eui'] * $bpd_data['floor_area']) : null;
        $ui_data['number_of_people'] = isset($bpd_data['number_of_people']) ? floor($bpd_data['number_of_people'] * $bpd_data['floor_area']) : null;
        $ui_data['on_site_renewable_electric_eui'] = isset($bpd_data['on_site_renewable_electric_eui']) ? floor($bpd_data['on_site_renewable_electric_eui'] * $bpd_data['floor_area']) : null;
        $ui_data['electric_utility_cost_intensity'] = isset($bpd_data['electric_utility_cost_intensity']) ? floor($bpd_data['electric_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['natural_gas_utility_cost_intensity'] = isset($bpd_data['natural_gas_utility_cost_intensity']) ? floor($bpd_data['natural_gas_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['fuel_oil_utility_cost_intensity'] = isset($bpd_data['fuel_oil_utility_cost_intensity']) ? floor($bpd_data['fuel_oil_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['other_fuel_utility_cost_intensity'] = isset($bpd_data['other_fuel_utility_cost_intensity']) ? floor($bpd_data['other_fuel_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['district_chilled_water_utility_cost_intensity'] = isset($bpd_data['district_chilled_water_utility_cost_intensity']) ? floor($bpd_data['district_chilled_water_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['district_hot_water_utility_cost_intensity'] = isset($bpd_data['district_hot_water_utility_cost_intensity']) ? floor($bpd_data['district_hot_water_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['district_steam_utility_cost_intensity'] = isset($bpd_data['district_steam_utility_cost_intensity']) ? floor($bpd_data['district_steam_utility_cost_intensity'] * $bpd_data['floor_area']) : null;
        $ui_data['total_lab_area'] = isset($bpd_data['total_lab_area']) ? floor($bpd_data['total_lab_area'] * $bpd_data['floor_area']) : null;

        /** U * Total Lab Area **/
        $ui_data['dry_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['dry_lab_area'])) ? floor($bpd_data['dry_lab_area'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['biological_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['biological_lab_area'])) ? floor($bpd_data['biological_lab_area'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['chemical_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['chemical_lab_area'])) ? floor($bpd_data['chemical_lab_area'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['physical_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['physical_lab_area'])) ? floor($bpd_data['physical_lab_area'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['vivarium_area'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['vivarium_area'])) ? floor($bpd_data['vivarium_area'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['other_lab_area'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['other_lab_area'])) ? floor($bpd_data['other_lab_area'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['number_of_ducted_fume_hoods'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['number_of_ducted_fume_hoods'])) ? floor($bpd_data['number_of_ducted_fume_hoods'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['number_of_filtering_fume_hoods'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['number_of_filtering_fume_hoods'])) ? floor($bpd_data['number_of_filtering_fume_hoods'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;
        $ui_data['total_fume_hood_length'] = ($bpd_data['total_lab_area'] > 0 && isset($bpd_data['total_fume_hood_length'])) ? floor($bpd_data['total_fume_hood_length'] * ($bpd_data['total_lab_area'] * $bpd_data['floor_area'])) : null;

        /** U * Floor Area / 1000 **/
        $ui_data['peak_electric_demand_intensity'] = isset($bpd_data['peak_electric_demand_intensity']) ? floor($bpd_data['peak_electric_demand_intensity'] * $bpd_data['floor_area'] / 1000) : null;
        $ui_data['ventilation_peak_electric_demand_intensity'] = isset($bpd_data['ventilation_peak_electric_demand_intensity']) ? floor($bpd_data['ventilation_peak_electric_demand_intensity'] * $bpd_data['floor_area'] / 1000) : null;
        $ui_data['cooling_plant_peak_electric_demand_intensity'] = isset($bpd_data['cooling_plant_peak_electric_demand_intensity']) ? floor($bpd_data['cooling_plant_peak_electric_demand_intensity'] * $bpd_data['floor_area'] / 1000) : null;
        $ui_data['lighting_peak_electric_demand_intensity'] = isset($bpd_data['lighting_peak_electric_demand_intensity']) ? floor($bpd_data['lighting_peak_electric_demand_intensity'] * $bpd_data['floor_area'] / 1000) : null;
        $ui_data['process_plug_peak_electric_demand_intensity'] = isset($bpd_data['process_plug_peak_electric_demand_intensity']) ? floor($bpd_data['process_plug_peak_electric_demand_intensity'] * $bpd_data['floor_area'] / 1000) : null;

        /** Floor Area * U **/
        $ui_data['cooling_plant_capacity'] = (isset($bpd_data['cooling_plant_capacity'])) ? (($bpd_data['cooling_plant_capacity'] > 0) ? floor($bpd_data['floor_area'] / $bpd_data['cooling_plant_capacity']) : null) : null;
        $ui_data['cooling_plant_peak_load_intensity'] = (isset($bpd_data['cooling_plant_peak_load_intensity'])) ? (($bpd_data['cooling_plant_peak_load_intensity'] > 0) ? floor($bpd_data['floor_area'] / $bpd_data['cooling_plant_peak_load_intensity']) : null) : null;

        $ui_data['annual_electric_use_measured'] = $bpd_data['electric_eui_measured'];

        /** Unit Conversations **/
        // Therms
        $ui_data['natural_gas_eui'] = floor($bpd_data['natural_gas_eui'] * $bpd_data['floor_area'] / 100);

        // Gas
        $ui_data['fuel_oil_eui'] = floor($bpd_data['fuel_oil_eui'] * $bpd_data['floor_area'] / 138);

        // MMBtu
        $ui_data['other_fuel_eui'] = floor($bpd_data['other_fuel_eui'] * $bpd_data['floor_area'] / 1000);

        // ton-hours
        $ui_data['district_chilled_water_eui'] = floor($bpd_data['district_chilled_water_eui'] * $bpd_data['floor_area'] / 12);

        // MMBTu
        $ui_data['district_hot_water_eui'] = floor($bpd_data['district_hot_water_eui'] * $bpd_data['floor_area'] / 1000);

        // klbs
        $ui_data['district_steam_eui'] = floor($bpd_data['district_steam_eui'] * $bpd_data['floor_area'] / 1000);

        // gal
        $ui_data['water_use_intensity'] = floor($bpd_data['water_use_intensity'] * $bpd_data['floor_area'] / 1000);

        // Replace BPD data with UI Data
        foreach ($ui_data as $k => $v) {
            $bpd_data[$k] = $ui_data[$k];
        }

        return $bpd_data;
    }

    public function create($data, $facility_id = null)
    {
        $data = $this->dataCleanup($data);

        // Crate Facility ID
        $this->loadModel('Users');
        $email = $this->Auth->user('email');
        $api_key = $this->Auth->user('api_key');
        if ($facility_id) {
            $facility_response = [];
            $facility_response['id'] = $facility_id;
        } else {
            $facility_json = json_encode(['name' => $data['name']]);
            $facility_response = json_decode($this->BPD->createFacility($email, $api_key, $facility_json), true);
        }

        if (isset($facility_response['metadata']['error'])) {
            $response = ['error' => $facility_response['metadata']['error']];
        } else {
            $data['facility_id'] = $facility_response['id'];
            $data['facility_type'] = 'Laboratory';
            $data['building_class'] = 'Commercial';
            unset($data['name']);

            // Create Building
            $building_json = json_encode($data);
            $building_response = json_decode($this->BPD->createBuilding($email, $api_key, $building_json), true);
            if (isset($building_response['metadata']['error'])) {
                $response = ['error' => $building_response['metadata']['error']];
            } else {
                if($data['site_eui']<20 || $data['site_eui'] > 2000){
                    $this->Flash->error(__("Warning: calculated building Site EUI is {0} kBtu/sf/yr, which is outside of the normal range for lab buildings. Please recheck your data entries.",[round($data['site_eui'],0)]));
                }
                $response = ['success' => true];
            }
        }

        return $response;
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $myBuildingCount = $this->getBuildingCount($id);

        $this->loadModel('Users');
        $email = $this->Auth->user('email');
        $api_key = $this->Auth->user('api_key');
        if ($myBuildingCount === 1) {
            $response = json_decode($this->BPD->deleteBuilding($email, $api_key, $id), true);

            if (isset($response['error'])) {
                $this->Flash->error($response['error']);
            }

            $response = json_decode($this->BPD->deleteFacility($email, $api_key, $id), true);
        } else {
            $response = json_decode($this->BPD->deleteBuilding($email, $api_key, $id), true);
        }

        if (!isset($response['error'])) {
            $this->Flash->success(__('The building has been deleted.'));
        } else {
            $this->Flash->error($response['error']);
        }

        return $this->redirect(['action' => 'index']);
    }

    public function add($facility_id = null, $building_id = null)
    {

        if ($this->request->is(['post'])) {
            $myData = json_decode($this->request->getData('data'), true);
            $myName = isset($myData['name']) ? $myData['name'] : '';
            $myYear = isset($myData['year']) ? $myData['year'] : '';
            $response = $this->create($myData, $facility_id);

            if (isset($response['error'])) {
                $this->Flash->error($response['error']);
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->success(__("Added Building {0} {1}", [$myName, $myYear]));
                return $this->redirect(['action' => 'index']);
            }

        } else {

            if ($facility_id) {
                $myBuildingYears = $this->getBuildingYear($building_id);

                $this->loadModel('Users');
                $email = $this->Auth->user('email');
                $api_key = $this->Auth->user('api_key');
                $building = json_decode($this->BPD->getBuildingInfo($email, $api_key, $building_id), true);
                $building_tmp = $this->reverseCalculations($building);
                $building = $this->fixLabArea($building_tmp);

                $building['name'] = isset($building['facility_name']) ? $building['facility_name'] : "";

                // Remove fields in Utility Usage Tab
                $unset_ids = ['year', 'annual_electric_use', 'natural_gas_eui', 'fuel_oil_eui', 'other_fuel_eui', 'district_chilled_water_eui', 'district_hot_water_eui', 'district_steam_eui', 'water_use_intensity', 'water_sewer_cost_intensity', 'total_utility_cost_intensity',
                    'peak_electric_demand_intensity', 'ventilation_peak_airflow', 'on_site_renewable_electric_eui', 'process_plug_peak_electric_demand_intensity', 'ventilation_electric_eui', 'ventilation_peak_electric_demand_intensity', 'cooling_plant_electric_eui', 'cooling_plant_peak_electric_demand_intensity',
                    'cooling_plant_peak_load_intensity', 'cooling_plant_capacity', 'lighting_electric_eui', 'lighting_peak_electric_demand_intensity', 'installed_lighting_intensity', 'process_plug_electric_eui', 'cooling_plant_capacity'];
                foreach ($unset_ids as $obj) {
                    if (isset($building[$obj])) {
                        unset($building[$obj]);
                    }
                }

            } else {
                $building = null;
                $myBuildingYears = [];
            }

            $control = $this->generateControls($building);
            $this->set(compact('category', 'control', 'building', 'myBuildingYears'));
        }
    }

    public function generateControls($building)
    {
        $category = $this->getCategories(false, true);
        $label = $this->getLabels();
        $tooltip = $this->getTooltips();
        $requiredField = $this->getRequiredFields();
        $mm = $this->getMinMax();
        $options = [];
        foreach ($category as $k => $obj) {
            foreach ($obj as $kk => $obj2) {
                if (empty($obj2['id'])) {
                    if (is_array($obj2)) {
                        foreach ($obj2 as $xx => $vars) {
                            if (empty($vars['label'])) {
                                $options[$k][""] = "";
                            } else {
                                if ($vars['label'] !== 'All') {
                                    $id = empty($vars['alternative_label']) ? $vars['label'] : $vars['alternative_label'];
                                    $options[$k][$id] = !empty($vars['add_label']) ? $vars['add_label'] : $vars['label'];
                                }
                            }
                        }
                    }
                } else {
                    if ($obj2['label'] !== 'All') {
                        $id = empty($obj2['alternative_label']) ? $obj2['label'] : $obj2['alternative_label'];
                        $options[$k][$id] = !empty($obj2['add_label']) ? $obj2['add_label'] : $obj2['label'];
                    }
                }

            }

        }

        $options['state'] = $this->getStates(false);
        $input_fields = ['name', 'year_built', 'existing_building', 'location', 'state', 'zip_code', 'organization_type', 'lab_type', 'operating_hours', 'number_of_people', 'number_of_buildings', 'floor_area', 'net_floor_area', 'total_lab_area', 'biological_lab_area', 'chemical_lab_area', 'physical_lab_area', 'vivarium_area', 'dry_lab_area', 'other_lab_area', 'biosafety_lab_area', 'cleanroom_iso5_area', 'cleanroom_iso6_area', 'cleanroom_iso7_area', 'hvac_type', 'hvac_control', 'cooling', 'heating', 'exhaust_air_energy_recovery', 'building_level_combined_heat_power', 'on_site_renewable_energy_type', 'total_fume_hood_length', 'number_of_filtering_fume_hoods', 'number_of_ducted_fume_hoods', 'fume_hood_sash_height', 'fume_hood_face_velocity', 'fume_hood_control', 'occupied_required_air_change_rate', 'unoccupied_required_air_change_rate', 'year', 'water_sewer_cost_intensity', 'total_utility_cost_intensity', 'peak_electric_demand_intensity', 'on_site_renewable_electric_eui', 'process_plug_peak_electric_demand_intensity', 'ventilation_electric_eui', 'ventilation_peak_electric_demand_intensity', 'ventilation_peak_airflow', 'cooling_plant_electric_eui', 'cooling_plant_peak_electric_demand_intensity', 'cooling_plant_peak_load_intensity', 'cooling_plant_capacity', 'lighting_electric_eui', 'lighting_peak_electric_demand_intensity', 'installed_lighting_intensity', 'process_plug_electric_eui', 'ult_freezers', 'data_center_load'];
        $control = [];
        foreach ($input_fields as $field) {
            $myLabel = ['label' => ['text' => $label[$field], 'class' => 'col-md-4']];
            if (!empty($tooltip[$field])) {
                $myLabel['title'] = $tooltip[$field];
                $myLabel['icon'] = 'glyphicon-info-sign';
            }else{
                $myLabel['icon'] = 'glyphicon-none';
            }

            if (!empty($options[$field]) || $field === 'state') {
                $myLabel['options'] = $options[$field];
            }

            if ($requiredField[$field]) {
                $myLabel['required'] = $requiredField[$field];
            }

            if (!empty($mm[$field])) {
                $myLabel['type'] = 'number';
                $myLabel['min'] = $mm[$field][0];
                $myLabel['max'] = ($field === "year" || $field === "year_built") ? date('Y') : $mm[$field][1];
                $myLabel['step'] = $mm[$field][2];
            } else {
               if(in_array($field,['water_use_intensity','total_utility_cost_intensity','water_sewer_cost_intensity','peak_electric_demand_intensity','process_plug_electric_eui','ventilation_electric_eui','cooling_plant_electric_eui','lighting_electric_eui','on_site_renewable_electric_eui','process_plug_peak_electric_demand_intensity','ventilation_peak_electric_demand_intensity','cooling_plant_peak_electric_demand_intensity','lighting_peak_electric_demand_intensity','ventilation_peak_airflow','cooling_plant_peak_load_intensity'])){
                   $myLabel['type'] = 'number';
                   $myLabel['min'] = 0;
               }
            }

            if ($field === "zip_code") {
                $myLabel['pattern'] = '[0-9]{5}';
            }

            if ($field === "name") {
                if ($building) {
                    $myLabel['disabled'] = true;
                }
            }

            $control[$field] = $myLabel;
        }

        // Yes No Units
        $yes_input_fields = ['fume_hood_automatic_sash_closers', 'fume_hood_unoccupied_airflow_setback', 'supply_static_pressure_reset', 'supply_air_temperature_reset', 'exhaust_static_pressure_reset', 'lab_unoccupied_airflow_setback', 'lab_unoccupied_temperature_setback', 'pump_head_reset', 'exhaust_fan_wind_speed_response', 'lab_chemical_sensing_airflow_response', 'geothermal_heat_pump', 'heat_recovery_chiller', 'air_side_low_pressure_drop_design', 'water_side_low_pressure_drop_design', 'true_variable_air_volume_exhaust', 'high_efficiency_ultra_low_temperature_freezers', 'cascade_air_use', 'water_cooled_lab_equipment', 'hazard_assessment_minimum_air_change_rate', 'imag_equipment'];
        foreach ($yes_input_fields as $field) {
            $myLabel = ['templateVars' => ['id' => $field, 'label' => $label[$field], 'value' => 'true', 'checkbox_label' => 'Yes', 'tooltip' => $tooltip[$field]]];

            if ($requiredField[$field]) {
                $myLabel['required'] = $requiredField[$field];
            }
            $control[$field] = $myLabel;

        }

        // Units
        $units_kWh = '<option value="kWh">kWh</option>';
        $units_therms = '<option value="therms">therms</option>';
        $units_gal = '<option value="gal">gal</option>';
        $units_MMBtu = '<option value="MMBtu">MMBTU</option>';
        $units_ton_hours = '<option value="ton-hours">ton-hours</option>';
        $units_ccf = '<option value="ccf">ccf</option>';
        $units_klbs = '<option value="klbs">klbs</option>';

        // TODO: Add tool tips here?
        // Units Drop down
        $control['annual_electric_use'] = ['templateVars' => ['id' => 'annual_electric_use', 'label' => $label['annual_electric_use'], 'option1' => $units_kWh, 'icon' => 'glyphicon-none'], $requiredField['annual_electric_use']];
        $control['natural_gas_eui'] = ['templateVars' => ['id' => 'natural_gas_eui', 'label' => $label['natural_gas_eui'], 'option1' => $units_therms, 'option2' => $units_MMBtu, 'option3' => $units_ccf,'icon' => 'glyphicon-none'], $requiredField['natural_gas_eui']];
        $control['fuel_oil_eui'] = ['templateVars' => ['id' => 'fuel_oil_eui', 'label' => $label['fuel_oil_eui'], 'option1' => $units_gal, 'option2' => $units_MMBtu,'icon' => 'glyphicon-none'], $requiredField['fuel_oil_eui']];
        $control['other_fuel_eui'] = ['templateVars' => ['id' => 'other_fuel_eui', 'label' => $label['other_fuel_eui'], 'option1' => $units_MMBtu,'icon' => 'glyphicon-none'], $requiredField['other_fuel_eui']];
        $control['district_chilled_water_eui'] = ['templateVars' => ['id' => 'district_chilled_water_eui', 'label' => $label['district_chilled_water_eui'], 'option1' => $units_ton_hours, 'option2' => $units_MMBtu,'icon' => 'glyphicon-none'], $requiredField['district_chilled_water_eui']];
        $control['district_hot_water_eui'] = ['templateVars' => ['id' => 'district_hot_water_eui', 'label' => $label['district_hot_water_eui'], 'option1' => $units_MMBtu,'icon' => 'glyphicon-none'], $requiredField['district_hot_water_eui']];
        $control['district_steam_eui'] = ['templateVars' => ['id' => 'district_steam_eui', 'label' => $label['district_steam_eui'], 'option1' => $units_klbs, 'option2' => $units_MMBtu,'icon' => 'glyphicon-none'], $requiredField['district_steam_eui']];
        $control['water_use_intensity'] = ['templateVars' => ['id' => 'water_use_intensity', 'label' => $label['water_use_intensity'], 'option1' => $units_gal, 'option2' => $units_ccf,'icon' => 'glyphicon-info-sign', 'title' => $tooltip['water_use_intensity']], $requiredField['water_use_intensity']];

        return $control;
    }

    public function getBuildingCount($id)
    {

        $buildingLists = $this->getBuildings();
        $myBuildings = $buildingLists[0];
        $found_facility_id = 0;
        foreach ($myBuildings as $facility_id => $myBuilding) {
            foreach ($myBuilding as $obj) {
                if ((int)$obj['id'] === (int)$id) {
                    $found_facility_id = $facility_id;
                    break;
                }
            }
        }

        $myBuildingCount = isset($myBuildings[$found_facility_id]) ? count($myBuildings[$found_facility_id]) : 0;

        return $myBuildingCount;
    }

    public function getBuildingYear($id)
    {
        $buildingLists = $this->getBuildings();
        $myBuildings = $buildingLists[0];
        $found_facility_id = 0;
        foreach ($myBuildings as $facility_id => $myBuilding) {
            foreach ($myBuilding as $obj) {
                if ((int)$obj['id'] === (int)$id) {
                    $found_facility_id = $facility_id;
                    break;
                }
            }
        }

        $myBuildingYears = isset($buildingLists[2][$found_facility_id]) ? $buildingLists[2][$found_facility_id] : [];
        return $myBuildingYears;
    }

    public function fixLabArea($building)
    {
        // True up the lab rounding issue to match the lab area (Assumes user inputted dated correctly)
        $total = ((int)$building['biological_lab_area'] + (int)$building['chemical_lab_area'] + (int)$building['physical_lab_area'] + (int)$building['vivarium_area'] + (int)$building['dry_lab_area'] + (int)$building['other_lab_area']);
        if ((int)$building['total_lab_area'] !== $total) {
            // TODO: check which one has 99 and add 1 if not then add one to like so
            if ($building['biological_lab_area'] > 0) {
                $building['biological_lab_area'] = (int)$building['biological_lab_area'] + ((int)$building['total_lab_area'] - $total);
            } else if ($building['chemical_lab_area'] > 0) {
                $building['chemical_lab_area'] = (int)$building['chemical_lab_area'] + ((int)$building['total_lab_area'] - $total);
            } else if ($building['physical_lab_area'] > 0) {
                $building['physical_lab_area'] = (int)$building['physical_lab_area'] + ((int)$building['total_lab_area'] - $total);
            } else if ($building['vivarium_area'] > 0) {
                $building['vivarium_area'] = (int)$building['vivarium_area'] + ((int)$building['total_lab_area'] - $total);
            } else if ($building['dry_lab_area'] > 0) {
                $building['dry_lab_area'] = (int)$building['dry_lab_area'] + ((int)$building['total_lab_area'] - $total);
            } else if ($building['other_lab_area'] > 0) {
                $building['other_lab_area'] = (int)$building['other_lab_area'] + ((int)$building['total_lab_area'] - $total);
            }
        }
        return $building;
    }

    public function edit($id = null)
    {
        if ($this->request->is(['post'])) {
            //  Clean Data
            $myData = json_decode($this->request->getData('data'), true);
            $myName = isset($myData['name']) ? $myData['name'] : '';
            unset($myData['name']);
            $data = $this->dataCleanup($myData);

            // Set to blank if null
            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');
            $building = json_decode($this->BPD->getBuildingInfo($email, $api_key, $id), true);
            foreach($building as $k => $v){
                if(is_numeric($v)) {
                    if(!in_array($k, ['facility_user_id','id_orig','original_building_year_id','original_facility_id','user_id','facility_id','id','facility_original_facility_id','random_id'])) {
                        if (strpos($k, 'original_') === false) {
                            if (!isset($data[$k])) {
                                $data[$k] = null;
                            }
                        }
                    }
                }
            }

            // Clear nulls
            $yes_input_fields = ['fume_hood_automatic_sash_closers', 'fume_hood_unoccupied_airflow_setback', 'supply_static_pressure_reset', 'supply_air_temperature_reset', 'exhaust_static_pressure_reset', 'lab_unoccupied_airflow_setback', 'lab_unoccupied_temperature_setback', 'pump_head_reset', 'exhaust_fan_wind_speed_response', 'lab_chemical_sensing_airflow_response', 'geothermal_heat_pump', 'heat_recovery_chiller', 'air_side_low_pressure_drop_design', 'water_side_low_pressure_drop_design', 'true_variable_air_volume_exhaust', 'high_efficiency_ultra_low_temperature_freezers', 'cascade_air_use', 'water_cooled_lab_equipment', 'hazard_assessment_minimum_air_change_rate'];
            foreach ($yes_input_fields as $field) {
                if (!isset($data[$field])) {
                    $data[$field] = 'Unknown';
                }
            }

            // Update Building
            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');
            $building_json = json_encode($data);
            $building_response = json_decode($this->BPD->updateBuilding($email, $api_key, $id, $building_json), true);

            if (isset($building_response['metadata']['error'])) {
                $this->Flash->error($building_response['metadata']['error']);
            } else {
                if($data['site_eui']<20 || $data['site_eui'] > 2000){
                    $this->Flash->error(__("Warning: calculated building Site EUI is {0} kBtu/sf/yr, which is outside of the normal range for lab buildings. Please recheck your data entries.",[round($data['site_eui'],0)]));
                }else{
                    $this->Flash->success(__("Updated Building {0}", [$myName]));
                }
                return $this->redirect(['action' => 'index']);
            }

        } else {

            $myBuildingCount = $this->getBuildingCount($id);
            $myBuildingYears = $this->getBuildingYear($id);

            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');
            $building_response = json_decode($this->BPD->getBuildingInfo($email, $api_key, $id), true);

            if (isset($building_response['metadata']['error'])) {
                $this->Flash->error(__("The Building does NOT exist"));
                return $this->redirect(['action' => 'index']);
            } else {
                $building_tmp = $this->reverseCalculations($building_response);
                $building = $this->fixLabArea($building_tmp);
            }

            $building['name'] = isset($building['facility_name']) ? $building['facility_name'] : "";
            $control = $this->generateControls($building);

            // Remove Current year
            $found_key = array_search($building['year'], $myBuildingYears);
            unset($myBuildingYears[$found_key]);
            $myBuildingYears = array_values($myBuildingYears);

            $states = $this->getStates(false);
            $options['state'] = $states;
            $this->set(compact('category', 'control', 'building', 'myBuildingCount', 'myBuildingYears'));
        }
    }

    public function editName($id = null)
    {
        if ($this->request->is(['post'])) {
            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');

            // Update Facility
            $myData = $this->request->getData();
            $facility_json = json_encode($myData);
            $response = json_decode($this->BPD->updateFacility($email, $api_key, $id, $facility_json), true);

            if (!isset($response['error'])) {
                $this->Flash->success(__('The building name changed.'));
            } else {
                $this->Flash->error($response['error']);
            }

            return $this->redirect(['action' => 'index']);
        } else {

            $this->loadModel('Users');
            $email = $this->Auth->user('email');
            $api_key = $this->Auth->user('api_key');
            $building = json_decode($this->BPD->getFacilityInfo($email, $api_key, $id), true);

            $name = isset($building['name']) ? $building['name'] : "";
            $this->set(compact('name'));
        }
    }

    public function climate($zip = null)
    {

        if ($zip) {

            // Read County & Zip
            $zip_county = array_map('str_getcsv', file(WWW_ROOT . '/csv/zip_county.csv'));
            array_walk($zip_county, function (&$a) use ($zip_county) {
                // remove &#65279 char
                $a = array_combine(preg_replace("/\xEF\xBB\xBF/", "", $zip_county[0]), $a);
            });
            array_shift($zip_county);


            // Match Zip
            $state_fip = '';
            $county_fip = '';
            foreach ($zip_county as $record) {
                if (isset($record['zip'])) {
                    if ((int)$record['zip'] === (int)$zip) {
                        $temp = str_split($record['county'], 2);
                        $state_fip = isset($temp[0]) ? $temp[0] : '';
                        $temp2 = isset($temp[0]) ? explode($temp[0], $record['county']) : '';
                        $county_fip = isset($temp2[1]) ? $temp2[1] : '';
                        break;
                    }
                }
            }


            // Read Climate Zones Lookup
            $climate_zones = array_map('str_getcsv', file(WWW_ROOT . '/csv/climate_zones.csv'));
            array_walk($climate_zones, function (&$a) use ($climate_zones) {
                $a = array_combine($climate_zones[0], $a);
            });
            array_shift($climate_zones);

            // Match State and Zip to get Climate Zone
            $climate_zone = '';
            if ($state_fip && $climate_zones) {

                foreach ($climate_zones as $record) {
                    if (isset($record['State FIPS'])) {
                        $my_state_fip = strlen($record['State FIPS']) === 1 ? "0" . $record['State FIPS'] : $record['State FIPS'];
                        $my_county_fip = strlen($record['County FIPS']) === 1 ? "00" . $record['County FIPS'] : (strlen($record['County FIPS']) === 2 ? "0" . $record['County FIPS'] : $record['County FIPS']);

                        if (($my_state_fip === $state_fip) && ($my_county_fip === $county_fip)) {
                            $climate_zone = $record['IECC Climate Zone'] . $record['IECC Moisture Regime'];
                            break;
                        }
                    }
                }
            }

            $state = ["AK" => "02", "AL" => "01", "AR" => "05", "AS" => "60", "AZ" => "04", "CA" => "06", "CO" => "08", "CT" => "09", "DC" => "11", "DE" => "10", "FL" => "12", "GA" => "13", "GU" => "66", "HI" => "15", "IA" => "19", "ID" => "16", "IL" => "17", "IN" => "18", "KS" => "20", "KY" => "21", "LA" => "22", "MA" => "25", "MD" => "24", "ME" => "23", "MI" => "26", "MN" => "27", "MO" => "29", "MS" => "28", "MT" => "30", "NC" => "37", "ND" => "38", "NE" => "31", "NH" => "33", "NJ" => "34", "NM" => "35", "NV" => "32", "NY" => "36", "OH" => "39", "OK" => "40", "OR" => "41", "PA" => "42", "PR" => "72", "RI" => "44", "SC" => "45", "SD" => "46", "TN" => "47", "TX" => "48", "UT" => "49", "VA" => "51", "VI" => "78", "VT" => "50", "WA" => "53", "WI" => "55", "WV" => "54", "WY" => "56"];
            $state = array_flip($state);

            $climate = ["1A" => "1A Very Hot - Humid (Miami-FL)",
                "2A" => "2A Hot - Humid (Houston-TX)",
                "2B" => "2B Hot - Dry (Phoenix-AZ)",
                "3A" => "3A Warm - Humid (Memphis-TN)",
                "3B" => "3B Warm - Dry (El Paso-TX)",
                "3C" => "3C Warm - Marine (San Francisco-CA)",
                "4A" => "4A Mixed - Humid (Baltimore-MD)",
                "4B" => "4B Mixed - Dry (Albuquerque-NM)",
                "4C" => "4C Mixed - Marine (Salem-OR)",
                "5A" => "5A Cool - Humid (Chicago-IL)",
                "5B" => "5B Cool - Dry (Boise-ID)",
                "6A" => "6A Cold - Humid (Burlington-VT)",
                "6B" => "6B Cold - Dry (Helena-MT)",
                "7" => "7 Very Cold (Duluth-MN)",
                "8" => "8 Subarctic (Fairbanks-AK)"];

            $my_climate = array_key_exists($climate_zone, $climate) ? $climate[$climate_zone] : 'Unknown';
            return [$my_climate, isset($state[$state_fip]) ? $state[$state_fip] : "Unknown"];
        }

        return [];
    }
}
