<?php

namespace mortenson\PsalmPluginDrupal;

use PhpParser\Node\Expr\ArrayItem;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\AddRemoveTaintsEvent;

class RenderArray
{
    private const DANGEROUS_KEYS = [
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

    /**
     * Whether the expression is a render array item whose value Drupal escapes.
     */
    public static function isEscapedItem(AddRemoveTaintsEvent $event): bool
    {
        $item = $event->getExpr();
        $statements_analyzer = $event->getStatementsSource();
        if (!($item instanceof ArrayItem) || !($statements_analyzer instanceof StatementsAnalyzer)) {
            return false;
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

        return isset($item_key_value[0])
            && $item_key_value[0] === '#'
            && !in_array($item_key_value, self::DANGEROUS_KEYS, true);
    }
}
