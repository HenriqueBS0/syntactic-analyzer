<?php

namespace HenriqueBS0\SyntacticAnalyzer\Parsers;

use Exception;
use HenriqueBS0\LexicalAnalyzer\Token;

class SyntacticException extends Exception {
    private Token $token;

    /**
     * Get the value of token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * Set the value of token
     */
    public function setToken(Token $token): self
    {
        $this->token = $token;

        return $this;
    }
}