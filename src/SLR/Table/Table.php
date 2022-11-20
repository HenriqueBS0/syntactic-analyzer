<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Table;

use HenriqueBS0\SyntacticAnalyzer\Grammar\Grammar;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Accept;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\GoToAction;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Reduce;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Shift;

class Table {

    /** @var ItemTable[] */
    private array $itens;

    public function addItem(string $state, ItemTable $item) : void
    {
        $this->itens[$state] = $item;
    }

    public function getItem(string $state) : ItemTable
    {
        return $this->itens[$state];
    }

    public static function getTable(Grammar $grammar) : Table
    {
        $table = new Table();

        $states = StateSLR::getStatesByGrammar($grammar);
        
        $follows = $grammar->getFollows(); 

        foreach ($states as $state) {
            $itemTable = new ItemTable();

            foreach (StateSLR::getNextCoreItemsByState($state, $grammar, true) as $symbol => $coreItems) {
                $nextState = StateSLR::getStateWithCoreItems($states, $coreItems);  
                
                if($symbol === $grammar->getSymbolETransition()) {
                    continue;
                }

                if(Grammar::isSymbolVariable($symbol)) {
                    $itemTable->addAction(Grammar::getVariable($symbol), new GoToAction($nextState->getName()));
                }
                else {
                    $itemTable->addAction($symbol, new Shift($nextState->getName()));
                }
            }

            /** @var ItemLR */
            foreach ($state->getCompleteItems() as $item) {

                $variable = $item->getProductionRule()->getVariable();

                foreach ($follows[$variable] as $follow) {

                    $action = $item->getProductionRule()->getVariable() === $grammar->getSymbolInitial() ? new Accept() : new Reduce($item->getProductionRule());

                    $itemTable->addAction($follow, $action);
                }
            }

            $table->addItem($state->getName(), $itemTable);
        }

        return $table;
    }
}