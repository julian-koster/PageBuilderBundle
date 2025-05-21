<?php

namespace JulianKoster\PageBuilderBundle\Service;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Twig\Error\SyntaxError;
use Twig\Source;

class BlockSchemaParser
{
    private string $projectDir;

    public function __construct(
        private BlockSchemaExtractor $blockSchemaExtractor,
    )
    {
    }

    /**
     * Locates a Twig template file, reads it's contents and passes it to a block schema extractor function that extracts the required pb_block_config nodes from the contents.
     * @param PageBuilderBlock $pageBuilderBlock
     * @return array an array with either: a simple string (type,value) a conditional with (type,test,true,false) or a raw block schema (type,raw).
     * @throws SyntaxError
     */
    public function parseTwigTags(PageBuilderBlock $pageBuilderBlock): array
    {
        $twigTemplate = $pageBuilderBlock->getTwigTemplatePath();

        $filePath = $this->projectDir . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'page_builder' . DIRECTORY_SEPARATOR . $twigTemplate;

        $filesystem = new Filesystem();
        if(!$filesystem->exists($filePath))
        {
            throw new FileNotFoundException($filePath);
        }

        $source = new Source(file_get_contents($filePath), $filePath);

        # Get all the pb_block_config tags from the Twig template, as well as their types and fallbacks (if any).
        try {
            $extractedContents = $this->blockSchemaExtractor->extract($source);
        } catch (SyntaxError $e) {
            throw new SyntaxError("Could not parse the Twig template. With the following error: " . $e->getMessage());
        }

        return $extractedContents;
    }
}