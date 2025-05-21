<?php

namespace JulianKoster\PageBuilderBundle\Service;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockInstanceRepository;

readonly class BlockInstanceResolver
{
    public function __construct(private PageBuilderBlockInstanceRepository $pageBuilderBlockInstanceRepository)
    {
    }

    /**
     * @param string $instanceId
     * @return PageBuilderBlockInstance
     */
    public function getBlockInstanceByInstanceId(string $instanceId): PageBuilderBlockInstance
    {
        return $this->pageBuilderBlockInstanceRepository->findOneBy(["instanceId" => $instanceId]);
    }
}