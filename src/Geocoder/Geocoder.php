<?php

namespace Geocoder;

class Geocoder
{
    const API_URL = 'http://maps.google.com/maps/api/geocode';
    const VALID_OUTPUT_FORMAT = ['json', 'xml'];

    /**
     * @param        $address
     * @param string $region
     * @param string $outputFormat
     *
     * @throws \Exception
     *
     * @return string
     */
    public function geocode($address, $region = '', $outputFormat = 'json')
    {
        if ($this->validateOutputFormat($outputFormat) !== true) {
            throw new \Exception("'{$outputFormat}' is not a valid format");
        }

        return file_get_contents($this->generateRequestUrl($address, $region, $outputFormat));
    }

    /**
     * @param $address
     *
     * @return array
     */
    public function getLatLng($address)
    {
        $result = $this->geocode($address);
        $result = json_decode($result, true);

        if ($result['status'] !== 'OK') {
            return;
        }

        $latLng = [];
        foreach ($result['results'] as $result) {
            $latLng[] = [
                'lat' => $result['geometry']['location']['lat'],
                'lng' => $result['geometry']['location']['lng'],
            ];
        }

        return $latLng;
    }

    /**
     * @param $format
     *
     * @return bool
     */
    private function validateOutputFormat($format)
    {
        if (in_array($format, self::VALID_OUTPUT_FORMAT)) {
            return true;
        }

        return false;
    }

    /**
     * @param        $address
     * @param string $region
     * @param string $outputFormat
     * @param bool   $sensor
     *
     * @return string
     */
    private function generateRequestUrl($address, $region = '', $outputFormat = 'json', $sensor = false)
    {
        $baseUrl = self::API_URL.'/'.$outputFormat.'?address='.urlencode($address).'&sensor='.$sensor;

        if (!empty($region)) {
            $baseUrl .= "&region={$region}";
        }

        return $baseUrl;
    }
}
