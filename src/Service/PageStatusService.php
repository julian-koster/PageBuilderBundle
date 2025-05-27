<?php

namespace JulianKoster\PageBuilderBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Enum\PageBuilderPageStatus;

readonly class PageStatusService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function setStatus(PageBuilderPage $page, string $status): void
    {
        if($this->isStatusValid($status)) {
            $page->setStatus($status);
            $this->entityManager->flush();
        }
    }

    private function isStatusValid(string $status): bool
    {
        /** @var PageBuilderPageStatus $allowedStatuses */
        $allowedStatuses = PageBuilderPageStatus::cases();
        foreach($allowedStatuses as $allowedStatus) {
            if($allowedStatus->value == $status) {
                return true;
            }
        }

        return false;
    }
}