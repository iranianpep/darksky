<?php

namespace Darksky;

/**
 * Class Get.
 */
class Get implements RequestMethod
{
    /**
     * Get constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $requestUrl
     *
     * @throws DarkskyException
     *
     * @return bool|string
     */
    public function submit(string $requestUrl)
    {
        $content = @file_get_contents($requestUrl);

        if ($content === false) {
            throw new DarkskyException("Failed reading: '{$requestUrl}'");
        }

        return $content;
    }
}
