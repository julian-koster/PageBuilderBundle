<?php

namespace Service;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlockInstance;
use JulianKoster\PageBuilderBundle\Repository\PageBuilderBlockInstanceRepository;
use JulianKoster\PageBuilderBundle\Service\BlockInstanceResolver;
use PHPUnit\Framework\TestCase;

class BlockInstanceResolverTest extends TestCase
{
    public function testGetBlockInstanceByInstanceId(): void
    {
        $mockInstance = new PageBuilderBlockInstance();

        $repository = $this->createMock(PageBuilderBlockInstanceRepository::class);
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['instanceId' => 'abc-123'])
            ->willReturn($mockInstance);

        $resolver = new BlockInstanceResolver($repository);
        $result = $resolver->getBlockInstanceByInstanceId('abc-123');

        $this->assertSame($mockInstance, $result);
    }
}