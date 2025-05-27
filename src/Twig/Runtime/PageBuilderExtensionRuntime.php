<?php

namespace JulianKoster\PageBuilderBundle\Twig\Runtime;

use JulianKoster\PageBuilderBundle\Service\FieldContextBuilder;
use JulianKoster\PageBuilderBundle\Service\PageBuilderService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\RuntimeExtensionInterface;

readonly class PageBuilderExtensionRuntime implements RuntimeExtensionInterface
{
    private ExpressionLanguage $expressionLanguage;
    private string $publicUploadDir;

    public function __construct(
        private LoggerInterface                                  $logger,
        private PageBuilderService                               $pageBuilderService,
        private Environment                                      $twig,
        private FieldContextBuilder                              $fieldContextBuilder,
        #[Autowire(param: 'public_upload_download_dir') ] string $publicUploadDir,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
        $this->expressionLanguage = new ExpressionLanguage();
        $this->publicUploadDir = $publicUploadDir;
    }

    public function configureBlockSetting(
        array $context,
        string $name = null,
        ?string $type = 'string',
        mixed $fallback = null
    ): mixed {
        $instanceId = $context["instanceId"] ?? null;
        if ($instanceId === null) {
            throw new RuntimeError('Missing "instanceId" parameter.');
        }

        if($fallback) {
            $this->validateFallback($fallback);
        }

        $overrideResult = $this->pageBuilderService->getOverrideValues($instanceId, $name, $type);

        if ($overrideResult === null || empty($overrideResult->value)) {
            return $fallback;
        }

        if ($overrideResult->type === 'link') {
            $path = $overrideResult->get('path') ?? null;
            $url = $overrideResult->get('url') ?? null;

            if($path)
            {
                return $this->urlGenerator->generate($path);
            }
            else
            {
                return $url;
            }
        }

        if ($overrideResult->type === 'image') {
            return $this->publicUploadDir . $overrideResult->get('image');
        }

        if ($overrideResult->type === 'conditional') {
            $test = $overrideResult->get('test') ?? $fallback['test'] ?? null;
            $true = $overrideResult->get('true') ?? $fallback['true'] ?? null;
            $false = $overrideResult->get('false') ?? $fallback['false'] ?? null;

            // Evaluate the test
            $contextVars = $context; // or subset
            $result = $this->evaluateTest($test, $contextVars);

            return $result ? $true : $false;
        }

        return $overrideResult->get('value') ?? $fallback;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function renderBlockInputs(string $key, array $config, mixed $value, string $instanceId): string
    {
        $template = "@PageBuilder/field_types/{$config['type']}.html.twig";
        $fieldContext = $this->fieldContextBuilder->getFieldContext($key, $config["type"], $instanceId) ?? null;

        return $this->twig->render($template, [
            'key' => $key,
            'value' => $value,
            'config' => $config,
            'instanceId' => $instanceId,
            'context' => $fieldContext ?? null,
        ]);
    }

    private function evaluateTest(?string $test, array $contextVars): bool
    {
        if (!$test) {
            return false;
        }

        try {
            return (bool) $this->expressionLanguage->evaluate($test, $contextVars);
        } catch (\Throwable $e) {
            $this->logger->warning('Failed to evaluate expression: ' . $test, [
                'error' => $e->getMessage(),
                'vars' => $contextVars,
            ]);
            return false;
        }
    }

    private function validateFallback(mixed $fallback): void
    {
        if (!is_string($fallback)) {
            throw new \InvalidArgumentException('Fallback value must be a string.');
        }

        if (mb_strlen($fallback) > 500) {
            throw new \InvalidArgumentException('Fallback is too long. Maximum length is 500 characters.');
        }

        if (!mb_check_encoding($fallback, 'UTF-8')) {
            throw new \InvalidArgumentException('Fallback string is not valid UTF-8.');
        }
    }

    public function sentenceCase(string $string): string
    {
        return ucfirst(mb_strtolower($string));
    }
}
