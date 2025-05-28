<?php

namespace Service;

use JulianKoster\PageBuilderBundle\Service\BlockSchemaExtractor;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Source;

class BlockSchemaExtractorTest extends TestCase
{
    public function testExtractsBasicSchema(): void
    {
        $twig = new Environment(new ArrayLoader());
        $twig->addExtension(new \JulianKoster\PageBuilderBundle\Twig\Extension\PageBuilderExtension());
        $extractor = new BlockSchemaExtractor($twig);

        $template = <<<TWIG
            {{ pb_block_config('headline', type='string', fallback='Welcome') }}
        TWIG;

        $source = new Source($template, 'test_template');

        $result = $extractor->extract($source);

        $this->assertCount(1, $result);
        $this->assertSame([
            'key' => 'headline',
            'type' => 'string',
            'fallback' => [
                'type' => 'string',
                'value' => 'Welcome',
            ],
            'label' => 'Headline',
        ], $result[0]);
    }

    public function testExtractsWithConditionalFallback(): void
    {
        $twig = new Environment(new ArrayLoader());
        $twig->addExtension(new \JulianKoster\PageBuilderBundle\Twig\Extension\PageBuilderExtension());
        $extractor = new BlockSchemaExtractor($twig);

        $template = <<<TWIG
            {{ pb_block_config('cta_text', type='string', fallback=isLoggedIn ? 'Logout' : 'Login') }}
        TWIG;

        $source = new Source($template, 'test_template');

        $result = $extractor->extract($source);

        $this->assertCount(1, $result);
        $this->assertSame('cta_text', $result[0]['key']);
        $this->assertSame('string', $result[0]['type']);
        $this->assertSame('conditional', $result[0]['fallback']['type']);
        $this->assertSame('isLoggedIn', $result[0]['fallback']['test']);
        $this->assertSame('Logout', $result[0]['fallback']['true']);
        $this->assertSame('Login', $result[0]['fallback']['false']);
    }
}