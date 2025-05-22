<?php

namespace JulianKoster\PageBuilderBundle\Service;

use JulianKoster\PageBuilderBundle\Enum\PageBuilderFieldTypes;
use JulianKoster\PageBuilderBundle\Dto\PageBuilderOverrideResult;

readonly class PageBuilderService
{
    public function __construct(
        private OverrideManager $overrideManager,
    )
    {
    }

    /**
     * @param string $instanceId the instance's id.
     * @param string|null $key it's identifier at the block level, e.g. "backgroundClasses" or "conditional[true]".
     * @param string|null $type the string representation of the override type, e.g.: "conditional" or "string".
     * @return mixed returns a single PageBuilderOverrideResult DTO.
     */
    public function getOverrideValues(string $instanceId, ?string $key, ?string $type): PageBuilderOverrideResult
    {
        if(PageBuilderFieldTypes::isSimpleType($type))
        {
            $override = $this->overrideManager->fetchSimpleOverride($instanceId, $key, $type);
            return new PageBuilderOverrideResult(
                type: $type,
                key: $key,
                instanceId: $instanceId,
                value: ['value' => $override?->getFieldValue()],
            );
        }
        elseif(PageBuilderFieldTypes::isLinkType($type))
        {
            $result = $this->overrideManager->fetchLinkOverride($instanceId, $key, $type);
            return new PageBuilderOverrideResult(
                type: $type,
                key: $key,
                instanceId: $instanceId,
                value: $result ?? [],
            );
        }
        elseif(PageBuilderFieldTypes::isConditionalType($type))
        {
            $result = $this->overrideManager->fetchConditionalOverride($instanceId, $key, $type);
            return new PageBuilderOverrideResult(
                type: $type,
                key: $key,
                instanceId: $instanceId,
                value: $result ?? [],
            );
        }
        elseif(PageBuilderFieldTypes::isImageType($type))
        {
            $result = $this->overrideManager->fetchImageOverride($instanceId, $key, $type);
            return new PageBuilderOverrideResult(
                type: $type,
                key: $key,
                instanceId: $instanceId,
                value: $result ?? [],
            );
        }
        else {
            $override = $this->overrideManager->fetchSimpleOverride($instanceId, $key, $type);
            return new PageBuilderOverrideResult(
                type: $type,
                key: $key,
                instanceId: $instanceId,
                value: ['value' => $override?->getFieldValue()],
            );
        }
    }
}