<?php

namespace Darksky;

class Darksky
{
    const API_BASE_URL = 'https://api.darksky.net/forecast';
    const VALID_UNITS = [
        'auto',
        'ca',
        'uk2',
        'us',
        'si',
    ];

    const VALID_EXCLUDE = [
        'currently',
        'minutely',
        'hourly',
        'daily',
        'alerts',
        'flags',
    ];

    private $key;
    private $latitude;
    private $longitude;
    private $language;
    private $units;

    public function __construct($key, $latitude, $longitude, $lang = 'en', $units = 'auto')
    {
        $this->setKey($key);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setLanguage($lang);
        $this->setUnits($units);
    }

    public function forecast(array $exclude = [], $extend = false)
    {
        try {
            return file_get_contents($this->generateRequestUrl($exclude, $extend));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    private function generateRequestUrl(array $exclude = [], $extend = false)
    {
        return self::API_BASE_URL.'/'.$this->getKey().'/'.$this->getLatitude().','.$this->getLongitude()
            .'?'.$this->generateUrlQueryString($exclude, $extend);
    }

    private function generateUrlQueryString(array $exclude = [], $extend = false)
    {
        $queryString = ['lang'  => $this->getLanguage(), 'units' => $this->getUnits()];

        // validate $exclude
        if ($this->validateExcludes($exclude) !== true) {
            $validExcludes = implode(',', self::VALID_EXCLUDE);
            throw new \Exception("Invalid excludes. Provide valid excludes: {$validExcludes}'");
        }

        if (!empty($exclude)) {
            $queryString['exclude'] = implode(',', $exclude);
        }

        if ($extend === true) {
            $queryString['extend'] = 'hourly';
        }

        return http_build_query($queryString);
    }

    private function validateExcludes($exclude)
    {
        if (empty($exclude)) {
            return true;
        }

        foreach ($exclude as $anExclude) {
            if (!in_array($anExclude, self::VALID_EXCLUDE)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param $units
     *
     * @throws \Exception
     */
    public function setUnits($units)
    {
        if (!in_array($units, self::VALID_UNITS)) {
            $validUnits = implode(',', self::VALID_UNITS);
            throw new \Exception("'{$units}' is not a valid unit. Valid units: {$validUnits}");
        }

        $this->units = $units;
    }
}
