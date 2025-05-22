<?php

namespace JulianKoster\PageBuilderBundle\Twig\Components;

use JulianKoster\PageBuilderBundle\Dto\PageBuilderOverrideResult;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockOverrides;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockInstanceRepository;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockOverridesRepository;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockRepository;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderPageRepository;
use JulianKoster\PageBuilderBundle\Service\BlockInstanceResolver;
use JulianKoster\PageBuilderBundle\Service\BlockSchemaParser;
use JulianKoster\PageBuilderBundle\Service\KeySanitizer;
use JulianKoster\PageBuilderBundle\Service\PageBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Twig\Error\SyntaxError;

#[AsLiveComponent('MainBuilderComponent')]
final class MainBuilderComponent
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public ?int $page = null;

    #[LiveProp(writable: true)]
    public ?PageBuilderPage $builderPage = null;

    #[LiveProp(writable: true)]
    public array $overrides = []; // Block ID => override array

    #[LiveProp(writable: true)]
    public ?string $selectedBlockInstanceId = null;

    public function __construct(
        private readonly PageBuilderBlockRepository          $builderBlockRepository,
        private readonly PageBuilderPageRepository           $pageRepository,
        private readonly EntityManagerInterface              $entityManager,
        private readonly PageBuilderBlockRepository          $pageBuilderBlockRepository,
        private readonly PageBuilderService                  $pageBuilderService,
        private readonly PageBuilderBlockOverridesRepository $blockOverridesRepository,
        private readonly PageBuilderBlockInstanceRepository  $pageBuilderBlockInstanceRepository,
        private readonly PageBuilderBlockOverridesRepository $pageBuilderBlockOverridesRepository,
        private readonly LoggerInterface                     $logger,
        private readonly BlockInstanceResolver               $blockInstanceResolver,
        private readonly KeySanitizer                        $keySanitizer,
        private readonly BlockSchemaParser                   $blockSchemaParser,
    )
    {
    }

    public function blockIsOnPage(string $instanceId): bool
    {
        if($this->pageBuilderBlockInstanceRepository->findOneBy(['instanceId' => $instanceId, "pageBuilderPage" => $this->page]))
        {
            return true;
        }
        else {
            return false;
        }
    }

    #[PostMount]
    public function getBuilderPage(): void
    {
        $this->builderPage = $this->pageRepository->find($this->page);
        $this->getNormalizedBlockOrder();
    }

    public function getLayoutConfig(string $instanceId): array
    {
        return $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId)->getLayoutConfig() ?? [];
    }

    public function getLayoutClasses(string $instanceId): string
    {
        $classString = '';

        $layoutConfig = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId)->getLayoutConfig() ?? [];
        return implode(' ', array_map(
            fn($k, $v) => "$k-$v",
            array_keys($layoutConfig),
            $layoutConfig
        ));
    }

    #[LiveAction]
    public function updateLayoutConfig(#[LiveArg('instanceId')] string $instanceId, #[LiveArg('key')] string $key, #[LiveArg('value')] string $value): void
    {
        $layoutConfig = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId)->getLayoutConfig() ?? [];

        $layoutConfig[$key] = $value;

        $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);
        $blockInstance->setLayoutConfig($layoutConfig);
        $this->entityManager->persist($blockInstance);
        $this->entityManager->flush();
    }

    private function getSelectedBlockEntry(): ?PageBuilderBlockInstance
    {
        $blocksOnPage = $this->builderPage->getBlockInstance();

        if($blocksOnPage === null)
        {
            return null;
        }

        foreach ($blocksOnPage as $block) {
            if($block->getInstanceId() === $this->selectedBlockInstanceId)
            {
                return $block;
            }
        }
        return null;
    }

    public function getRenderableBlockInstances(): array
    {
        return array_filter(
            $this->getNormalizedBlockOrder()->toArray(),
            fn(PageBuilderBlockInstance $instance) =>
                $instance->getPageBuilderBlock()?->getId() !== null
                && $instance->getPageBuilderBlock()?->getTwigTemplatePath() !== null
        );
    }

    /**
     * @throws SyntaxError
     */
    public function getBlockSchema(?PageBuilderBlockInstance $blockInstance = null): array
    {
        $block = $blockInstance?->getPageBuilderBlock() ?? $this->getSelectedBlockEntry()?->getPageBuilderBlock();

        if (!$block || !$block->getId()) {
            return [];
        }

        return $this->getBlockDocumentSchema($block)
            ?: $this->getBlockTagSchema($block);
    }

    public function getAvailableBlocks(): array
    {
        return $this->builderBlockRepository->findAll();
    }

    #[LiveAction]
    public function selectBlockInstance(#[LiveArg('instanceId')] string $instanceId): void
    {
        $this->selectedBlockInstanceId = $instanceId;
    }

    #[LiveAction]
    public function addBlock(#[LiveArg('blockId')] ?int $blockId): void
    {
        $block = $this->builderBlockRepository->find($blockId);
        if (!$block) return;

        $this->builderPage = $this->pageRepository->find($this->page);

        $blockInstance = new PageBuilderBlockInstance();
        $blockInstance->setInstanceId(Uuid::v4()->toRfc4122());
        $blockInstance->setPageBuilderBlock($block);
        $blockInstance->setPosition(0);

        $this->entityManager->persist($blockInstance);

        $this->builderPage->addBlockInstance($blockInstance);

        $this->entityManager->persist($this->builderPage);
        $this->entityManager->flush();

        $this->selectedBlockInstanceId = $blockInstance->getId();
    }

    public function getNormalizedBlockOrder(): \Doctrine\Common\Collections\Collection
    {
        $this->builderPage = $this->pageRepository->find($this->page);
        return $this->builderPage->getBlockInstance();
    }

    #[LiveAction]
    public function removeBrokenBlock(#[LiveArg] string $instanceId): void
    {
        $blockInstance = $this->pageBuilderBlockInstanceRepository->findOneBy(["instanceId" => $instanceId]);

        $this->entityManager->remove($blockInstance);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function updateOverride(#[LiveArg('key')] string $key, #[LiveArg('value')] string $value, #[LiveArg('instanceId')] string $instanceId, #[LiveArg('type')] string $type): void
    {
        $blockInstance = $this->pageBuilderBlockInstanceRepository->findOneBy(["instanceId" => $instanceId]);
        $override = $this->pageBuilderBlockOverridesRepository->findOneBy(["instanceId" => $instanceId, "pageBuilderBlockInstance" => $blockInstance, "fieldKey" => $key, "type" => $type]);

        if($override)
        {
            if($override->getFieldKey() == $key)
            {
                $override->setFieldValue($value);
            }
        }
        else {
            $newOverride = new PageBuilderBlockOverrides();
            $newOverride->setType($type);
            $newOverride->setInstanceId($instanceId);
            $newOverride->setPageBuilderBlockInstance($blockInstance);
            $newOverride->setFieldKey($key);
            $newOverride->setFieldValue($value);
            $this->entityManager->persist($newOverride);
        }

        $this->entityManager->flush();

        $this->getNormalizedBlockOrder();
    }


    /**
     * @param string $key
     * @param string $value
     * @param string $instanceId
     * @param string $type
     * @return void
     */
    #[LiveAction]
    public function replaceOverride(#[LiveArg('key')] string $key, #[LiveArg('value')] string $value, #[LiveArg('instanceId')] string $instanceId, #[LiveArg('type')] string $type): void
    {
        $baseKey = $this->keySanitizer->extractBaseKeyFromKey($key);
        if($baseKey === null || $baseKey === "")
        {
            $this->logger->error("
            Tried to replace an override for the following block instance: {instanceId}, 
            attempting to replace value: {value} of key {key}. The base-key of {key} could not be extracted. 
            It does not exist. Check your Twig block configs, does every custom pb_block_config tag have a unique name, does not contain special chars that could potentially cause errors?
            ", [
                "instanceId" => $instanceId,
                "key" => $baseKey,
                "value" => $value,
            ]);
            return;
        }

        $blockInstance = $this->pageBuilderBlockInstanceRepository->findOneBy([
            "instanceId" => $instanceId,
        ]);

        # Produces an array of existing keys in the database that we must remove, as we're running a replace operation.
        $existingKeys = $this->pageBuilderBlockOverridesRepository->getExistingOverridesToDelete($blockInstance, $type, $baseKey, $key);

        if(!empty($existingKeys))
        {
            foreach($existingKeys as $existingKey)
            {
                $this->entityManager->remove($existingKey);
            }
        }

        $override = $this->pageBuilderBlockOverridesRepository->findOneBy([
            "instanceId" => $instanceId,
            "pageBuilderBlockInstance" => $blockInstance,
            "fieldKey" => $key,
            "type" => $type
        ]);

        if($override)
        {
            if($override->getFieldKey() == $key)
            {
                $override->setFieldValue($value);
            }
        }

        if (!$override) {
            $newOverride = new PageBuilderBlockOverrides();
            $newOverride->setType($type);
            $newOverride->setInstanceId($instanceId);
            $newOverride->setPageBuilderBlockInstance($blockInstance);
            $newOverride->setFieldKey($key);
            $newOverride->setFieldValue($value);
            $this->entityManager->persist($newOverride);
        }

        $this->entityManager->flush();

        $this->getNormalizedBlockOrder();
    }

    #[LiveAction]
    public function updateNestedOverride(
        #[LiveArg] int $index,
        #[LiveArg] string $key,
        #[LiveArg] int $itemIndex,
        #[LiveArg] string $itemKey,
        #[LiveArg] string $value
    ): void {
        $this->builderPage = $this->pageRepository->find($this->page);
        $order = $this->builderPage->getBlockOrder() ?? [];

        $order[$index]['overrides'][$key][$itemIndex][$itemKey] = $value;
        $this->builderPage->setBlockOrder($order);

        $this->entityManager->persist($this->builderPage);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function addListItem(
        #[LiveArg] int $index,
        #[LiveArg] string $key
    ): void {
        $this->builderPage = $this->pageRepository->find($this->page);
        $order = $this->builderPage->getBlockOrder() ?? [];
        $order[$index]['overrides'][$key][] = []; // add empty object
        $this->builderPage->setBlockOrder($order);

        $this->entityManager->persist($this->builderPage);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function deleteBlockFromPage(
        #[LiveArg('instanceId')] string $instanceId
    ): void
    {
        $blockInstance = $this->blockInstanceResolver->getBlockInstanceByInstanceId($instanceId);
        $this->builderPage = $this->pageRepository->find($this->page);

        $this->builderPage->removeBlockInstance($blockInstance);

        $this->entityManager->persist($this->builderPage);

        $this->entityManager->remove($blockInstance);
        $this->entityManager->flush();
    }

    #[LiveAction]
    public function save(): void
    {
        $this->entityManager->persist($this->builderPage);
        $this->entityManager->flush();
    }

    /**
     * @throws SyntaxError
     */
    public function getBlockTagSchema(?PageBuilderBlock $block = null): array
    {
        return $this->blockSchemaParser->parseTwigTags($block);
    }

    public function getBlockDocumentSchema(?PageBuilderBlock $block = null): array
    {
        if ($block->getPhpClass() && class_exists($block->getPhpClass())) {
            return call_user_func([$block->getPhpClass(), 'getSchema']);
        }
        return [];
    }

    public function getBlockOverrides(string $instanceId, ?string $key, ?string $type): PageBuilderOverrideResult
    {
        return $this->pageBuilderService->getOverrideValues($instanceId, $key, $type);
    }
}
