<?php

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockOverrides;
use PHPUnit\Framework\TestCase;

class PageBuilderBlockOverridesTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $override = new PageBuilderBlockOverrides();

        $this->assertNull($override->getId());

        $override->setInstanceId('abc-456');
        $this->assertSame('abc-456', $override->getInstanceId());

        $override->setFieldKey('backgroundColor');
        $this->assertSame('backgroundColor', $override->getFieldKey());

        $override->setFieldValue('#fff');
        $this->assertSame('#fff', $override->getFieldValue());

        $override->setFieldValue(null);
        $this->assertNull($override->getFieldValue());

        $override->setType('string');
        $this->assertSame('string', $override->getType());

        $instance = $this->createMock(PageBuilderBlockInstance::class);
        $override->setPageBuilderBlockInstance($instance);
        $this->assertSame($instance, $override->getPageBuilderBlockInstance());

        $override->setPageBuilderBlockInstance(null);
        $this->assertNull($override->getPageBuilderBlockInstance());
    }
}