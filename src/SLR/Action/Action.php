<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Action;

use HenriqueBS0\LexicalAnalyzer\TokenStack;
use HenriqueBS0\SyntacticAnalyzer\Grammar\Grammar;
use HenriqueBS0\SyntacticAnalyzer\SLR\Stack\Stack;
use HenriqueBS0\SyntacticAnalyzer\SLR\Table\Table;
use HenriqueBS0\SyntacticAnalyzer\SLR\Tree\OnReduceTable;

abstract class Action {
    public function resolve(ResolveAction &$resolveAction) : void {}
};