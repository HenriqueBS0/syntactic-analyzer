<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Stack;

use HenriqueBS0\DataStructures\Stack as DataStructuresStack;

class Stack {

    private DataStructuresStack $stack;

    public function __construct() {
        $this->stack = new DataStructuresStack();
    }

    public function push(Layer $layer) : self 
    {
        $this->stack->push($layer);
        return $this;
    }

    public function pop() : Layer
    {
        return $this->stack->pop();
    }

    public function top() : Layer
    {
        return $this->stack->top();
    }

}