<?php

namespace Geocoder;

class Geocoder
{
    const API_URL = 'http://maps.google.com/maps/api/geocode';

    public function geocode($address, $outputFormat = 'json')
    {
        $address = urlencode($address);
        $result = file_get_contents(self::API_URL."/{$outputFormat}?address={$address}&sensor=false");

        return $result;
    }

    public function asArray()
    {
        if ($this->getDefaultOutputFormat() !== 'json') {
            throw new \Exception('This function is only compatible with json as default output format');
        }

        return json_decode($this->rawResponse, true);
    }

    public function asObject()
    {
        if ($this->getDefaultOutputFormat() !== 'json') {
            throw new \Exception('This function is only compatible with json as default output format');
        }

        return json_decode($this->rawResponse);
    }
}
