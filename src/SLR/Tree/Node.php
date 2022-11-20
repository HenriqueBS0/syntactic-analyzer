<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Tree;

use HenriqueBS0\SyntacticAnalyzer\SLR\Semantic\SemanticAnalyzer;
abstract class Node {
    public function semanticValidation(SemanticAnalyzer &$semanticAnalyzer) : void 
    {

    } 
};