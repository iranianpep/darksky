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
     *
     * @return GuzzleHttp\Psr7\Response
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
