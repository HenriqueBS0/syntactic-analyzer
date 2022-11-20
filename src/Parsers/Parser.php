<?php

namespace HenriqueBS0\SyntacticAnalyzer\Parsers;

use Closure;
use HenriqueBS0\LexicalAnalyzer\LexicalAnalyzer;
use HenriqueBS0\LexicalAnalyzer\TokenStack;
use HenriqueBS0\SyntacticAnalyzer\Grammar\Grammar;

abstract class Parser {

    private LexicalAnalyzer $lexicalAnalyzer;
    private Grammar $grammar;
    private Closure $prepareTokenStack;

    public function __construct(LexicalAnalyzer $lexicalAnalyzer, Grammar $grammar)
    {   
        $this->grammar = $grammar;
        $this->lexicalAnalyzer = $lexicalAnalyzer;
    }

    protected function getGrammar() : Grammar
    {
        return $this->grammar;
    }

    protected function getLexicalAnalizer() : LexicalAnalyzer
    {
        return $this->lexicalAnalyzer;
    }

    protected function hasPrepareTokenStack() : bool
    {
        return !is_null($this->prepareTokenStack);
    }

    public function setPrepareTokenStack(Closure $prepareTokenStack) : void
    {
        $this->prepareTokenStack = $prepareTokenStack;
    }

    protected function prepareTokenStack(TokenStack $tokensStack) : TokenStack 
    {
        return call_user_func($this->prepareTokenStack, $tokensStack);
    }
}