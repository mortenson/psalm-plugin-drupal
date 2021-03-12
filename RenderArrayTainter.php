<?php

namespace mortenson\PsalmPluginDrupal;

use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar\String_;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\StatementsSource;
use Psalm\Type\TaintKindGroup;
use Psalm\Plugin\Hook\AfterExpressionAnalysisInterface;

class RenderArrayTainter implements AfterExpressionAnalysisInterface
{

    public static function afterExpressionAnalysis(
        Expr $expr,
        Context $context,
        StatementsSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = []
    ): ?bool {
        if ($expr instanceof Node\Expr\Array_) {
            /** @var ArrayItem $item */
            foreach ($expr->items as $item) {
                if ($item->key instanceof String_ && $item->key->value === "#children") {
                    $codeLocation = new CodeLocation($statements_source, $expr);
                    $expr_id = '$render-array-children'
                    . '-' . $statements_source->getFileName()
                    . ':' . $expr->getAttribute('startFilePos');
                    $codebase->addTaintSink($expr_id, TaintKindGroup::ALL_INPUT, $codeLocation);
                }
            }
            return true;
        }
        return null;
    }

}
