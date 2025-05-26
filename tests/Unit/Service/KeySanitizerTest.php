<?php

namespace Service;

use JulianKoster\PageBuilderBundle\Service\KeySanitizer;
use PHPUnit\Framework\TestCase;

class KeySanitizerTest extends TestCase
{
    public function testExtractBaseKeyFromKey(): void
    {
        $sanitizer = new KeySanitizer();

        $this->assertSame('HeroBtnUrl', $sanitizer->extractBaseKeyFromKey('HeroBtnUrl[url]'));
        $this->assertSame('ContactLink', $sanitizer->extractBaseKeyFromKey('ContactLink[path]'));
        $this->assertSame('SimpleKey', $sanitizer->extractBaseKeyFromKey('SimpleKey'));
        $this->assertSame('NestedKey', $sanitizer->extractBaseKeyFromKey('NestedKey[0][url]'));
        $this->assertSame('', $sanitizer->extractBaseKeyFromKey('[orphan]'));
    }
}