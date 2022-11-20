<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Action;

use HenriqueBS0\SyntacticAnalyzer\Grammar\Grammar;
use HenriqueBS0\SyntacticAnalyzer\Grammar\ProductionRule;
use HenriqueBS0\SyntacticAnalyzer\SLR\Stack\Layer;

class Reduce extends Action {
    private ProductionRule $productionRule;

    public function __construct(ProductionRule $productionRule) {
        $this->productionRule = $productionRule;
    }

    public function getProductionRule() : ProductionRule
    {
        return $this->productionRule;
    }

    public function resolve(ResolveAction &$resolveAction) : void
    {
        $hasOnReduceTable = !is_null($resolveAction->getOnReduceTable());

        if($hasOnReduceTable) {
            $onRedulceCallback = $resolveAction->getOnReduceTable()->getCallbackOnReduce($this->productionRule->getVariable());

            $node = new ($onRedulceCallback->getNodeClass());
        }

        foreach (array_reverse($this->productionRule->getSymbols()) as $symbol) {
            if($symbol === $resolveAction->getGrammar()->getSymbolETransition()) {
                continue;
            }

            $layer = $resolveAction->getParsingStack()->pop();

            if($hasOnReduceTable) {
                $onRedulceCallback->resolve($node, $layer, Grammar::isSymbolTerminal($symbol));
            }   
        }

        if($hasOnReduceTable) {
            $node->semanticValidation($resolveAction->getSemanticAnalyzer());
        }

        $goTo = $resolveAction->getTable()->getItem($resolveAction->getParsingStack()->top()->getState())->getAction($this->productionRule->getVariable());

        $parsingStackLayer = (new Layer)
            ->setState($goTo->getState())
            ->setSymbol($this->productionRule->getVariable());

        if($hasOnReduceTable) {
            $parsingStackLayer->setNode($node);
        }

        $resolveAction->getParsingStack()->push($parsingStackLayer);
    }
}