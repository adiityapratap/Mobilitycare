<?php
class ControllerDealerGoogleproxy extends Controller {
    
    private $apiKey = 'AIzaSyCdIW7xk3UQDxMkhznl2gEyabtnGXHN2ww'; 

    public function geocode() {
        $this->response->addHeader('Content-Type: application/json');
        if (!isset($this->request->post['address'])) {
            $this->response->setOutput(json_encode(['status' => 'ERROR', 'message' => 'Address not provided']));
            return;
        }
        
        $address = urlencode($this->request->post['address']);
        $country = 'AU';
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&components=country:{$country}&key={$this->apiKey}";

        // $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$this->apiKey}";
        $result = $this->curlRequest($url);
        $this->response->setOutput($result);
    }

    public function geocodeReverse() {
        $this->response->addHeader('Content-Type: application/json');
        if (!isset($this->request->post['lat']) || !isset($this->request->post['lng'])) {
            $this->response->setOutput(json_encode(['status' => 'ERROR', 'message' => 'Lat/Lng not provided']));
            return;
        }
        $lat = $this->request->post['lat'];
        $lng = $this->request->post['lng'];
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$this->apiKey}";
        $result = $this->curlRequest($url);
        $this->response->setOutput($result);
    }

    public function nearby() {
        $this->response->addHeader('Content-Type: application/json');
        if (!isset($this->request->post['lat']) || !isset($this->request->post['lng']) || !isset($this->request->post['radius'])) {
            $this->response->setOutput(json_encode(['status' => 'ERROR', 'message' => 'Lat/Lng/Radius not provided']));
            return;
        }
        $lat = $this->request->post['lat'];
        $lng = $this->request->post['lng'];
        // $radius = $this->request->post['radius'];
        $radius = 200000;
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location={$lat},{$lng}&radius={$radius}&key={$this->apiKey}";
     
        $result = $this->curlRequest($url);
        $this->response->setOutput($result);
    }

    public function distance() {
        $this->response->addHeader('Content-Type: application/json');
        if (!isset($this->request->post['origins']) || !isset($this->request->post['destinations'])) {
            $this->response->setOutput(json_encode(['status' => 'ERROR', 'message' => 'Origins/Destinations not provided']));
            return;
        }
        $origins = $this->request->post['origins'];
        $destinations = $this->request->post['destinations'];
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$origins}&destinations={$destinations}&key={$this->apiKey}";
        $result = $this->curlRequest($url);
        $this->response->setOutput($result);
    }

    private function curlRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}