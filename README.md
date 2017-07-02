# Geocoder
A class for handling geocoding.

[![Latest Stable Version](https://poser.pugx.org/geocoder/geocoder/v/stable)](https://packagist.org/packages/geocoder/geocoder)
[![Build Status](https://travis-ci.org/iranianpep/geocoder.svg?branch=master)](https://travis-ci.org/iranianpep/geocoder)
[![Build Status](https://scrutinizer-ci.com/g/iranianpep/geocoder/badges/build.png?b=master)](https://scrutinizer-ci.com/g/iranianpep/geocoder/build-status/master)
[![Code Climate](https://codeclimate.com/github/iranianpep/geocoder/badges/gpa.svg)](https://codeclimate.com/github/iranianpep/geocoder)
[![Test Coverage](https://codeclimate.com/github/iranianpep/geocoder/badges/coverage.svg)](https://codeclimate.com/github/iranianpep/geocoder/coverage)
[![Code Coverage](https://scrutinizer-ci.com/g/iranianpep/geocoder/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/iranianpep/geocoder/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iranianpep/geocoder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iranianpep/geocoder/?branch=master)
[![Issue Count](https://codeclimate.com/github/iranianpep/geocoder/badges/issue_count.svg)](https://codeclimate.com/github/iranianpep/geocoder)
[![License](https://poser.pugx.org/geocoder/geocoder/license)](https://packagist.org/packages/geocoder/geocoder)
[![StyleCI](https://styleci.io/repos/95418474/shield?branch=master)](https://styleci.io/repos/95418474)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/8575ff8e33034e0a81cedd9464ac359a)](https://www.codacy.com/app/iranianpep/geocoder?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=iranianpep/geocoder&amp;utm_campaign=Badge_Grade)
[![Packagist](https://img.shields.io/packagist/dt/geocoder/geocoder.svg)](https://packagist.org/packages/geocoder/geocoder)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/iranianpep/geocoder/master/LICENSE)

## Usage
- Get the Google Maps Geocoding API key: https://developers.google.com/maps/documentation/geocoding/get-api-key
- `$result = (new Geocoder('THE_API_KEY'))->geocode('THE_ADDRESS');`
- To get latitude and longitude `(new Geocoder('THE_API_KEY'))->getLatLng('THE_ADDRESS')`