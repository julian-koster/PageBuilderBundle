<?php

namespace JulianKoster\PageBuilderBundle\Enum;

enum PageBuilderFieldTypes: string
{
    public CONST array SIMPlE_TYPES = ['string'];
    public CONST array CONDITIONAL_TYPES = ['conditional'];
    public CONST array IMAGE_TYPES = ['image'];

    public CONST array LINK_TYPES = ['link'];

    public static function isSimpleType(string $type): bool
    {
        return in_array($type, self::SIMPlE_TYPES, true);
    }

    public static function getSimpleTypes(): string
    {
        return implode(', ', self::SIMPlE_TYPES);
    }

    public static function getConditionalTypes(): string
    {
        return implode(', ', self::CONDITIONAL_TYPES);
    }

    public static function isConditionalType(string $type): bool
    {
        return in_array($type, self::CONDITIONAL_TYPES, true);
    }

    public static function isImageType(string $type): bool
    {
        return in_array($type, self::IMAGE_TYPES, true);
    }

    public static function getImageTypes(): string
    {
        return implode(', ', self::IMAGE_TYPES);
    }

    public static function isLinkType(string $type): bool
    {
        return in_array($type, self::LINK_TYPES, true);
    }

    public static function getLinkTypes(): string
    {
        return implode(', ', self::LINK_TYPES);
    }
}
