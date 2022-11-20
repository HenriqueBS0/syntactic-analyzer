<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Action;

use HenriqueBS0\SyntacticAnalyzer\SLR\Stack\Layer;

class Shift extends Action {
    private string $state;

    public function __construct(string $state) {
        $this->state = $state;
    }

    public function getState() : string
    {
        return $this->state;
    }

    public function resolve(ResolveAction &$resolveAction) : void
    {
        $token = $resolveAction->getTokenStack()->pop();

        $parsingStackLayer = (new Layer)
            ->setState($this->state)
            ->setSymbol($token->getToken())
            ->setToken($token);

        $resolveAction->getParsingStack()->push($parsingStackLayer);
    }
}