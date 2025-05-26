<?php

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockOverrides;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use PHPUnit\Framework\TestCase;

class PageBuilderBlockInstanceTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $instance = new PageBuilderBlockInstance();

        $this->assertNull($instance->getId());

        $instance->setInstanceId('a87224633222a3e');
        $this->assertSame('a87224633222a3e', $instance->getInstanceId());

        $instance->setPosition(5);
        $this->assertSame(5, $instance->getPosition());

        $page = $this->createMock(PageBuilderPage::class);
        $instance->setPageBuilderPage($page);
        $this->assertSame($page, $instance->getPageBuilderPage());

        $block = $this->createMock(PageBuilderBlock::class);
        $instance->setPageBuilderBlock($block);
        $this->assertSame($block, $instance->getPageBuilderBlock());

        $layout = ['col' => 6, 'align' => 'center'];
        $instance->setLayoutConfig($layout);
        $this->assertSame($layout, $instance->getLayoutConfig());
    }

    public function testAddOverrides(): void
    {
        $instance = new PageBuilderBlockInstance();
        $override = $this->createMock(PageBuilderBlockOverrides::class);

        $override->expects($this->once())->method('setPageBuilderBlockInstance')->with($instance);
        $override->method('getPageBuilderBlockInstance')->willReturn($instance);

        $instance->addOverride($override);
        $this->assertContains($override, $instance->getOverrides());
    }
}