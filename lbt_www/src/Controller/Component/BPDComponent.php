<?php

namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

/**
 * @property \Cake\ORM\Table Logs
 * @property \Cake\ORM\Table TableObj
 * @property \Cake\ORM\Table Users
 */
class BPDComponent extends Component
{

    public $api_url;
    public $api_user;
    public $api_key;

    public function __construct()
    {
        $this->api_url = env('API_URL');
        $this->api_user = env('API_USER');
        $this->api_key = env('API_KEY');
    }

    public function createFacility($api_user, $api_key, $post){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->requestData('/facility/', $post);
    }
    
    public function  createBuilding($api_user, $api_key, $post){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->requestData('/building/', $post);
    }

    public function  updateBuilding($api_user, $api_key, $id, $post){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->requestData('/building/' . $id, $post);
    }

    public function  updateFacility($api_user, $api_key, $id, $post){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->requestData('/facility/' . $id, $post);
    }

    public function  getApiKeyFromBpd($user){
        return $this->requestData('/user/' . $user,null);
    }

    public function requestData($path, $post){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = [];
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: ApiKey " . $this->api_user . ":" . $this->api_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo json_encode(['Error' => curl_error($ch)]);
        }

        curl_close ($ch);
        return $result;
    }

    public function getData($path){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = [];
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: ApiKey " . $this->api_user . ":" . $this->api_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo json_encode(['Error' => curl_error($ch)]);
        }

        curl_close ($ch);
        return $result;
    }

    public function deleteData($path){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->api_url . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $headers = [];
        $headers[] = "Content-Type: application/json";
        $headers[] = "Authorization: ApiKey " . $this->api_user . ":" . $this->api_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $this->log($this->api_url . $path);
        $this->log($ch);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo json_encode(['Error' => curl_error($ch)]);
        }

        curl_close ($ch);
        return $result;
    }

    public function getScatterPlot($post){
        return $this->requestData('/analyze/scatterplot', $post);
    }

    public function getUserScatterPlot($api_user, $api_key, $post){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->requestData('/analyze/building_table', $post);
    }

    public function getHistogramPlot($post){
        return $this->requestData('/analyze/histogram', $post);
    }

    public function getFields(){
        return $this->getData('/introspection/fields');
    }

    public function getFieldInfo($info){
        return $this->getData('/introspection/fields/' . $info);
    }

    public function getBuildingListInfo($api_user, $api_key,$post){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->requestData('/analyze/building_table', $post);
    }

    public function getBuildingInfo($api_user, $api_key,$id){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->getData('/building/' . $id);
    }

    public function getFacilityInfo($api_user, $api_key,$id){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->getData('/facility/' . $id);
    }

    public function deleteBuilding($api_user, $api_key,$id){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->deleteData('/building/' . $id);
    }

    public function deleteFacility($api_user, $api_key,$id){
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        return $this->deleteData('/facility/' . $id);
    }

}