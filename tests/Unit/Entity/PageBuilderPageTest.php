<?php

use JulianKoster\PageBuilderBundle\Entity\PageBuilderPage;
use PHPUnit\Framework\TestCase;

class PageBuilderPageTest extends TestCase
{
    public function testCanGetAndSetData(): void
    {
        $page = new PageBuilderPage();
        $page->setTitle('Homepage');
        $page->setName('This is our company homepage, awesome!');
        $page->setLocale('en');

        self::assertEquals('Homepage', $page->getTitle());
        self::assertEquals('This is our company homepage, awesome!', $page->getName());
        self::assertEquals('en', $page->getLocale());
    }
}