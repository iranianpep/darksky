<?php

namespace Geocoder;

class Geocoder
{
    const API_URL = 'http://maps.google.com/maps/api/geocode';

    public function geocode($address)
    {
        $address = urlencode($address);
        $json = file_get_contents(self::API_URL."/json?address={$address}&sensor=false");

        return json_decode($json, true);
    }
}
