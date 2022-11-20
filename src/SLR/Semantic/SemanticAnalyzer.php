<?php

namespace HenriqueBS0\SyntacticAnalyzer\SLR\Semantic;

class SemanticAnalyzer {
    public function newSemanticException(string $message = "", int $code = 0) {
        throw new SemanticAnalyzerException($message, $code);
    }
}