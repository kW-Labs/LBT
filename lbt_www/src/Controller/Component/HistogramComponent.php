<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

class HistogramComponent extends Component
{

    public $_bins = [];
    public $_bin_range = [];
    public $_number_of_bins;
    public $_rangeLow;
    public $_rangeHigh;
    public $_data = null;

    function getHistogramInfo() {
        if (!empty($this->_number_of_bins)) {
            $info = [
                "data" => $this->_histogramData(),
                "bins" => $this->_bins,
                "number_of_bins" => $this->_number_of_bins,
                "range" => [
                    "low" => $this->_rangeLow,
                    "high" => $this->_rangeHigh
                ]
            ];
            return $info;
        } else {
            return false;
        }
    }


    function _clear() {
        $this->_number_of_bins = null;
        $this->_bins = [];
        $this->_rangeLow = null;
        $this->_rangeHigh = null;
        $this->_data = null;
    }

    function _histogramData() {
        return $this->_data;
    }


    function floor ($n, $order=0)
    {
        $oom = 10 ** $order;
        return floor($n * $oom) / $oom;
    }

    function ceil($n, $order=0)
    {
        $oom = 10 ** $order;
        return ceil($n * $oom) / $oom;
    }

    function flt($mant, $exp){
        return $mant * 10 ** $exp;
    }

    function convert_scientific($num){
        if($num > 0) {
            $exponent = floor(log($num, 10));
            $mantissa = $num / 10 ** $exponent;
            return [$mantissa, $exponent];
        }else{
            return [0,0];
        }
    }

    function calculate_bins($min,$max,$n, $data){

        $distance = $max - $min;
        $minval = $this->convert_scientific($min);
        $maxval = $this->convert_scientific($max);
        $distance = $this->convert_scientific($distance);

        # Calculate the starting bin width
        $approx_bin_width = $this->convert_scientific($this->flt($distance[0],$distance[1] )/$n);

        # This will always be *more* bins than we need.
        $starting_exp = $approx_bin_width[1];
        $starting_distance = $this->flt(1.0, $starting_exp);

        # Round min/max on the scale of starting_distance
        $multipliers = [10,5,2.5,2,1];

        # For 1 round to nearest 1
        # For 2 round to nearest 1
        # For 4 round to nearest 10
        # For 5 round to nearest 10
        # For 10 round to nearest 100
        $round_map = ["1" =>0, "2"=>0, "2.5" =>-1, "5" => -1, "10" =>-2];

        $step = 0;
        $start = 0;
        $end = 0;
        foreach ($multipliers as $i => $m){
            if ($this->flt($distance[0], $distance[1]) / ($starting_distance * $m) < $n) {
                continue;
            }
            $step = ($starting_distance * $m);
            $start = $this->floor($this->flt($minval[0],$minval[1]), -$starting_exp + $round_map["$m"]);
            $end = $this->ceil($this->flt($maxval[0],$maxval[1]), -$starting_exp + $round_map["$m"]);
            break;
        }

        // Generate Bins
        $widths = range($start, $end, $step);
        foreach($widths as $key => $val)
        {
            if (!isset($widths[$key + 1])) break;
            $this->_bin_range[] = [
                'min' => $val,
                'max' => $widths[$key + 1]
            ];
        }

        // Add values to Matching Bin
        $histogram_bins = [];
        foreach($data as $i => $row){
            $key = $this->check_bin($row);
            $range = $this->_bin_range[$key];

            $key = $range['min'] . '-' . $range['max'];
            if (!isset($histogram_bins[$key])) $histogram_bins[$key] = [];
            $histogram_bins[$key][] = $row;
        }

        return $histogram_bins;
    }


    function check_bin ($val){
        foreach($this->_bin_range as $index => $bin){
            if($this->in_range($val,$bin['min'], $bin['max'])) {
                return $index;
            }
        }

        return false;
    }

    function in_range($number, $min, $max)
    {
        if ($number >= $min && $number < $max)
        {
            return true;
        }

        return false;
    }

    function setData($data, $bin_width) {
        $this->_clear();

        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $item) {
            if (!is_numeric($item)) {
                return false;
            }
        }

        // Set and sort data
        $this->_data = $data;
        $this->_rangeLow = min($this->_data);
        $this->_rangeHigh = max($this->_data);
        sort($this->_data);
        $this->_bins = $this->calculate_bins($this->_rangeLow, $this->_rangeHigh, $bin_width, $data);
        $this->_number_of_bins = count($this->_bins);

        return true;
    }
}
