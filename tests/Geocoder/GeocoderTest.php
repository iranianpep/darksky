<?php

namespace Geocoder;

use PHPUnit\Framework\TestCase;

class GeocoderTest extends TestCase
{
    public function testGeocode()
    {
        $geocoder = new Geocoder();

        $this->assertEquals('hi', $geocoder->geocode(''));
    }
}
