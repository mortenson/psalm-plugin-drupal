<?php

namespace mortenson\PsalmPluginDrupal;

use Psalm\Plugin\EventHandler\Event\AddRemoveTaintsEvent;
use Psalm\Plugin\EventHandler\RemoveTaintsInterface;
use Psalm\Type\TaintKind;

class RenderArrayTainter implements RemoveTaintsInterface
{
    public static function removeTaints(AddRemoveTaintsEvent $event): int
    {
        // We could/should use a custom taint type here.
        return RenderArray::isEscapedItem($event) ? TaintKind::INPUT_HTML : 0;
    }
}
