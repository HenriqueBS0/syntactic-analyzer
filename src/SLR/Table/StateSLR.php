<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Table;

use HenriqueBS0\SyntacticAnalyzer\Grammar\Grammar;
use HenriqueBS0\SyntacticAnalyzer\Grammar\ProductionRule;

class StateSLR {
    private string $name;

    /** @var ItemLR[] */
    private array $coreItems = [];

    /** @var ItemLR[] */
    private array $closingItems = [];

    public function __construct(string $name, array $coreItems, array $closingItems) {
        $this->setName($name);
        $this->setCoreItems($coreItems);
        $this->setClosingItems($closingItems);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCoreItems(): array
    {
        return $this->coreItems;
    }

    public function setCoreItems(array $coreItems): self
    {
        $this->coreItems = $coreItems;

        return $this;
    }

    public function getClosingItems(): array
    {
        return $this->closingItems;
    }

    public function setClosingItems(array $closingItems): self
    {
        $this->closingItems = $closingItems;

        return $this;
    }

    /**
     * @return ItemLR[]
     */
    public function getItems() : array
    {
        return array_merge($this->getCoreItems(), $this->getClosingItems());
    }

    public function getCompleteItems() : array
    {
        return array_filter($this->getItems(), function(ItemLR $item) {return $item->isComplete();});
    }

    /**
     * @param Grammar $grammar
     * @return StateSLR[]
     */
    public static function getStatesByGrammar(Grammar $grammar) : array 
    {
        $states = [];

        $coreItems = [
            [new ItemLR($grammar->getVariableProductionRules($grammar->getSymbolInitial())[0], 0)]
        ];

        $idState = 0;

        while(count($coreItems) > 0) {
            $nextCoreItems = [];

            foreach ($coreItems as $coreItemsState) {
                if(self::existsStateWithCoreItems($states, $coreItemsState)) {
                    continue;
                }

                $state = self::createState($idState, $coreItemsState, $grammar);

                $states[] = $state;

                $nextCoreItems = array_merge($nextCoreItems, self::getNextCoreItemsByState($state, $grammar));

                $idState++;
            }

            $coreItems = $nextCoreItems;
        }

        return $states;
    }

    /**
     * @param StateSLR[] $states
     * @param ItemLR[] $coreItems
     * @return boolean
     */
    public static function existsStateWithCoreItems(array $states, array $coreItems) : bool
    {
        return !is_null(self::getStateWithCoreItems($states, $coreItems));
    }

    /**
     * @param StateSLR[] $states
     * @param ItemLR[] $coreItems
     * @return StateSLR|null
     */
    public static function getStateWithCoreItems(array $states, array $coreItems) : ?StateSLR
    {
        foreach ($states as $state) {
            if(count($coreItems) !== count($state->getCoreItems())) {
                continue;
            }

            $exists = true;

            foreach ($coreItems as $coreItem) {
                if(!in_array($coreItem, $state->getCoreItems())) {
                    $exists = false;
                    break;
                }
            }

            if($exists) {
                return $state;
            }
        }

        return null;
    }

    /**
     * @param int $id
     * @param ItemLR[] $coreItems
     * @param Grammar $grammar
     * @return StateSLR
     */
    public static function createState(int $id, array $coreItems, Grammar $grammar) : StateSLR
    {
        return new StateSLR(strval($id), $coreItems, self::getClosingItemsForCoreItems($coreItems, $grammar));
    }

    /**
     * @param ItemLR[] $coreItems
     * @param Grammar $grammar
     * @return ItemLR[]
     */
    public static function getClosingItemsForCoreItems(array $coreItems, Grammar $grammar) : array
    {
        $closingItems = [];

        foreach ($coreItems as $coreItem) {
            $closingItemsCoreItem = self::getClosingItemsForCoreItem($coreItem, $grammar);
            $closingItems = array_unique(array_merge($closingItems, $closingItemsCoreItem), SORT_REGULAR);
        }

        return $closingItems;
    }

    /**
     * @param ItemLR[] $coreItems
     * @param Grammar $grammar
     * @return ItemLR[]
     */
    public static function getClosingItemsForCoreItem(ItemLR $coreItem, Grammar $grammar) : array
    {
        if($coreItem->isComplete() || Grammar::isSymbolTerminal($coreItem->getSymbolAfterPoint())) {
            return [];
        }

        $itemsToCatchClosing = [$coreItem];

        $closingItems = [];

        while(count($itemsToCatchClosing) > 0) {
            $nextItemsToCatchClosing = [];

            foreach ($itemsToCatchClosing as $itemToCatchClosing) {
                $variable = Grammar::getVariable($itemToCatchClosing->getSymbolAfterPoint());

                /** @var ProductionRule */
                foreach ($grammar->getVariableProductionRules($variable) as $productionRule) {
                    $closingItem = new ItemLR($productionRule, 0);

                    if(in_array($closingItem, $closingItems)) {
                        continue;
                    }

                    $closingItems[] = $closingItem;

                    if($closingItem->getSymbolAfterPoint() === $grammar->getSymbolETransition()) {
                        $closingItem->setPointPosition(1);
                        continue;
                    }

                    if(Grammar::isSymbolVariable($closingItem->getSymbolAfterPoint())) {
                        $nextItemsToCatchClosing[] = $closingItem; 
                    }
                }       
            }

            $itemsToCatchClosing = $nextItemsToCatchClosing;
        }

        return $closingItems;
    }

    /**
     * @param StateSLR $state
     * @param Grammar $grammar
     * @return array
     */
    public static function getNextCoreItemsByState(StateSLR $state, Grammar $grammar, bool $symbolTransition = false) : array
    {
        $nextCoreItems = [];

        foreach ($state->getItems() as $item) {
            if($item->isComplete() || $item->getSymbolAfterPoint() === $grammar->getSymbolETransition()) {
                continue;
            }

            $nextCoreItem = clone $item;
            $nextCoreItem->setPointPosition($nextCoreItem->getPointPosition() + 1);

            $nextCoreItems[$item->getSymbolAfterPoint()][] = $nextCoreItem;
        }

        return $symbolTransition ? $nextCoreItems : array_values($nextCoreItems);
    }
}