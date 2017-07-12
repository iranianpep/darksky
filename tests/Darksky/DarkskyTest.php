<?php

namespace Darksky;

use PHPUnit\Framework\TestCase;

class DarkskyTest extends TestCase
{
    const API_KEY = '12345';
    const LAT = '42.3601';
    const LONG = '-71.0589';
    const TIMEZONE = 'America/New_York';

    public function testForecast()
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createMock(Darksky::class);

        // Configure the stub.
        $stub->method('forecast')
            ->willReturn($this->getSampleResponse());

        $result = $stub->forecast();
        $result = json_decode($result, true);

        $this->assertEquals(self::TIMEZONE, $result['timezone']);
    }

    public function testForecastWithExcludeAndHourly()
    {
        $darksky = new Darksky(self::API_KEY, self::LAT, self::LONG);
        $this->expectException('\PHPUnit\Framework\Error\Warning');
        $this->expectExceptionMessage(
            "file_get_contents(https://api.darksky.net/forecast/12345/42.3601,-71.0589
            ?lang=en&units=auto&exclude=minutely%2Chourly%2Cdaily%2Calerts):
            failed to open stream: HTTP request failed! HTTP/1.1 403 Forbidden"
        );

        $darksky->forecast(['minutely', 'hourly', 'daily', 'alerts'], true);
    }

    public function testForecastWithExclude()
    {
        $excludes = ['minutely', 'hourly', 'daily', 'alerts'];

        // Create a stub for the SomeClass class.
        $stub = $this->createMock(Darksky::class);

        // Configure the stub.
        $stub->method('forecast')
            ->with($this->isType('array'), $this->isType('bool'))
            ->will($this->returnValue($this->getSampleResponse($excludes)));

        $result = $stub->forecast($excludes, true);
        $result = json_decode($result, true);

        $this->assertTrue(isset($result['currently']));
        $this->assertFalse(isset($result['minutely']));
        $this->assertFalse(isset($result['hourly']));
        $this->assertFalse(isset($result['daily']));
        $this->assertFalse(isset($result['alerts']));
        $this->assertTrue(isset($result['flags']));

        $this->expectException('\Exception');
        $validExcludes = implode(',', Darksky::VALID_EXCLUDE);
        $this->expectExceptionMessage("Invalid excludes. Provide valid excludes: {$validExcludes}");

        $darksky = new Darksky(self::API_KEY, self::LAT, self::LONG);
        $darksky->forecast(['minutely', 'hourly', 'daily', 'alerts', 'invalid-exclude']);
    }

    public function testForecastHourly()
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createMock(Darksky::class);

        // Configure the stub.
        $stub->method('forecast')
            ->willReturn($this->getSampleResponse());

        $result = $stub->forecast();
        $result = json_decode($result, true);

        // next 48 hours
        $this->assertEquals(49, count($result['hourly']['data']));

        // Configure the stub.
        $stub = $this->createMock(Darksky::class);
        $stub->method('forecast')
            ->willReturn($this->getSampleResponse([], true));

        $result = $stub->forecast([], true);
        $result = json_decode($result, true);

        // next 168 hours
        $this->assertEquals(169, count($result['hourly']['data']));
    }

    public function testSetUnits()
    {
        $darksky = new Darksky(self::API_KEY, self::LAT, self::LONG);
        $this->assertEquals('auto', $darksky->getUnits());

        $darksky->setUnits('si');
        $this->assertEquals('si', $darksky->getUnits());

        $this->expectException('\Exception');
        $validUnits = implode(',', Darksky::VALID_UNITS);
        $this->expectExceptionMessage("'invalid-units' is not a valid unit. Valid units: {$validUnits}");

        $darksky->setUnits('invalid-units');
    }

    public function testGetLanguage()
    {
        $darksky = new Darksky(self::API_KEY, self::LAT, self::LONG);
        $this->assertEquals('en', $darksky->getLanguage());

        $darksky->setLanguage('ar');
        $this->assertEquals('ar', $darksky->getLanguage());
    }

    public function testGetKey()
    {
        $darksky = new Darksky(self::API_KEY, self::LAT, self::LONG);
        $this->assertEquals(self::API_KEY, $darksky->getKey());

        $darksky->setKey('12345');
        $this->assertEquals('12345', $darksky->getKey());
    }

    public function testGetLongitude()
    {
        $darksky = new Darksky(self::API_KEY, self::LAT, self::LONG);
        $this->assertEquals(self::LONG, $darksky->getLongitude());
    }

    public function testGetLatitude()
    {
        $darksky = new Darksky(self::API_KEY, self::LAT, self::LONG);
        $this->assertEquals(self::LAT, $darksky->getLatitude());
    }

    private function getSampleResponse(array $excludes = [], $hourly = false)
    {
        $response = '
        {
  "latitude": '.self::LAT.',
  "longitude": '.self::LONG.',
  "timezone": "'.self::TIMEZONE.'",
  "currently": {
    "time": 1453402675,
    "summary": "Rain",
    "icon": "rain",
    "nearestStormDistance": 0,
    "precipIntensity": 0.1685,
    "precipIntensityError": 0.0067,
    "precipProbability": 1,
    "precipType": "rain",
    "temperature": 48.71,
    "apparentTemperature": 46.93,
    "dewPoint": 47.7,
    "humidity": 0.96,
    "windSpeed": 4.64,
    "windGust": 9.86,
    "windBearing": 186,
    "visibility": 4.3,
    "cloudCover": 0.73,
    "pressure": 1009.7,
    "ozone": 328.35
  },
  "minutely": {
    "summary": "Rain for the hour.",
    "icon": "rain",
    "data": [
      {
        "time": 1453402620,
        "precipIntensity": 0.1715,
        "precipIntensityError": 0.0066,
        "precipProbability": 1,
        "precipType": "rain"
      }
    ]
  },
  "hourly": {
    "summary": "Rain throughout the day.",
    "icon": "rain",
    "data": [
      {
        "time": 1453399200,
        "summary": "Rain",
        "icon": "rain",
        "precipIntensity": 0.1379,
        "precipProbability": 0.85,
        "precipType": "rain",
        "temperature": 48.16,
        "apparentTemperature": 46.41,
        "dewPoint": 46.89,
        "humidity": 0.95,
        "windSpeed": 4.47,
        "windGust": 10.22,
        "windBearing": 166,
        "visibility": 3.56,
        "cloudCover": 0.39,
        "pressure": 1009.97,
        "ozone": 328.71
      }
    ]
  },
  "daily": {
    "summary": "Light rain throughout the week, with temperatures bottoming out at 48°F on Sunday.",
    "icon": "rain",
    "data": [
      {
        "time": 1453363200,
        "summary": "Rain throughout the day.",
        "icon": "rain",
        "sunriseTime": 1453391560,
        "sunsetTime": 1453424361,
        "moonPhase": 0.43,
        "precipIntensity": 0.1134,
        "precipIntensityMax": 0.1722,
        "precipIntensityMaxTime": 1453392000,
        "precipProbability": 0.87,
        "precipType": "rain",
        "temperatureMin": 41.42,
        "temperatureMinTime": 1453363200,
        "temperatureMax": 53.27,
        "temperatureMaxTime": 1453417200,
        "apparentTemperatureMin": 36.68,
        "apparentTemperatureMinTime": 1453363200,
        "apparentTemperatureMax": 53.27,
        "apparentTemperatureMaxTime": 1453417200,
        "dewPoint": 46.79,
        "humidity": 0.95,
        "windSpeed": 4.26,
        "windBearing": 150,
        "visibility": 4.02,
        "cloudCover": 0.77,
        "pressure": 1009.35,
        "ozone": 326.69
      }
    ]
  },
  "alerts": [
    {
      "title": "Flood Watch for Mason, WA",
      "time": 1453375020,
      "expires": 1453407300,
      "description": "FLOOD WATCH REMAINS IN EFFECT THROUGH LATE FRIDAY NIGHT"
    }
  ],
  "flags": {
    "units": "us"
  }
}
        ';

        $response = json_decode($response, true);

        // Filter the sample response based on excludes
        if (!empty($excludes)) {
            foreach ($excludes as $exclude) {
                if (isset($response[$exclude])) {
                    unset($response[$exclude]);
                }
            }
        }

        // Simulate 49 hourly data
        if (isset($response['hourly'])) {
            $firstData = $response['hourly']['data'][0];

            $limit = $hourly === true ? 168 : 48;

            for ($i = 0; $i < $limit; $i++) {
                $response['hourly']['data'][] = $firstData;
            }
        }

        return json_encode($response);
    }
}
