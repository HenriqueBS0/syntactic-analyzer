<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Tree;

class OnReduceTable {
    private array $callbacksOnReduce = [];

    public function addCallbackOnReduce(string $grammarVariable, OnReduce $onReduce) : void
    {
        $this->callbacksOnReduce[$grammarVariable] = $onReduce;
    }

    public function setCallbacksOnReduce(array $callbacksOnReduce) : self
    {
        foreach ($callbacksOnReduce as $variable => $onReduce) {
            $this->addCallbackOnReduce($variable, $onReduce);
        }

        return $this;
    }

    public function getCallbackOnReduce(string $grammarVariable) : OnReduce
    {
        return $this->callbacksOnReduce[$grammarVariable];
    }
}