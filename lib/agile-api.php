<?php 
defined( 'ABSPATH' ) or die( 'No direct file access allowed!' );

class AgileAPI {
    private $serviceId;
    private $accessCode;
    private $token_url;
    private $k12_url;
    private $ecc_url;

    // constructor
    public function __construct() {
        $this->serviceId = $_SERVER['serviceId'];
        $this->accessCode = $_SERVER['accessCode'];
        $this->token_url = 'https://Lookupapi.agile-ed.com/LookupService/authenticate';
        $this->k12_url = 'https://Lookupapi.agile-ed.com/LookupService/Building/K12/';
        $this->ecc_url = 'https://Lookupapi.agile-ed.com/LookupService/Building/ECC/';
    }


    public function authenticate() {
        $url = $this->token_url.'?'.http_build_query(array("serviceId" => $this->serviceId, "accessCode" => $this->accessCode));
        
        $ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization ));
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function get_buildings($accessToken, $zip) {
        $buildings = array();

        // curl GET request
        $ch = curl_init($this->k12_url.'?'.http_build_query(array("zip" => $zip, "buildingType" => 'BLDG')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$accessToken));
        $k12_response = curl_exec($ch);
        curl_close($ch);

        $buildings = array_merge($buildings, json_decode($k12_response));
        

        $ch = curl_init($this->ecc_url.'?'.http_build_query(array("zip" => $zip, "buildingType" => 'BLDG')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '.$accessToken));
        $ecc_response = curl_exec($ch);
        curl_close($ch);

        $buildings = array_merge($buildings, json_decode($ecc_response));

        // remove duplicates from araay of objects
        $buildings = array_map("unserialize", array_unique(array_map("serialize", $buildings)));

        // sort buildings by name
        usort($buildings, function($a, $b) {
            return $a->institutionNameProper <=> $b->institutionNameProper;
        });

        return $buildings;
    }
}
