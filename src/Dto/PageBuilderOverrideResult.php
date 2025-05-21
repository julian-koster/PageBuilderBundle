<?php

namespace JulianKoster\PageBuilderBundle\Dto;

readonly class PageBuilderOverrideResult
{
    public function __construct(
        public string $type,
        public string $key,
        public string $instanceId,
        public array $value,
    )
    {
    }

    public function get(string $subKey): mixed
    {
        return $this->value[$subKey] ?? null;
    }
}