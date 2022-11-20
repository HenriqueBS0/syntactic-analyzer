<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Action;

class GoToAction extends Action {
    private string $state;

    public function __construct(string $state) {
        $this->state = $state;
    }

    public function getState() : string
    {
        return $this->state;
    }
}