<?php

namespace JulianKoster\PageBuilderBundle\Service;

class KeySanitizer
{
    /**
     * @param string $key the full key, for example: "HeroBtnUrl[url]"
     * @return ?string the base key: "HeroBtnUrl".
     */
    public function extractBaseKeyFromKey(string $key): ?string
    {
        return preg_replace('/\[(.*?)\]$/', '', $key);
    }
}