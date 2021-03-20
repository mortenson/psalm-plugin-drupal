<?php

namespace mortenson\PsalmPluginDrupal;

use PhpParser\Node\Expr\ArrayItem;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\AddRemoveTaintsEvent;
use Psalm\Plugin\EventHandler\RemoveTaintsInterface;

class RenderArrayTainter implements RemoveTaintsInterface
{
    public static function removeTaints(AddRemoveTaintsEvent $event): array
    {
        $item = $event->getExpr();
        $statements_analyzer = $event->getStatementsSource();
        if (!($item instanceof ArrayItem) || !($statements_analyzer instanceof StatementsAnalyzer)) {
            return [];
        }
        $item_key_value = '';
        if ($item->key) {
            if ($item_key_type = $statements_analyzer->node_data->getType($item->key)) {
                $key_type = $item_key_type;

                if ($key_type->isSingleStringLiteral()) {
                    $item_key_value = $key_type->getSingleStringLiteral()->value;
                }
            }
        }

        $dangerous_keys = [
            // Code execution.
            '#access_callback',
            '#ajax',
            '#after_build',
            '#element_validate',
            '#lazy_builder',
            '#post_render',
            '#pre_render',
            '#process',
            '#submit',
            '#validate',
            '#value_callback',
            '#file_value_callbacks',
            '#date_date_callbacks',
            '#date_time_callbacks',
            '#captcha_validate',
            // Cross site scripting.
            '#template',
            '#children',
        ];

        if (!in_array($item_key_value, $dangerous_keys, true)) {
            // We could/should use a custom taint type here.
            return ['html'];
        }

        return [];
    }
}
