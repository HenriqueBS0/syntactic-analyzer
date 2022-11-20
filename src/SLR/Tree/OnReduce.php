<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Tree;

use Exception;
use HenriqueBS0\SyntacticAnalyzer\SLR\Stack\Layer;

class OnReduce {
    private string $nodeClass;
    private array $settersMethods;

    public function __construct(string $nodeClass) {
        $this->nodeClass = $nodeClass;
    }

    public function getNodeClass() : string
    {
        return $this->nodeClass;
    }

    public function addMethod(string $symbol, string $method) : self
    {
        if(!method_exists($this->nodeClass, $method)) {
            throw new Exception("The '{$method}' method of the '{$this->nodeClass}' class does not use exists.");
        }

        $this->settersMethods[$symbol] = $method;

        return $this;
    }

    public function setMethods(array $methods) : void
    {
        foreach ($methods as $symbol => $method) {
            $this->addMethod($symbol, $method);
        }
    }

    public function getMethod(string $symbol) : string 
    {
        return $this->settersMethods[$symbol];
    }

    public function resolve(Node $node, Layer $layer, bool $terminal) : mixed
    {
        call_user_func([$node, $this->getMethod($layer->getSymbol())], $terminal ? $layer->getToken() : $layer->getNode() );
        return $node;
    }
}