<?php

namespace JulianKoster\PageBuilderBundle\Helper;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockOverrides;

class FieldContextHelper
{
    /**
     * @param PageBuilderBlockOverrides $override
     * @return string
     */
    public function detectLinkType(PageBuilderBlockOverrides $override): string
    {
        $fullOverrideKey = $override->getFieldKey();
        if (preg_match('/\[(url|email|path)]\[link]$/', $fullOverrideKey, $matches)) {
            if(count($matches) > 2)
            {
                throw new \UnexpectedValueException('When detecting the link type for override id: ' . $override->getId() . ' with fieldKey: ' . $fullOverrideKey . ' more than one unique link type identifier was found.');
            }
            return $matches[1]; // 'url', 'email' or 'path'
        }

        return '';
    }
}