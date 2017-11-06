<?php

namespace Darksky;

/**
 * Class Darksky.
 */
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

    /**
     * Darksky constructor.
     *
     * @param        $key
     * @param        $latitude
     * @param        $longitude
     * @param string $lang
     * @param string $units
     */
    public function __construct(
        string $key,
        string $latitude,
        string $longitude,
        string $lang = 'en',
        string $units = 'auto'
    ) {
        $this->setKey($key);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setLanguage($lang);
        $this->setUnits($units);
    }

    /**
     * @param array $exclude
     * @param bool  $extend
     *
     * @throws \Exception
     *
     * @return string
     */
    public function forecast(array $exclude = [], $extend = false)
    {
        try {
            return file_get_contents($this->generateRequestUrl($exclude, $extend));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $time
     * @param array $exclude
     *
     * @throws \Exception
     *
     * @return bool|string
     */
    public function timeMachine(string $time, array $exclude = [])
    {
        try {
            return file_get_contents($this->generateRequestUrl($exclude, false, $time));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude(string $latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude(string $longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @param array  $exclude
     * @param bool   $extend
     * @param string $time
     *
     * @return string
     */
    private function generateRequestUrl(array $exclude = [], $extend = false, string $time = ''): string
    {
        if (!empty($time)) {
            $time = ",{$time}";
        }

        return self::API_BASE_URL.'/'.$this->getKey().'/'.$this->getLatitude().','.$this->getLongitude().$time
            .'?'.$this->generateUrlQueryString($exclude, $extend);
    }

    /**
     * @param array $exclude
     * @param bool  $extend
     *
     * @throws \Exception
     *
     * @return string
     */
    private function generateUrlQueryString(array $exclude = [], $extend = false): string
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

    /**
     * @param $exclude
     *
     * @return bool
     */
    private function validateExcludes(array $exclude): bool
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
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getUnits(): string
    {
        return $this->units;
    }

    /**
     * @param $units
     *
     * @throws \Exception
     */
    public function setUnits(string $units)
    {
        if (!in_array($units, self::VALID_UNITS)) {
            $validUnits = implode(',', self::VALID_UNITS);

            throw new \Exception("'{$units}' is not a valid unit. Valid units: {$validUnits}");
        }

        $this->units = $units;
    }
}
