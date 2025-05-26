<?php

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockCategory;
use PHPUnit\Framework\TestCase;

class PageBuilderBlockCategoryTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $category = new PageBuilderBlockCategory();
        $this->assertNull($category->getId());

        $category->setName('Test Category');
        $this->assertSame('Test Category', $category->getName());
    }

    public function testAddAndRemovePageBuilderBlock(): void
    {
        $category = new PageBuilderBlockCategory();
        $block = $this->createMock(PageBuilderBlock::class);

        // Expect addCategory and removeCategory to be called
        $block->expects($this->once())->method('addCategory')->with($category);
        $block->expects($this->once())->method('removeCategory')->with($category);

        $category->addPageBuilderBlock($block);
        $this->assertCount(1, $category->getPageBuilderBlocks());

        $category->removePageBuilderBlock($block);
        $this->assertCount(0, $category->getPageBuilderBlocks());
    }
}