<?php

namespace JulianKoster\PageBuilderBundle\Enum;

enum PageBuilderPageStatus: string
{
    case DRAFT = 'DRAFT';
    case PUBLISHED = 'PUBLISHED';
    case ARCHIVE = 'ARCHIVED';
    case DELETED = 'DELETED';
}
