<?php

namespace mortenson\PslamPluginDrupal;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\Plugin\Hook\AfterMethodCallAnalysisInterface;
use Psalm\StatementsSource;
use Psalm\SymfonyPsalmPlugin\Symfony\ContainerMeta;
use Psalm\Type\Atomic\TNamedObject;
use Psalm\Type\Union;

// Copies Psalm\SymfonyPsalmPlugin\Handler\ContainerHandler to support Drupal::service.
class ContainerHandler implements AfterMethodCallAnalysisInterface
{
    /**
     * @var ContainerMeta|null
     */
    private static $containerMeta;

    public static function init(ContainerMeta $containerMeta): void
    {
        self::$containerMeta = $containerMeta;
    }

    /**
     * {@inheritdoc}
     */
    public static function afterMethodCallAnalysis(
        Expr $expr,
        string $method_id,
        string $appearing_method_id,
        string $declaring_method_id,
        Context $context,
        StatementsSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = [],
        Union &$return_type_candidate = null
    ): void {
        if (!in_array($declaring_method_id, ['Drupal::service'], true)) {
            return;
        }

        if (!self::$containerMeta) {
            return;
        }

        if ($expr->args[0]->value instanceof String_) {
            $serviceId = $expr->args[0]->value->value;
        } elseif ($expr->args[0]->value instanceof ClassConstFetch) {
            $serviceId = (string) $expr->args[0]->value->class->getAttribute('resolvedName');
        } else {
            return;
        }

        $service = self::$containerMeta->get($serviceId);
        if ($service) {
            $class = $service->getClassName();
            if ($class) {
                $codebase->classlikes->addFullyQualifiedClassName($class);
                $return_type_candidate = new Union([new TNamedObject($class)]);
            }
        }
    }
}
