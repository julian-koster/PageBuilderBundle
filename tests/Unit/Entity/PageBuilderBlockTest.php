<?php

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockCategory;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class PageBuilderBlockTest extends TestCase
{
    public function testCanGetAndSetData(): void
    {
        $now = new \DateTimeImmutable();

        $block = new PageBuilderBlock();
        $block->setName("Testblock 1");
        $block->setAddedAt($now);
        $block->setTwigTemplatePath("blocks/test_block_template.html.twig");
        $block->setScreenshot("testblock-screenshot.jpg");

        self::assertEquals("Testblock 1", $block->getName());
        self::assertEquals($now->format('Y-m-d H:i:s'), $block->getAddedAt()->format('Y-m-d H:i:s'));
        self::assertEquals("testblock-screenshot.jpg", $block->getScreenshot());
        self::assertEquals("blocks/test_block_template.html.twig", $block->getTwigTemplatePath());

        $blockCategory = new PageBuilderBlockCategory();
        $blockCategory->setName("Testblock Category");

        $block->addCategory($blockCategory);
        self::assertContains($blockCategory, $block->getCategory());

        $instanceId = Uuid::v4()->toRfc4122();

        self::assertNotNull($instanceId);

        $layoutConfig = unserialize('a:1:{s:2:"pl";s:1:"4";}');

        self::assertIsArray($layoutConfig);

        $blockInstance = new PageBuilderBlockInstance();
        $blockInstance->setInstanceId($instanceId);
        $blockInstance->setPosition(1);
        $blockInstance->setLayoutConfig($layoutConfig);

        $block->addBlockInstance($blockInstance);

        self::assertContains($blockInstance,$block->getBlockInstance());
    }
}