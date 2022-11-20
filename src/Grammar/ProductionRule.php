<?php

namespace HenriqueBS0\SyntacticAnalyzer\Grammar;

class ProductionRule {
    private string $variable;

    /**
     * @var string[]
     */
    private array $symbols;

    public function __construct(string $variable, array $symbols)
    {
        $this->setVariable($variable);
        $this->setSymbols($symbols);
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    public function setVariable(string $variable): self
    {
        $this->variable = $variable;

        return $this;
    }

    /** @return string[] */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    public function setSymbols(array $symbols): self
    {
        $this->symbols = $symbols;

        return $this;
    }

    public function getFirstSymbol() : string
    {
        return $this->getSymbols()[0];
    }
}