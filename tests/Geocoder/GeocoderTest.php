<?php

namespace Geocoder;

use PHPUnit\Framework\TestCase;

class GeocoderTest extends TestCase
{
    public function testGeocode()
    {
        $geocoder = new Geocoder();

        $expected = [
            'results' => [
                [
                    'address_components' => [
                            [
                                'long_name'  => 'Melbourne',
                                'short_name' => 'Melbourne',
                                'types'      => [
                                        0 => 'colloquial_area',
                                        1 => 'locality',
                                        2 => 'political',
                                    ],
                            ],
                            [
                                'long_name'  => 'Victoria',
                                'short_name' => 'VIC',
                                'types'      => [
                                        0 => 'administrative_area_level_1',
                                        1 => 'political',
                                    ],
                            ],
                            [
                                'long_name'  => 'Australia',
                                'short_name' => 'AU',
                                'types'      => [
                                        0 => 'country',
                                        1 => 'political',
                                    ],
                            ],
                        ],
                    'formatted_address' => 'Melbourne VIC, Australia',
                    'geometry'          => [
                            'bounds' => [
                                    'northeast' => [
                                            'lat' => -37.51127369999999672245394322089850902557373046875,
                                            'lng' => 145.512528800000012552118278108537197113037109375,
                                        ],
                                    'southwest' => [
                                            'lat' => -38.43385930000000172412910615094006061553955078125,
                                            'lng' => 144.593741800000003649984137155115604400634765625,
                                        ],
                                ],
                            'location' => [
                                    'lat' => -37.81362759999999667570591554976999759674072265625,
                                    'lng' => 144.96305760000001328080543316900730133056640625,
                                ],
                            'location_type' => 'APPROXIMATE',
                            'viewport'      => [
                                    'northeast' => [
                                            'lat' => -37.51127369999999672245394322089850902557373046875,
                                            'lng' => 145.512528800000012552118278108537197113037109375,
                                        ],
                                    'southwest' => [
                                            'lat' => -38.43385930000000172412910615094006061553955078125,
                                            'lng' => 144.593741800000003649984137155115604400634765625,
                                        ],
                                ],
                        ],
                    'place_id' => 'ChIJ90260rVG1moRkM2MIXVWBAQ',
                    'types'    => [
                            'colloquial_area',
                            'locality',
                            'political',
                        ],
                ],
            ],
            'status' => 'OK',
        ];

        $this->assertEquals($expected, $geocoder->geocode('Melbourne, Australia'));
    }
}
