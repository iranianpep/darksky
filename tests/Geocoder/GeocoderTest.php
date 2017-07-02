<?php

namespace Geocoder;

use PHPUnit\Framework\TestCase;

class GeocoderTest extends TestCase
{
    const VALID_ADDRESS = 'Melbourne, Australia';
    const VALID_ADDRESS_MULTIPLE_RESULT = 'springfield';
    const UNKNOWN_ADDRESS = 'Dummy Address';

    public function testGeocode()
    {
        $this->assertEquals(
            $this->getExpectedData(),
            (new Geocoder())->geocode(self::VALID_ADDRESS)
        );
    }

    public function testGeocodeMultipleResult()
    {
        $result = (new Geocoder())->geocode(self::VALID_ADDRESS_MULTIPLE_RESULT);
        $result = json_decode($result, true);

        $this->assertEquals(
            6,
            count($result['results'])
        );
    }

    public function testGeocodeUnknownAddress()
    {
        $this->assertEquals(
            $this->getExpectedNullData(),
            (new Geocoder())->geocode(self::UNKNOWN_ADDRESS)
        );
    }

    public function testGeocodeInvalidFormat()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage("'invalidFormat' is not a valid format");

        (new Geocoder())->geocode(self::VALID_ADDRESS, '', 'invalidFormat');
    }

    public function testGeocodeWithRegion()
    {
        $this->assertEquals(
            $this->getExpectedData(),
            (new Geocoder())->geocode(self::VALID_ADDRESS, 'au')
        );
    }

    public function testGeocodeWithInvalidApiKey()
    {
        $this->assertEquals(
            '{
   "error_message" : "The provided API key is invalid.",
   "results" : [],
   "status" : "REQUEST_DENIED"
}
',
            (new Geocoder('invalidApiKey'))->geocode(self::VALID_ADDRESS, 'au')
        );
    }

    public function testGetLatLngUnknownAddress()
    {
        $this->assertEquals(
            null,
            (new Geocoder())->getLatLng(self::UNKNOWN_ADDRESS)
        );
    }

    public function testGetLatLng()
    {
        $this->assertEquals(
            [
                [
                    'lat' => '-37.8136276',
                    'lng' => '144.9630576',
                ],
            ],
            (new Geocoder())->getLatLng(self::VALID_ADDRESS)
        );
    }

    public function testGetLatLngMultipleResult()
    {
        $this->assertEquals(
            6,
            count((new Geocoder())->getLatLng(self::VALID_ADDRESS_MULTIPLE_RESULT))
        );
    }

    private function getExpectedNullData()
    {
        return '{
   "results" : [],
   "status" : "ZERO_RESULTS"
}
';
    }

    private function getExpectedData()
    {
        return '{
   "results" : [
      {
         "address_components" : [
            {
               "long_name" : "Melbourne",
               "short_name" : "Melbourne",
               "types" : [ "colloquial_area", "locality", "political" ]
            },
            {
               "long_name" : "Victoria",
               "short_name" : "VIC",
               "types" : [ "administrative_area_level_1", "political" ]
            },
            {
               "long_name" : "Australia",
               "short_name" : "AU",
               "types" : [ "country", "political" ]
            }
         ],
         "formatted_address" : "Melbourne VIC, Australia",
         "geometry" : {
            "bounds" : {
               "northeast" : {
                  "lat" : -37.5112737,
                  "lng" : 145.5125288
               },
               "southwest" : {
                  "lat" : -38.4338593,
                  "lng" : 144.5937418
               }
            },
            "location" : {
               "lat" : -37.8136276,
               "lng" : 144.9630576
            },
            "location_type" : "APPROXIMATE",
            "viewport" : {
               "northeast" : {
                  "lat" : -37.5112737,
                  "lng" : 145.5125288
               },
               "southwest" : {
                  "lat" : -38.4338593,
                  "lng" : 144.5937418
               }
            }
         },
         "place_id" : "ChIJ90260rVG1moRkM2MIXVWBAQ",
         "types" : [ "colloquial_area", "locality", "political" ]
      }
   ],
   "status" : "OK"
}
';
    }
}
