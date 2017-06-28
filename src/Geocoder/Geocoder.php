<?php

namespace Geocoder;

class Geocoder
{
    const API_URL = 'http://maps.google.com/maps/api/geocode';

    /**
     * @param        $address
     * @param string $outputFormat
     *
     * @return string
     */
    public function geocode($address, $outputFormat = 'json')
    {
        return file_get_contents(
            self::API_URL.'/'.$outputFormat.'?address='.urlencode($address).'&sensor=false'
        );
    }

    /**
     * @param $address
     *
     * @return array
     */
    public function getLatLng($address)
    {
        $result = $this->geocode($address);
        $result = json_decode($result);

        return [
            'lat' => $result->results[0]->geometry->location->lat,
            'lng' => $result->results[0]->geometry->location->lng,
        ];
    }
}
