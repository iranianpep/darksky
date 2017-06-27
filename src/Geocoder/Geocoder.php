<?php

namespace Geocoder;

class Geocoder
{
    const API_URL = 'http://maps.google.com/maps/api/geocode';

    private $rawResponse;
    private $defaultOutputFormat = 'json';

    /**
     * @return string
     */
    public function getDefaultOutputFormat(): string
    {
        return $this->defaultOutputFormat;
    }

    /**
     * @param string $defaultOutputFormat
     */
    public function setDefaultOutputFormat(string $defaultOutputFormat)
    {
        $this->defaultOutputFormat = $defaultOutputFormat;
    }

    public function geocode($address)
    {
        $outputFormat = $this->getDefaultOutputFormat();
        $address = urlencode($address);
        $json = file_get_contents(self::API_URL."/{$outputFormat}?address={$address}&sensor=false");

        $this->rawResponse = $json;

        return $this;
    }

    public function asRaw()
    {
        return $this->rawResponse;
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
