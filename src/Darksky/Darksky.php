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
    private $language;
    private $units;

    /**
     * Darksky constructor.
     *
     * @param string $key
     * @param string $lang
     * @param string $units
     *
     * @throws \Exception
     */
    public function __construct(
        string $key,
        string $lang = 'en',
        string $units = 'auto'
    ) {
        $this->setKey($key);
        $this->setLanguage($lang);
        $this->setUnits($units);
    }

    /**
     * @param       $latitude
     * @param       $longitude
     * @param array $exclude
     * @param bool  $extend
     *
     * @throws \Exception
     *
     * @return string
     */
    public function forecast($latitude, $longitude, array $exclude = [], $extend = false)
    {
        try {
            return file_get_contents($this->generateRequestUrl($latitude, $longitude, $exclude, $extend));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param $time
     * @param array $exclude
     *
     * @throws \Exception
     *
     * @return bool|string
     */
    public function timeMachine($latitude, $longitude, string $time, array $exclude = [])
    {
        try {
            return file_get_contents($this->generateRequestUrl($latitude, $longitude, $exclude, false, $time));
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
     * @param $latitude
     * @param $longitude
     * @param array  $exclude
     * @param bool   $extend
     * @param string $time
     *
     * @throws \Exception
     *
     * @return string
     */
    private function generateRequestUrl(
        $latitude,
        $longitude,
        array $exclude = [],
        $extend = false,
        string $time = ''
    ): string {
        if (!empty($time)) {
            $time = ",{$time}";
        }

        return self::API_BASE_URL.'/'.$this->getKey().'/'.$latitude.','.$longitude.$time
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
        $queryString = ['lang' => $this->getLanguage(), 'units' => $this->getUnits()];

        // validate $exclude
        if ($this->validateExcludes($exclude) !== true) {
            $validExcludes = implode(',', self::VALID_EXCLUDE);

            throw new DarkskyException("Invalid excludes. Provide valid excludes: {$validExcludes}'");
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

            throw new DarkskyException("'{$units}' is not a valid unit. Valid units: {$validUnits}");
        }

        $this->units = $units;
    }
}
