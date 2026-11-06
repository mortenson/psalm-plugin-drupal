<?php

namespace mortenson\PsalmPluginDrupal;

use Psalm\Plugin\EventHandler\Event\AddRemoveTaintsEvent;
use Psalm\Plugin\EventHandler\RemoveTaintsInterface;

class Psalm6RenderArrayTainter implements RemoveTaintsInterface
{
    public static function removeTaints(AddRemoveTaintsEvent $event): array
    {
        // We could/should use a custom taint type here.
        return RenderArray::isEscapedItem($event) ? ['html'] : [];
    }
}
