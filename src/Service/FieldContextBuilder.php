<?php

namespace JulianKoster\PageBuilderBundle\Service;

use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockOverridesRepository;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;

/**
 * Builds the context for a field. E.g. the link-type contains multiple fields (url, email, etc.) - although it is stored as a single value in the database.
 * Passing extra context to certain types of fields helps to render them properly in the Builder Preview.
 */
readonly class FieldContextBuilder
{
    public function __construct(
        private BlockInstanceResolver $blockInstanceResolver,
        private LoggerInterface $logger,
        private PageBuilderBlockOverridesRepository $pageBuilderBlockOverridesRepository,
    )
    {
    }

    /**
     * @param string $key
     * @param string $type
     * @param string $instanceId
     * @return array
     */
    public function getFieldContext(string $key, string $type, string $instanceId): array
    {
        if ($type === 'link' || $type === 'image') {
            return [
                'instanceId' => $instanceId,
                'key' => $key,
                'type' => 'link',
                'context' => $this->getActiveLinkVarsByKey($instanceId, $key),
            ];
        }

        // Default for other types
        return [];
    }

    /**
     * @param string $instanceId
     * @param string $key
     * @return array
     * @throws NonUniqueResultException
     */
    private function getActiveLinkVarsByKey(string $instanceId, string $key): array
    {
        $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);

        $activeKey = $this->pageBuilderBlockOverridesRepository->getExistingOverridesByBaseKey($blockInstance, "link", $key);

        if ($activeKey === null) {
            $this->logger->warning('
                    Attempted to retrieve the active link values for: {key}, from block instance: {instanceId} but it could not be found.
                    Verify that the key you defined in your template config block (pb_block_config()) exists and contains a "link" type key.
                ', [
                'key' => $key,
                'instanceId' => $instanceId,
            ]);
            return [];
        }

        return ["activeKey" => $activeKey->getFieldKey(), "activeValue" => $activeKey->getFieldValue()];
    }
}