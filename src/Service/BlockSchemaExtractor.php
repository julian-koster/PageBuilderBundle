<?php

namespace JulianKoster\PageBuilderBundle\Service;

use Twig\Environment;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\Ternary\ConditionalTernary;
use Twig\Source;
use Twig\Node\Node;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Expression\ConstantExpression;

readonly class BlockSchemaExtractor
{
    public function __construct(private Environment $twig)
    {
    }

    /**
     * @throws SyntaxError
     */
    public function extract(Source $source): array
    {
        $tokens = $this->twig->tokenize($source);
        $parsed = $this->twig->parse($tokens);
        $twig = $this->twig;

        $schema = [];

        $walker = function (Node $node) use (&$walker, &$schema, $twig) {
            if ($node instanceof FunctionExpression && $node->getAttribute('name') === 'pb_block_config') {
                $args = $node->getNode('arguments');

                $key = null;
                $type = 'string';
                $fallback = null;

                # Positional arg
                if ($args->hasNode(0)) {
                    $keyNode = $args->getNode(0);
                    if ($keyNode instanceof ConstantExpression) {
                        $key = $keyNode->getAttribute('value');
                    }
                }

                # Named args (string keys)
                if ($args->hasNode('type')) {
                    $typeNode = $args->getNode('type');
                    if ($typeNode instanceof ConstantExpression) {
                        $type = $typeNode->getAttribute('value');
                    }
                }

                if ($args->hasNode('fallback')) {
                    $fallbackWrapper = $args->getNode('fallback');

                    # fallback: new ConditionalTernary(...) or ConstantExpression
                    $fallbackNode = $fallbackWrapper instanceof Node && $fallbackWrapper->hasNode(1)
                        ? $fallbackWrapper->getNode(1) # inside the pair fallback => node
                        : $fallbackWrapper;

                    $fallback = $this->getStructuredFallback($fallbackNode);
                }

                if ($key) {
                    $schema[$key] = [
                        'key' => $key,
                        'type' => $type,
                        'fallback' => $fallback,
                        'label' => ucwords(str_replace(['_', '-'], ' ', $key)),
                    ];
                }
            }

            foreach ($node as $child) {
                if ($child instanceof Node) {
                    $walker($child);
                }
            }
        };

        $walker($parsed);

        return array_values($schema);
    }

    private function getStructuredFallback(Node $node): array
    {
        if ($node instanceof ConstantExpression) {
            return [
                'type' => 'string',
                'value' => $node->getAttribute('value'),
            ];
        }

       # condition ? true : false
        if ($node instanceof ConditionalTernary) {
            return [
                'type' => 'conditional',
                'test' => $this->extractReadableTest($this->twig->compile($node->getNode('test'))),
                'true' => $this->extractNodeValue($node->getNode('left')),
                'false' => $this->extractNodeValue($node->getNode('right')),
            ];
        }

        # Classic if/else in Twig
        if ($node instanceof \Twig\Node\Expression\ConditionalExpression) {
            return [
                'type' => 'conditional',
                'test' => $this->twig->compile($node->getNode('expr1')), // actual test
                'true' => $this->twig->compile($node->getNode('expr2')),
                'false' => $this->twig->compile($node->getNode('expr3')),
            ];
        }

        # Fallback to raw compiled expression
        return [
            'type' => 'expression',
            'value' => $this->twig->compile($node),
        ];
    }

    private function extractReadableTest(string $compiled): string
    {
        if (preg_match('/\$context\["([^"]+)"\]/', $compiled, $m)) {
            return $m[1]; # e.g., "isTrue"
        }

        return $compiled; # TODO: currently fallback to full PHP but I don't want that.
    }

    private function extractNodeValue(Node $node): string
    {
        if ($node instanceof ConstantExpression) {
            return $node->getAttribute('value');
        }

        $compiled = $this->twig->compile($node);

        # Strip quotes if it's a quoted string
        if (preg_match('/^"(.*)"$/s', $compiled, $m)) {
            return $m[1];
        }

        return $compiled;
    }
}