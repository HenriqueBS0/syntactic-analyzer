<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Action;

use HenriqueBS0\LexicalAnalyzer\TokenStack;
use HenriqueBS0\SyntacticAnalyzer\Grammar\Grammar;
use HenriqueBS0\SyntacticAnalyzer\SLR\Semantic\SemanticAnalyzer;
use HenriqueBS0\SyntacticAnalyzer\SLR\Stack\Stack;
use HenriqueBS0\SyntacticAnalyzer\SLR\Table\Table;
use HenriqueBS0\SyntacticAnalyzer\SLR\Tree\OnReduceTable;

class ResolveAction {
    private Stack             $parsingStack; 
    private TokenStack        $tokenStack;
    private Grammar           $grammar; 
    private Table             $table;
    private SemanticAnalyzer  $semanticAnalyzer;
    private ?OnReduceTable    $onReduceTable = null;

    /**
     * Get the value of parsingStack
     */
    public function getParsingStack(): Stack
    {
        return $this->parsingStack;
    }

    /**
     * Set the value of parsingStack
     */
    public function setParsingStack(Stack $parsingStack): self
    {
        $this->parsingStack = $parsingStack;

        return $this;
    }

    /**
     * Get the value of tokenStack
     */
    public function getTokenStack(): TokenStack
    {
        return $this->tokenStack;
    }

    /**
     * Set the value of tokenStack
     */
    public function setTokenStack(TokenStack $tokenStack): self
    {
        $this->tokenStack = $tokenStack;

        return $this;
    }

    /**
     * Get the value of grammar
     */
    public function getGrammar(): Grammar
    {
        return $this->grammar;
    }

    /**
     * Set the value of grammar
     */
    public function setGrammar(Grammar $grammar): self
    {
        $this->grammar = $grammar;

        return $this;
    }

    /**
     * Get the value of table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * Set the value of table
     */
    public function setTable(Table $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the value of onReduceTable
     */
    public function getOnReduceTable(): ?OnReduceTable
    {
        return $this->onReduceTable;
    }

    /**
     * Set the value of onReduceTable
     */
    public function setOnReduceTable(?OnReduceTable $onReduceTable): self
    {
        $this->onReduceTable = $onReduceTable;

        return $this;
    }

    /**
     * Get the value of semanticAnalyzer
     */
    public function getSemanticAnalyzer(): SemanticAnalyzer
    {
        return $this->semanticAnalyzer;
    }

    /**
     * Set the value of semanticAnalyzer
     */
    public function setSemanticAnalyzer(SemanticAnalyzer $semanticAnalyzer): self
    {
        $this->semanticAnalyzer = $semanticAnalyzer;

        return $this;
    }
}