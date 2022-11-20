<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Table;

use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Accept;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Action;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\GoToAction;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Reduce;
use HenriqueBS0\SyntacticAnalyzer\SLR\Action\Shift;

class ItemTable {
    private array $action = [];

    public function addAction(string $input, Action $action) : void
    {
        $this->action[$input] = $action;
    }

    public function getActions() : array 
    {
        return $this->action;
    }

    public function getAction(string $input) : Accept|GoToAction|Reduce|Shift
    {
        return $this->action[$input];
    }

    public function hasActionForInput($input) : bool
    {
        return isset($this->action[$input]);
    }
}