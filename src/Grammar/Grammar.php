<?php

namespace HenriqueBS0\SyntacticAnalyzer\Grammar;

class Grammar {
    private string $symbolInitial;
    private string $symbolFinal = '$';
    private string $symbolETransition = 'ε';

    /** @var ProductionRule[] */
    private array $productionRules = [];

    /**
     * @param string $symbolInitial
     * @param string $symbolFinal
     * @param string $symbolETransition
     * @param ProductionRule[] $productionRules
     */
    public function __construct(string $symbolInitial, array $productionRules = [], string $symbolFinal = '$', string $symbolETransition = 'ε')
    {
        $this->setSymbolInitial($symbolInitial);
        $this->setSymbolFinal($symbolFinal);
        $this->setSymbolETransition($symbolETransition);
        $this->setProductionRules($productionRules);
    }

    public function getSymbolInitial(): string
    {
        return $this->symbolInitial;
    }

    public function setSymbolInitial(string $symbolInitial): self
    {
        $this->symbolInitial = $symbolInitial;

        return $this;
    }

    public function getSymbolFinal(): string
    {
        return $this->symbolFinal;
    }

    public function setSymbolFinal(string $symbolFinal): self
    {
        $this->symbolFinal = $symbolFinal;

        return $this;
    }

    public function getSymbolETransition(): string
    {
        return $this->symbolETransition;
    }

    public function setSymbolETransition(string $symbolETransition): self
    {
        $this->symbolETransition = $symbolETransition;

        return $this;
    }

    /**
     * @return ProductionRule[]
     */
    public function getProductionRules(): array
    {
        return $this->productionRules;
    }

    /**
     * @return ProductionRule[]
     */
    public function getVariableProductionRules(string $variable) : array
    {
        $callback = function(ProductionRule $productionRule) use($variable) : bool 
        {
            return $productionRule->getVariable() === $variable;
        };

        $productionsRules = array_filter($this->getProductionRules(), $callback);

        return array_values($productionsRules);
    }

    public function setProductionRules(array $productionRules): self
    {
        $this->productionRules = $productionRules;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getVariables() : array
    {
        $callback = function(ProductionRule $productionRule) : string
        {
            return $productionRule->getVariable();
        };

        return array_unique(array_map($callback, $this->getProductionRules()));
    }

    /** @return array */
    public function getFirsts() : array
    {
        $firsts = [];

        do {
            $firstsBeforeScan = $firsts;

            foreach ($this->getProductionRules() as $rule) {
                $firsts[$rule->getVariable()] = $this->getFirstsRule($rule, $firsts);
            }

        } while($firsts !== $firstsBeforeScan);

        return $firsts;
    }

    /** @return string[] */
    public function getFirstsRule(ProductionRule $rule, array $firsts) : array
    {
        $firstsRule = $firsts[$rule->getVariable()] ?: [];
        
        $allWithETransition = true;

        foreach ($rule->getSymbols() as $symbol) {
            if(Grammar::isSymbolTerminal($symbol)) {
                $firstsRule[] = $symbol;
                $allWithETransition = $symbol === $this->getSymbolETransition();
                break;
            }

            $firstVariable =  $firsts[Grammar::getVariable($symbol)] ?: [];

            $firstsRule = array_merge($firstsRule, $firstVariable);

            if(!in_array($this->getSymbolETransition(), $firstVariable)) {
                $allWithETransition = false;
                break;
            }
        }

        if($allWithETransition) {
            $firstsRule[] = $this->getSymbolETransition();
        }

        sort($firstsRule);

        return array_unique($firstsRule);
    }

    /** @return array */
    public function getFollows() : array
    {
        $firsts = $this->getFirsts();

        $follows = [];

        foreach ($this->getVariables() as $varible) {
            $follows[$varible] = [];
        }

        $follows[$this->getSymbolInitial()][] = $this->getSymbolFinal();

        do {
            $followsBeforeScan = $follows;

            foreach ($this->getProductionRules() as $rule) {
                $follows = $this->getFollowsByRule($rule, $follows, $firsts);
            }

        } while($followsBeforeScan !== $follows);

        return $follows;
    }

    /**
     * @param ProductionRule $rule
     * @param array $follows
     * @param array $firsts
     * @return string[]
     */
    private function getFollowsByRule(ProductionRule $rule, array $follows, array $firsts) : array
    {
        $lastSymbolIndex = count($rule->getSymbols()) - 1;

        foreach ($rule->getSymbols() as $index => $symbol) {
            if(Grammar::isSymbolTerminal($symbol)) {
                continue;
            }

            $variable = Grammar::getVariable($symbol);

            if($index === $lastSymbolIndex) {
                $follows[$variable] = array_unique(array_merge($follows[$variable], $follows[$rule->getVariable()]));
                sort($follows[$variable]); 
                continue;
            }

            for($indexNextSymbol = $index + 1; $indexNextSymbol <= $lastSymbolIndex; $indexNextSymbol++) {

                $nextSymbol = $rule->getSymbols()[$indexNextSymbol];

                if(Grammar::isSymbolTerminal($nextSymbol)) {
                    $follows[$variable][] = $nextSymbol;
                    $follows[$variable] = array_unique($follows[$variable]);
                    break;
                }

                $nextVariable = Grammar::getVariable($nextSymbol);

                $follows[$variable] = $this->removeETransitionSymbol(array_unique(array_merge($follows[$variable], $firsts[$nextVariable])));

                sort($follows[$variable]);
             
                if(!in_array($this->getSymbolETransition(), $firsts[$nextVariable])) {
                    break;
                }
            }
        }

        return $follows;
    }

    /**
     * @param string[] $symbols
     * @return array
     */
    private function removeETransitionSymbol(array $symbols) : array
    {
        $newSymbols = [];

        foreach ($symbols as $symbol) {
            if($symbol !== $this->getSymbolETransition()) {
                $newSymbols[] = $symbol;
            }
        }

        return $newSymbols;
    }

    public static function isSymbolTerminal(string $symbol) : bool
    {
        return !self::isSymbolVariable($symbol);
    }

    public static function isSymbolVariable(string $symbol) : bool
    {
        $firstCharacter = substr($symbol, 0, 1);
        $lastCharacter = substr($symbol, -1); 
        return $firstCharacter === '<' && $lastCharacter === '>';
    }

    public static function getVariable(string $symbol) : string
    {
        $name = substr($symbol, 1, (strlen($symbol) - 2));
        return $name;
    }
}