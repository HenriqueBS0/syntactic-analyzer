<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Stack;

use HenriqueBS0\LexicalAnalyzer\Token;
use HenriqueBS0\SyntacticAnalyzer\SLR\Tree\Node;

class Layer {
    private string $state;
    private string $symbol;
    private Node $node;
    private Token $token;

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function setNode(Node $node) : self
    {
        $this->node = $node;

        return $this;
    }

    public function getNode() : Node
    {
        return $this->node;
    }

    public function setToken(Token $token) : self
    {
        $this->token = $token;

        return $this;
    }

    public function getToken() : Token
    {
        return $this->token;
    }
}