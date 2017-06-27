<?php

namespace Geocoder;

use PHPUnit\Framework\TestCase;

class GeocoderTest extends TestCase
{
    public function testAsRaw()
    {
        $this->assertEquals(
            $this->getExpectedData(),
            (new Geocoder())->geocode('Melbourne, Australia')->asRaw()
        );
    }

    public function testAsArray()
    {
        $expected = json_decode($this->getExpectedData(), true);
        $this->assertEquals($expected, (new Geocoder())->geocode('Melbourne, Australia')->asArray());
    }

    public function testAsObject()
    {
        $expected = json_decode($this->getExpectedData());
        $this->assertEquals($expected, (new Geocoder())->geocode('Melbourne, Australia')->asObject());
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
