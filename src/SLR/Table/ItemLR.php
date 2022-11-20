<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Table;

use HenriqueBS0\SyntacticAnalyzer\Grammar\ProductionRule;

class ItemLR {
    private ProductionRule $productionRule;
    private int $pointPosition;

    public function __construct(ProductionRule $productionRule, int $pointPosition) {
        $this->setProductionRule($productionRule);    
        $this->setPointPosition($pointPosition);
    }

    public function getProductionRule(): ProductionRule
    {
        return $this->productionRule;
    }

    
    public function setProductionRule(ProductionRule $productionRule): self
    {
        $this->productionRule = $productionRule;

        return $this;
    }

    public function getPointPosition(): int
    {
        return $this->pointPosition;
    }

    public function setPointPosition(int $pointPosition): self
    {
        $this->pointPosition = $pointPosition;

        return $this;
    }

    public function getSymbolsWithPoint() : array 
    {
        $symbols = $this->getProductionRule()->getSymbols();

        array_splice($symbols, $this->getPointPosition(), 0, '.');
        
        return $symbols;
    }

    public function getSymbolBeforePoint() : string|null
    {
        return $this->getPointPosition() !== 0 ? $this->getSymbolsWithPoint()[$this->getPointPosition() - 1] : null;
    }

    public function getSymbolAfterPoint() : string|null
    {
        return $this->getPointPosition() !==  count($this->getSymbolsWithPoint()) ? $this->getSymbolsWithPoint()[$this->getPointPosition() + 1] : null;
    }

    public function isInitial() : bool
    {
        return is_null($this->getSymbolBeforePoint());
    }

    public function isComplete() : bool
    {
        return is_null($this->getSymbolAfterPoint());
    }

    /**
     * @param ItemLR[] $items
     * @param ItemLR[] $itemsToAdd
     * @return ItemLR[]
     */
    public static function mergeItems(array $items, array $itemsToAdd) : array 
    {
        foreach ($itemsToAdd as $item) {
            if(!in_array($item, $items)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}