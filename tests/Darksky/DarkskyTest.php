<?php

namespace Darksky;

use PHPUnit\Framework\TestCase;

class DarkskyTest extends TestCase
{
    const API_KEY = '12345';
    const API_KEY_2 = '123456';
    const LAT = '42.3601';
    const LONG = '-71.0589';
    const TIMEZONE = 'America/New_York';
    const MINUTELY = 'minutely';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const ALERTS = 'alerts';
    const EXCLUDES = [self::MINUTELY, self::HOURLY, self::DAILY, self::ALERTS];
    const FORECAST_FUNCTION = 'forecast';
    const PHPUNIT_WARNING = '\PHPUnit\Framework\Error\Warning';
    const HTTP_ERROR = 'HTTP request failed! HTTP/1.1 403 Forbidden';

    public function testForecast()
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createMock(Darksky::class);

        // Configure the stub.
        $stub->method(self::FORECAST_FUNCTION)
            ->willReturn($this->getSampleResponse());

        $result = $stub->forecast(self::LAT, self::LONG);
        $result = json_decode($result, true);

        $this->assertEquals(self::TIMEZONE, $result['timezone']);
    }

    public function testForecastEmptyExcludeAndHourly()
    {
        $darksky = new Darksky(self::API_KEY);
        $this->expectException(self::PHPUNIT_WARNING);
        $baseURL = 'https://api.darksky.net/forecast/12345/42.3601,-71.0589';
        $queryString = 'lang=en&units=auto&extend=hourly';
        $httpError = self::HTTP_ERROR;
        $this->expectExceptionMessage(
            "file_get_contents({$baseURL}?{$queryString}): failed to open stream: {$httpError}"
        );

        $darksky->forecast(self::LAT, self::LONG, [], true);
    }

    public function testForecastWithExcludeAndHourly()
    {
        $darksky = new Darksky(self::API_KEY);
        $this->expectException(self::PHPUNIT_WARNING);
        $baseURL = 'https://api.darksky.net/forecast/12345/42.3601,-71.0589';
        $queryString = 'lang=en&units=auto&exclude=minutely%2Chourly%2Cdaily%2Calerts&extend=hourly';
        $httpError = self::HTTP_ERROR;
        $this->expectExceptionMessage(
            "file_get_contents({$baseURL}?{$queryString}): failed to open stream: {$httpError}"
        );

        $darksky->forecast(self::LAT, self::LONG, self::EXCLUDES, true);
    }

    public function testForecastWithExclude()
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createMock(Darksky::class);

        // Configure the stub.
        $stub->method(self::FORECAST_FUNCTION)
            ->with($this->isType('array'), $this->isType('bool'))
            ->will($this->returnValue($this->getSampleResponse(self::EXCLUDES)));

        $result = $stub->forecast(self::EXCLUDES, true);
        $result = json_decode($result, true);

        $this->assertTrue(isset($result['currently']));
        $this->assertFalse(isset($result[self::MINUTELY]));
        $this->assertFalse(isset($result[self::HOURLY]));
        $this->assertFalse(isset($result[self::MINUTELY]));
        $this->assertFalse(isset($result[self::ALERTS]));
        $this->assertTrue(isset($result['flags']));

        $this->expectException('\Exception');
        $validExcludes = implode(',', Darksky::VALID_EXCLUDE);
        $this->expectExceptionMessage("Invalid excludes. Provide valid excludes: {$validExcludes}");

        $darksky = new Darksky(self::API_KEY);
        $darksky->forecast(self::LAT, self::LONG, [
            self::MINUTELY, self::HOURLY, self::MINUTELY, self::ALERTS, 'invalid-exclude',
        ]);
    }

    public function testForecastHourly()
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createMock(Darksky::class);

        // Configure the stub.
        $stub->method(self::FORECAST_FUNCTION)
            ->willReturn($this->getSampleResponse());

        $result = $stub->forecast(self::LAT, self::LONG);
        $result = json_decode($result, true);

        // next 48 hours
        $this->assertEquals(49, count($result[self::HOURLY]['data']));

        // Configure the stub.
        $stub = $this->createMock(Darksky::class);
        $stub->method(self::FORECAST_FUNCTION)
            ->willReturn($this->getSampleResponse([], true));

        $result = $stub->forecast([], true);
        $result = json_decode($result, true);

        // next 168 hours
        $this->assertEquals(169, count($result[self::HOURLY]['data']));
    }

    public function testTimeMachine()
    {
        // Create a stub for the SomeClass class.
        $stub = $this->createMock(Darksky::class);

        // Configure the stub.
        $stub->method('timeMachine')
            ->willReturn($this->getSampleTimeMachineResponse());

        $result = $stub->timeMachine(self::LAT, self::LONG, '409467600');
        $result = json_decode($result, true);

        $this->assertEquals(self::TIMEZONE, $result['timezone']);
    }

    public function testTimeMachineWithException()
    {
        $darksky = new Darksky(self::API_KEY);
        $this->expectException(self::PHPUNIT_WARNING);
        $baseURL = 'https://api.darksky.net/forecast/12345/42.3601,-71.0589,409467600';
        $queryString = 'lang=en&units=auto';
        $httpError = self::HTTP_ERROR;
        $this->expectExceptionMessage(
            "file_get_contents({$baseURL}?{$queryString}): failed to open stream: {$httpError}"
        );

        $darksky->timeMachine(self::LAT, self::LONG, '409467600');
    }

    public function testSetUnits()
    {
        $darksky = new Darksky(self::API_KEY);
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
        $darksky = new Darksky(self::API_KEY);
        $this->assertEquals('en', $darksky->getLanguage());

        $darksky->setLanguage('ar');
        $this->assertEquals('ar', $darksky->getLanguage());
    }

    public function testGetKey()
    {
        $darksky = new Darksky(self::API_KEY);
        $this->assertEquals(self::API_KEY, $darksky->getKey());

        $darksky->setKey(self::API_KEY_2);
        $this->assertEquals(self::API_KEY_2, $darksky->getKey());
    }

    private function getJsonResponse()
    {
        return '
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
    }

    private function getSampleResponse(array $excludes = [], $hourly = false)
    {
        $response = $this->getJsonResponse();
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
        if (isset($response[self::HOURLY])) {
            $firstData = $response[self::HOURLY]['data'][0];

            $limit = $hourly === true ? 168 : 48;

            for ($i = 0; $i < $limit; $i++) {
                $response[self::HOURLY]['data'][] = $firstData;
            }
        }

        return json_encode($response);
    }

    private function getSampleTimeMachineResponse()
    {
        return '{
            "latitude":42.3601,
   "longitude":-71.0589,
   "timezone":"America/New_York",
   "currently":{
            "time":409467600,
      "summary":"Mostly Cloudy",
      "icon":"partly-cloudy-night",
      "precipIntensity":0,
      "precipProbability":0,
      "temperature":28.37,
      "apparentTemperature":20,
      "dewPoint":16.29,
      "humidity":0.6,
      "pressure":1026.41,
      "windSpeed":8.68,
      "windBearing":250,
      "cloudCover":0.73,
      "uvIndex":0,
      "visibility":9.62
   },
   "hourly":{
            "summary":"Snow (1–2 in.) starting in the morning.",
      "icon":"snow",
      "data":[
         {
             "time":409467600,
            "summary":"Mostly Cloudy",
            "icon":"partly-cloudy-night",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":28.37,
            "apparentTemperature":20,
            "dewPoint":16.29,
            "humidity":0.6,
            "pressure":1026.41,
            "windSpeed":8.68,
            "windBearing":250,
            "cloudCover":0.73,
            "uvIndex":0,
            "visibility":9.62
         },
         {
             "time":409471200,
            "summary":"Mostly Cloudy",
            "icon":"partly-cloudy-night",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":28.81,
            "apparentTemperature":21.21,
            "dewPoint":16.17,
            "humidity":0.59,
            "pressure":1026.34,
            "windSpeed":7.63,
            "windBearing":248,
            "cloudCover":0.78,
            "uvIndex":0,
            "visibility":9.69
         },
         {
             "time":409474800,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":29.09,
            "apparentTemperature":20.95,
            "dewPoint":15.54,
            "humidity":0.56,
            "pressure":1027.13,
            "windSpeed":8.56,
            "windBearing":290,
            "cloudCover":0.97,
            "uvIndex":0,
            "visibility":9.62
         },
         {
             "time":409478400,
            "summary":"Partly Cloudy",
            "icon":"partly-cloudy-night",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":28.85,
            "apparentTemperature":21.35,
            "dewPoint":15.6,
            "humidity":0.57,
            "pressure":1027.39,
            "windSpeed":7.49,
            "windBearing":298,
            "cloudCover":0.34,
            "uvIndex":0,
            "visibility":9.62
         },
         {
             "time":409482000,
            "summary":"Partly Cloudy",
            "icon":"partly-cloudy-night",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":28.59,
            "apparentTemperature":21.99,
            "dewPoint":16.15,
            "humidity":0.59,
            "pressure":1027.01,
            "windSpeed":6.22,
            "windBearing":302,
            "cloudCover":0.59,
            "uvIndex":0,
            "visibility":9.67
         },
         {
             "time":409485600,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":29.26,
            "apparentTemperature":23.99,
            "dewPoint":17.32,
            "humidity":0.61,
            "pressure":1027.48,
            "windSpeed":4.86,
            "windBearing":297,
            "cloudCover":0.96,
            "uvIndex":0,
            "visibility":9.62
         },
         {
             "time":409489200,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":29.7,
            "apparentTemperature":23.71,
            "dewPoint":17.69,
            "humidity":0.6,
            "pressure":1028.14,
            "windSpeed":5.74,
            "windBearing":304,
            "cloudCover":0.96,
            "uvIndex":0,
            "visibility":9.57
         },
         {
             "time":409492800,
            "summary":"Mostly Cloudy",
            "icon":"partly-cloudy-night",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":30.36,
            "apparentTemperature":24.47,
            "dewPoint":18.37,
            "humidity":0.61,
            "pressure":1028.35,
            "windSpeed":5.76,
            "windBearing":307,
            "cloudCover":0.92,
            "uvIndex":0,
            "visibility":9.48
         },
         {
             "time":409496400,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0.001,
            "precipProbability":0.05,
            "precipAccumulation":0.01,
            "precipType":"snow",
            "temperature":29.64,
            "apparentTemperature":23.74,
            "dewPoint":18.44,
            "humidity":0.63,
            "pressure":1028.97,
            "windSpeed":5.62,
            "windBearing":13,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":9.02
         },
         {
             "time":409500000,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0.001,
            "precipProbability":0.05,
            "precipAccumulation":0.009,
            "precipType":"snow",
            "temperature":29.94,
            "apparentTemperature":24.18,
            "dewPoint":19.9,
            "humidity":0.66,
            "pressure":1029.54,
            "windSpeed":5.53,
            "windBearing":30,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":7.2
         },
         {
             "time":409503600,
            "summary":"Flurries",
            "icon":"snow",
            "precipIntensity":0.0024,
            "precipProbability":0.1,
            "precipAccumulation":0.02,
            "precipType":"snow",
            "temperature":30.53,
            "apparentTemperature":26.73,
            "dewPoint":20.03,
            "humidity":0.65,
            "pressure":1029.91,
            "windSpeed":3.7,
            "windBearing":16,
            "cloudCover":0.88,
            "uvIndex":1,
            "visibility":3.41
         },
         {
             "time":409507200,
            "summary":"Flurries",
            "icon":"snow",
            "precipIntensity":0.002,
            "precipProbability":0.1,
            "precipAccumulation":0.017,
            "precipType":"snow",
            "temperature":30.61,
            "apparentTemperature":30.61,
            "dewPoint":23.82,
            "humidity":0.76,
            "pressure":1030.11,
            "windSpeed":1.11,
            "windBearing":160,
            "cloudCover":1,
            "uvIndex":1,
            "visibility":2.03
         },
         {
             "time":409510800,
            "summary":"Snow",
            "icon":"snow",
            "precipIntensity":0.0276,
            "precipProbability":0.95,
            "precipAccumulation":0.241,
            "precipType":"snow",
            "temperature":30.21,
            "apparentTemperature":30.21,
            "dewPoint":25.77,
            "humidity":0.83,
            "pressure":1029.93,
            "windSpeed":2.61,
            "windBearing":136,
            "cloudCover":1,
            "uvIndex":1,
            "visibility":1.61
         },
         {
             "time":409514400,
            "summary":"Snow",
            "icon":"snow",
            "precipIntensity":0.0199,
            "precipProbability":1,
            "precipAccumulation":0.162,
            "precipType":"snow",
            "temperature":31.22,
            "apparentTemperature":26.34,
            "dewPoint":26.96,
            "humidity":0.84,
            "pressure":1029.48,
            "windSpeed":4.81,
            "windBearing":118,
            "cloudCover":1,
            "uvIndex":1,
            "visibility":1.25
         },
         {
             "time":409518000,
            "summary":"Foggy",
            "icon":"fog",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":31.26,
            "apparentTemperature":27.02,
            "dewPoint":26.72,
            "humidity":0.83,
            "pressure":1029.32,
            "windSpeed":4.19,
            "windBearing":97,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":1.89
         },
         {
             "time":409521600,
            "summary":"Snow",
            "icon":"snow",
            "precipIntensity":0.0185,
            "precipProbability":0.95,
            "precipAccumulation":0.152,
            "precipType":"snow",
            "temperature":31.04,
            "apparentTemperature":27.84,
            "dewPoint":27.4,
            "humidity":0.86,
            "pressure":1029.63,
            "windSpeed":3.28,
            "windBearing":85,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":1.47
         },
         {
             "time":409525200,
            "summary":"Light Snow",
            "icon":"snow",
            "precipIntensity":0.0109,
            "precipProbability":0.95,
            "precipAccumulation":0.086,
            "precipType":"snow",
            "temperature":31.55,
            "apparentTemperature":27.87,
            "dewPoint":28.02,
            "humidity":0.87,
            "pressure":1029.5,
            "windSpeed":3.73,
            "windBearing":89,
            "cloudCover":0.92,
            "uvIndex":0,
            "visibility":3.77
         },
         {
             "time":409528800,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0.001,
            "precipProbability":0.1,
            "precipAccumulation":0.008,
            "precipType":"snow",
            "temperature":31.36,
            "apparentTemperature":27.91,
            "dewPoint":26.19,
            "humidity":0.81,
            "pressure":1030.06,
            "windSpeed":3.52,
            "windBearing":119,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":2.89
         },
         {
             "time":409532400,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":31.18,
            "apparentTemperature":27.54,
            "dewPoint":27.76,
            "humidity":0.87,
            "pressure":1030.05,
            "windSpeed":3.65,
            "windBearing":142,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":3.35
         },
         {
             "time":409536000,
            "summary":"Mostly Cloudy",
            "icon":"partly-cloudy-night",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":31.79,
            "apparentTemperature":28.44,
            "dewPoint":28.25,
            "humidity":0.87,
            "pressure":1030.27,
            "windSpeed":3.48,
            "windBearing":121,
            "cloudCover":0.91,
            "uvIndex":0,
            "visibility":5.27
         },
         {
             "time":409539600,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0,
            "precipProbability":0,
            "temperature":31.41,
            "apparentTemperature":26.69,
            "dewPoint":27.84,
            "humidity":0.86,
            "pressure":1030.7,
            "windSpeed":4.67,
            "windBearing":27,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":4.68
         },
         {
             "time":409543200,
            "summary":"Overcast",
            "icon":"cloudy",
            "precipIntensity":0.001,
            "precipProbability":0.1,
            "precipAccumulation":0.008,
            "precipType":"snow",
            "temperature":31.53,
            "apparentTemperature":27.44,
            "dewPoint":28.4,
            "humidity":0.88,
            "pressure":1030.4,
            "windSpeed":4.09,
            "windBearing":28,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":3.59
         },
         {
             "time":409546800,
            "summary":"Snow",
            "icon":"snow",
            "precipIntensity":0.027,
            "precipProbability":0.9,
            "precipAccumulation":0.206,
            "precipType":"snow",
            "temperature":32.49,
            "apparentTemperature":28.45,
            "dewPoint":29.35,
            "humidity":0.88,
            "pressure":1030.11,
            "windSpeed":4.18,
            "windBearing":26,
            "cloudCover":0.78,
            "uvIndex":0,
            "visibility":3.39
         },
         {
             "time":409550400,
            "summary":"Snow",
            "icon":"snow",
            "precipIntensity":0.027,
            "precipProbability":0.9,
            "precipAccumulation":0.213,
            "precipType":"snow",
            "temperature":31.58,
            "apparentTemperature":25.96,
            "dewPoint":29.44,
            "humidity":0.92,
            "pressure":1030.68,
            "windSpeed":5.7,
            "windBearing":4,
            "cloudCover":1,
            "uvIndex":0,
            "visibility":1.71
         }
      ]
   },
   "daily":{
            "data":[
         {
             "time":409467600,
            "summary":"Snow (1–2 in.) until afternoon, starting again overnight.",
            "icon":"snow",
            "sunriseTime":409493550,
            "sunsetTime":409526218,
            "moonPhase":0.26,
            "precipIntensity":0.0058,
            "precipIntensityMax":0.0276,
            "precipIntensityMaxTime":409510800,
            "precipProbability":1,
            "precipAccumulation":1.13,
            "precipType":"snow",
            "temperatureHigh":31.79,
            "temperatureHighTime":409536000,
            "temperatureLow":31.41,
            "temperatureLowTime":409539600,
            "apparentTemperatureHigh":30.61,
            "apparentTemperatureHighTime":409507200,
            "apparentTemperatureLow":25.96,
            "apparentTemperatureLowTime":409550400,
            "dewPoint":22.81,
            "humidity":0.74,
            "pressure":1029.04,
            "windSpeed":1.42,
            "windBearing":340,
            "cloudCover":0.91,
            "uvIndex":1,
            "uvIndexTime":409503600,
            "visibility":5.56,
            "temperatureMin":28.37,
            "temperatureMinTime":409467600,
            "temperatureMax":32.49,
            "temperatureMaxTime":409546800,
            "apparentTemperatureMin":20,
            "apparentTemperatureMinTime":409467600,
            "apparentTemperatureMax":30.61,
            "apparentTemperatureMaxTime":409507200
         }
      ]
   },
   "flags":{
            "sources":[
                "isd"
            ],
      "isd-stations":[
                "725059-99999",
                "725065-99999",
                "725070-14765",
                "725076-99999",
                "725090-14739",
                "725095-94746",
                "725096-99999",
                "725097-14790",
                "725098-99999",
                "725099-99999",
                "726054-99999",
                "726067-99999",
                "726069-99999",
                "743945-14710",
                "744900-14702",
                "744905-04779"
            ],
      "units":"us"
   },
   "offset":-5
}';
    }
}
