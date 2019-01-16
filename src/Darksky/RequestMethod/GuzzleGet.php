<?php

namespace Darksky;

use GuzzleHttp\Client;

/**
 * Class GuzzleGet.
 */
class GuzzleGet implements RequestMethod
{
    /**
     * GuzzleGet constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $requestUrl
     *
     * @throws DarkskyException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function submit(string $requestUrl)
    {
        $client = new Client();

        try {
            $response = $client->request('GET', $requestUrl);
        } catch (\Exception $e) {
            throw new DarkskyException($e->getMessage());
        }

        return $response->getBody();
    }
}
