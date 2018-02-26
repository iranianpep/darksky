# Darksky
A simple wrapper for Dark Sky API

[![Latest Stable Version](https://poser.pugx.org/darksky/darksky/v/stable)](https://packagist.org/packages/darksky/darksky)
[![Build Status](https://travis-ci.org/iranianpep/darksky.svg?branch=master)](https://travis-ci.org/iranianpep/darksky)
[![Build Status](https://scrutinizer-ci.com/g/iranianpep/darksky/badges/build.png?b=master)](https://scrutinizer-ci.com/g/iranianpep/darksky/build-status/master)
[![Maintainability](https://api.codeclimate.com/v1/badges/b30e33cb9589bffbe46e/maintainability)](https://codeclimate.com/github/iranianpep/darksky/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/b30e33cb9589bffbe46e/test_coverage)](https://codeclimate.com/github/iranianpep/darksky/test_coverage)
[![Code Coverage](https://scrutinizer-ci.com/g/iranianpep/darksky/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/iranianpep/darksky/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iranianpep/darksky/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iranianpep/darksky/?branch=master)
[![Issue Count](https://codeclimate.com/github/iranianpep/darksky/badges/issue_count.svg)](https://codeclimate.com/github/iranianpep/darksky)
[![License](https://poser.pugx.org/darksky/darksky/license)](https://packagist.org/packages/darksky/darksky)
[![StyleCI](https://styleci.io/repos/96892746/shield?branch=master)](https://styleci.io/repos/96892746)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/8575ff8e33034e0a81cedd9464ac359a)](https://www.codacy.com/app/iranianpep/darksky?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=iranianpep/darksky&amp;utm_campaign=Badge_Grade)
[![Packagist](https://img.shields.io/packagist/dt/darksky/darksky.svg)](https://packagist.org/packages/darksky/darksky)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/iranianpep/darksky/master/LICENSE)

## Server Requirements
- PHP >= 7.0.0

## Usage
- Get the API key from https://darksky.net/dev
- Install this package in your project:<br> `composer require darksky/darksky`
- To forecast:
```
try {
    $result = (new Darksky('API_KEY'))->forecast('LAT', 'LONG');
} catch(DarkskyException $e) {
    // handle the exception
} catch(Exception $e) {
    // handle the exception
}
```

- To use the time machine:
```
try {
    $result = (new Darksky('API_KEY'))->timeMachine('LAT', 'LONG', 'UNIX_TIME');
} catch(DarkskyException $e) {
    // handle the exception
} catch(Exception $e) {
    // handle the exception
}
```
