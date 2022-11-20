<?php

namespace HenriqueBS0\SyntacticAnalyzer\Parsers;

use HenriqueBS0\LexicalAnalyzer\LexicalAnalyzer;
use HenriqueBS0\LexicalAnalyzer\LexicalAnalyzerException;
use HenriqueBS0\LexicalAnalyzer\Position;
use HenriqueBS0\LexicalAnalyzer\Token;
use HenriqueBS0\LexicalAnalyzer\TokenStack;
use HenriqueBS0\SyntacticAnalyzer\Grammar\Grammar;
use HenriqueBS0\SyntacticAnalyzer\Grammar\ProductionRule;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Accept;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\ResolveAction;
use HenriqueBS0\SyntacticAnalyzer\SLR\Semantic\SemanticAnalyzer;
use HenriqueBS0\SyntacticAnalyzer\SLR\Semantic\SemanticAnalyzerException;
use HenriqueBS0\SyntacticAnalyzer\SLR\Stack\Layer;
use HenriqueBS0\SyntacticAnalyzer\SLR\Stack\Stack;
use HenriqueBS0\SyntacticAnalyzer\SLR\Table\Table;
use HenriqueBS0\SyntacticAnalyzer\SLR\Tree\OnReduceTable;

class ParserSLR extends Parser {

    private Table $table;
    private OnReduceTable $onReduceTable;
    private ?SemanticAnalyzer $semanticAnalyzer = null;

    public function getOnReduceTable() : OnReduceTable
    {
        return $this->onReduceTable;
    }

    public function setOnReduceTable(OnReduceTable $onReduceTable) : void
    {
        $this->onReduceTable = $onReduceTable;
    }

    private function getSemanticAnalyzer() : ?SemanticAnalyzer
    {
        return $this->semanticAnalyzer;
    }

    public function setSemanticAnalyzer(SemanticAnalyzer $semanticAnalyzer): self
    {
        $this->semanticAnalyzer = $semanticAnalyzer;

        return $this;
    }

    public function __construct(LexicalAnalyzer $lexicalAnalyzer, Grammar $grammar)
    {
        parent::__construct($lexicalAnalyzer, self::prepareGrammar($grammar));
        $this->setTable();
    }

    private function getParsingStack() : Stack 
    {
        return (new Stack())->push((new Layer())->setState('0'));
    }

    private function getTokens(string $input) : TokenStack
    {

        $lastToken = new Token($this->getGrammar()->getSymbolFinal(), $this->getGrammar()->getSymbolFinal(), new Position(0,0,0,0,0,0)); 

        $tokensStack = $this->getLexicalAnalizer()
            ->getTokens($input)
            ->reverseOrdering();

        $tokensStack->push($lastToken);

        $tokensStack->reverseOrdering();

        if($this->hasPrepareTokenStack()) {
            return $this->prepareTokenStack($tokensStack);
        }

        return $tokensStack;
    }

    private static function prepareGrammar(Grammar $grammar) : Grammar
    {
        $newInicialProductionRule = new ProductionRule("{$grammar->getSymbolInitial()}'", ["<{$grammar->getSymbolInitial()}>"]);
        
        $productionRules = $grammar->getProductionRules();

        $productionRules[] = $newInicialProductionRule;

        $grammar->setProductionRules($productionRules);

        $grammar->setSymbolInitial($newInicialProductionRule->getVariable());

        return $grammar;
    }

    private function setTable() : void
    {
        $this->table = Table::getTable($this->getGrammar());
    }

    private function getTable() : Table
    {
        return $this->table;
    }

    public function accepted(string $input) : bool
    {

        $resolveAction = new ResolveAction();
        $resolveAction->setGrammar($this->getGrammar());
        $resolveAction->setTable($this->getTable());
        $resolveAction->setParsingStack($this->getParsingStack());
        
        
        try {            
            $resolveAction->setTokenStack($this->getTokens($input));
        } catch (\Throwable $th) {
            return false;
        } 

        while(!$resolveAction->getTokenStack()->isEmpty()) {

            $itemTable = $this->getTable()->getItem($resolveAction->getParsingStack()->top()->getState());

            if(!$itemTable->hasActionForInput($resolveAction->getTokenStack()->top()->getToken())) {
                return false;
            }

            $action = $itemTable->getAction($resolveAction->getTokenStack()->top()->getToken());

            if($action instanceof Accept) {
                return true;
            }

            $action->resolve($resolveAction);
        }
    }

    /**
     * @param string $input
     * @throws LexicalAnalyzerException
     * @throws SyntacticException
     * @throws SemanticAnalyzerException 
     * @return mixed
     */
    public function getParseTree(string $input) : mixed
    {
        $resolveAction = new ResolveAction();
        $resolveAction->setGrammar($this->getGrammar());
        $resolveAction->setTable($this->getTable());
        $resolveAction->setParsingStack($this->getParsingStack());
        $resolveAction->setOnReduceTable($this->getOnReduceTable());

        $resolveAction->setSemanticAnalyzer($this->getSemanticAnalyzer() ?: new SemanticAnalyzer());
        
        $resolveAction->setTokenStack($this->getTokens($input));

        while(!$resolveAction->getTokenStack()->isEmpty()) {

            $itemTable = $this->getTable()->getItem($resolveAction->getParsingStack()->top()->getState());

            if(!$itemTable->hasActionForInput($resolveAction->getTokenStack()->top()->getToken())) {
                $ex = new SyntacticException();
                $ex->setToken($resolveAction->getTokenStack()->top());
                throw new $ex;
            }

            $action = $itemTable->getAction($resolveAction->getTokenStack()->top()->getToken());

            if($action instanceof Accept) {
                return $resolveAction->getParsingStack()->top()->getNode();
            }

            $action->resolve($resolveAction);
        }
    }
}   