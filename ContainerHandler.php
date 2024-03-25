<?php

namespace mortenson\PsalmPluginDrupal;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\String_;
use Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
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
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        if ($event->getMethodId() != 'Drupal::service') {
            return;
        }

        if (!self::$containerMeta) {
            return;
        }

        $expr = $event->getExpr();
        if ($expr->args[0]->value instanceof String_) {
            $serviceId = $expr->args[0]->value->value;
        } elseif ($expr->args[0]->value instanceof ClassConstFetch) {
            $serviceId = (string) $expr->args[0]->value->class->getAttribute('resolvedName');
        } else {
            return;
        }

        $service = self::$containerMeta->get($serviceId);
        if ($service) {
            $class = $service->getClass();
            if ($class) {
                $event->getCodebase()->classlikes->addFullyQualifiedClassName($class);
                $event->setReturnTypeCandidate(new Union([new TNamedObject($class)]));
            }
        }
    }
}
