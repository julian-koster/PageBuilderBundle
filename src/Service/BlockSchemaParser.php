<?php

namespace JulianKoster\PageBuilderBundle\Service;

use JulianKoster\PageBuilderBundle\Entity\PageBuilderBlock;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Twig\Error\SyntaxError;
use Twig\Source;

readonly class BlockSchemaParser
{
    public function __construct(
        private BlockSchemaExtractor  $blockSchemaExtractor,
        private ContainerBagInterface $containerBag,
    )
    {
    }

    /**
     * Locates a Twig template file, reads it's contents and passes it to a block schema extractor function that extracts the required pb_block_config nodes from the contents.
     * @param PageBuilderBlock $pageBuilderBlock
     * @return array an array with either: a simple string (type,value) a conditional with (type,test,true,false) or a raw block schema (type,raw).
     * @throws SyntaxError
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function parseTwigTags(PageBuilderBlock $pageBuilderBlock): array
    {
        $templateDir = null;

        if($this->containerBag->has('page_builder.template_dir')) {
            $templateDir = $this->containerBag->get('page_builder.template_dir');
        }
        else {
            throw new \Exception('Missing template dir parameter: page_builder.template_dir');
        }

        $twigTemplate = $pageBuilderBlock->getTwigTemplatePath();

        if (!$twigTemplate) {
            throw new \InvalidArgumentException('No Twig template path set for block "' . $pageBuilderBlock->getName() . '".');
        }

        $filePath = $templateDir . DIRECTORY_SEPARATOR . $twigTemplate;

        $filesystem = new Filesystem();
        if(!$filesystem->exists($filePath)) {
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