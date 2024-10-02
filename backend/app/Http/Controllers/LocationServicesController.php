<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationServicesController extends Controller
{
    public function getCoordinatesFromAddress($address)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $address = urlencode($address);

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$apiKey}";
        
        $response = file_get_contents($url);
        $json = json_decode($response, true);

        if ($json['status'] == 'OK') {
            $latitude = $json['results'][0]['geometry']['location']['lat'];
            $longitude = $json['results'][0]['geometry']['location']['lng'];

            return ['lat' => $latitude, 'lng' => $longitude];
        } else {
            throw new Exception('Failed to get coordinates for address.');
        }
    }

public function calculateDistanceUsingGoogleAPI($serviceLat, $serviceLng, $customerLat, $customerLng)
{
    $apiKey = env('GOOGLE_MAPS_API_KEY');
    
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$serviceLat},{$serviceLng}&destinations={$customerLat},{$customerLng}&key={$apiKey}";

    $response = file_get_contents($url);
    $json = json_decode($response, true);

    if ($json['status'] == 'OK') {
        $distance = $json['rows'][0]['elements'][0]['distance']['text'];
        $duration = $json['rows'][0]['elements'][0]['duration']['text'];

        return ['distance' => $distance, 'duration' => $duration];
    } else {
        throw new Exception('Failed to calculate distance.');
    }
}

}
