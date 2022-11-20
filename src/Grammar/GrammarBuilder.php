<?php

namespace HenriqueBS0\SyntacticAnalyzer\Grammar;

class GrammarBuilder {

    /**
     * @param string[]|string $productionRules
     * @param string|null $symbolInitial
     * @param string $symbolFinal
     * @param string $symbolETransition
     * @return Grammar
     */
    public static function getGrammar(array|string $productionRules, string|null $symbolInitial = null, string $symbolFinal = '$', string $symbolETransition = 'ε') : Grammar
    {
        $productionRules = self::getProductionRules($productionRules);
        $symbolInitial = $symbolInitial ?: $productionRules[0]->getVariable();
        
        return new Grammar($symbolInitial, $productionRules, $symbolFinal, $symbolETransition);
    }

    /**
     * @param string[]|string $productionRules
     * @return ProductionRule[]
     */
    private static function getProductionRules(array|string $productionRules) : array
    {
        $productionsRulesString = is_string($productionRules) ? self::trimElements(explode(PHP_EOL, $productionRules)) : $productionRules;   

        $productionRules = [];

        foreach ($productionsRulesString as $productionRuleString) {
            $productionRuleStringExplode = self::trimElements(explode('::=', $productionRuleString));

            $variable = $productionRuleStringExplode[0];

            foreach (self::trimElements(explode('|', $productionRuleStringExplode[1])) as $choice) {
                $productionRules[] = new ProductionRule($variable, self::trimElements(explode(' ', $choice)));
            }
        }

        return $productionRules;
    }

    private static function trimElements(array $elements) : array
    {
        return array_map(function($element) {return trim($element);}, $elements);
    }
}