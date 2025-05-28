<?php

namespace JulianKoster\PageBuilderBundle\Service;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockOverrides;
use JulianKoster\PageBuilderBundle\Enum\PageBuilderFieldTypes;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockOverridesRepository;
use Psr\Log\LoggerInterface;

readonly class OverrideManager
{
    public function __construct(
        private PageBuilderBlockOverridesRepository $pageBuilderBlockOverridesRepository,
        private LoggerInterface                     $logger,
        private BlockInstanceResolver               $blockInstanceResolver,
    )
    {
    }

    /**
     * @param string $instanceId the instance's id.
     * @param string|null $key it's identifier at the block level, e.g. "backgroundClasses" or "conditional[true]".
     * @param string|null $type the string representation of the override type, e.g.: "conditional" or "string".
     * @return PageBuilderBlockOverrides|null
     */
    public function fetchSimpleOverride(string $instanceId, ?string $key, ?string $type): PageBuilderBlockOverrides|null
    {
        if (PageBuilderFieldTypes::isSimpleType($type)) {
            $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);

            $completeKey = $key . "[" . $type . "]";

            return $this->pageBuilderBlockOverridesRepository->findOneBy(["instanceId" => $instanceId, "pageBuilderBlockInstance" => $blockInstance, "fieldKey" => $completeKey]);
        } else {
            $this->logger->warning(
                "Unsupported block type: {type}. The supported (simple)block types are: {fieldTypes}.",
                ["type" => $type, 'fieldTypes' => PageBuilderFieldTypes::getSimpleTypes()]
            );
            return null;
        }
    }

    /**
     * @param string $instanceId the instance's id.
     * @param string|null $key it's identifier at the block level, e.g. "backgroundClasses" or "conditional[true]".
     * @param string|null $type the string representation of the override type, e.g.: "conditional" or "string".
     * @return PageBuilderBlockOverrides|null
     */
    public function fetchListOverride(string $instanceId, ?string $key, ?string $type): PageBuilderBlockOverrides|null
    {
        if (PageBuilderFieldTypes::isListType($type)) {
            $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);

            $completeKey = $key . "[" . $type . "]";

            return $this->pageBuilderBlockOverridesRepository->findOneBy(["instanceId" => $instanceId, "pageBuilderBlockInstance" => $blockInstance, "fieldKey" => $completeKey]);
        } else {
            $this->logger->warning(
                "Unsupported block type: {type}. The supported (simple)block types are: {fieldTypes}.",
                ["type" => $type, 'fieldTypes' => PageBuilderFieldTypes::getSimpleTypes()]
            );
            return null;
        }
    }

    /**
     * Returns an array of overrides for a conditional PageBuilder block.
     * @param string $instanceId the instance's id.
     * @param string|null $key it's identifier at the block level, e.g. "backgroundClasses" or "conditional[true]".
     * @param string|null $type the string representation of the override type, e.g.: "conditional" or "string".
     * @return array|null the array contains four keys: (test, true and false) as well as their override values.
     */
    public function fetchConditionalOverride(string $instanceId, ?string $key, ?string $type): array|null
    {
        if (PageBuilderFieldTypes::isConditionalType($type)) {
            $resultArray = [];

            $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);

            # The conditional keys defined in the template usually consist of (at least) a test and a true. They may also contain a false value.
            # Rather than returning three overrides (one for test, one for true, and possibly one for false), we return a structured array containing the overrides (if any).
            $conditionalKeyArray = ["test", "true", "false"];

            foreach ($conditionalKeyArray as $conditionalKey) {

                $completeKey = $key . "[" . $conditionalKey . "]";

                $override = $this->pageBuilderBlockOverridesRepository->findOneBy(["instanceId" => $instanceId, "pageBuilderBlockInstance" => $blockInstance, "fieldKey" => $completeKey]);
                if ($override) {
                    $resultArray[$conditionalKey] = $override->getFieldValue();
                }
            }

            return $resultArray;
        } else {
            $this->logger->warning(
                "Unsupported block type for a conditional: {type}. Supported types: {fieldTypes}.",
                ["type" => $type, "fieldTypes" => PageBuilderFieldTypes::getConditionalTypes()]
            );
            return null;
        }
    }

    /**
     * Returns an array of overrides for an image-type PageBuilder block.
     * @param string $instanceId the instance's id.
     * @param string|null $key it's identifier at the block level, e.g. "backgroundClasses" or "conditional[true]".
     * @param string|null $type the string representation of the override type, e.g.: "conditional" or "string".
     * @return array|null the array contains three keys: (urlSrc, uploadSrc and imgAlt) as well as their override values.
     */
    public function fetchImageOverride(string $instanceId, ?string $key, ?string $type): array|null
    {
        if (PageBuilderFieldTypes::isImageType($type)) {
            $resultArray = [];

            $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);

            # The image keys defined in the template usually consist of (at least) a src and an imgAlt.
            # Rather than returning two overrides (one for src, one for imgAlt), we return a structured array containing the overrides (if any).
            $imageKeyArray = ["image"];

            foreach ($imageKeyArray as $imageKey) {

                $completeKey = $key . "[" . $imageKey . "]";

                $override = $this->pageBuilderBlockOverridesRepository->findOneBy(["instanceId" => $instanceId, "pageBuilderBlockInstance" => $blockInstance, "fieldKey" => $completeKey]);
                if ($override) {
                    $resultArray[$imageKey] = $override->getFieldValue();
                }
            }

            return $resultArray;
        } else {
            $this->logger->warning(
                "Unsupported block type for an image: {type}. Supported types: {fieldTypes}.",
                ["type" => $type, "fieldTypes" => PageBuilderFieldTypes::getImageTypes()]
            );
            return null;
        }
    }

    /**
     * @param string $instanceId the instance's id.
     * @param string|null $key it's identifier at the block level, e.g. "backgroundClasses" or "conditional[true]".
     * @param string|null $type the string representation of the override type, e.g.: "conditional" or "string".
     * @return PageBuilderBlockOverrides|null
     */
    public function fetchLinkOverride(string $instanceId, ?string $key, ?string $type): array|null
    {
        if (PageBuilderFieldTypes::isLinkType($type)) {
            $resultArray = [];

            $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);

            $linkKeyArray = ["url", "path"];

            foreach ($linkKeyArray as $linkKey) {

                $completeKey = $key . "[" . $linkKey . "][link]";

                $override = $this->pageBuilderBlockOverridesRepository->findOneBy(["instanceId" => $instanceId, "pageBuilderBlockInstance" => $blockInstance, "fieldKey" => $completeKey]);
                if ($override) {
                    $resultArray[$linkKey] = $override->getFieldValue();
                }
            }

            return $resultArray;
        } else {
            $this->logger->warning(
                "Unsupported block type: {type}. The supported (link)block types are: {fieldTypes}.",
                ["type" => $type, 'fieldTypes' => PageBuilderFieldTypes::getLinkTypes()]
            );
            return null;
        }
    }
}