<?php

namespace Mev\ConsoleQueryTool\Common\Query\Lexer;

/**
 * Represent each part of SQL syntax
 * 
 * 
 * @package Mev\ConsoleQueryTool\Common\Query\Lexer
 */
class Token
{
    /** 
     * Lexical type of token
     * 
     * @var  TokenType
     */
    private $type;

    /**
     * Value from query
     * 
     * @var string
     */
    private $value;

    /**
     * Token constructor.
     * @param $type     
     * @param $value
     */
    public function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $this->normalizeScalar($value);
    }


    /**
     * @return TokenType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Check token for SQL keyword
     * 
     * @return bool
     */
    public function isKeyword ()
    {
        return $this->isTypeAnyOf([
            TokenType::T_SELECT,
            TokenType::T_FROM,
            TokenType::T_WHERE,
            TokenType::T_ORDER,
            TokenType::T_LIMIT,
        ]);
    }

    /**
     * Check token type
     * 
     * @param $typeIdentifier
     * @return bool
     */
    public function isTypeOf($typeIdentifier) 
    {
        return $this->getType()->getValue() === $typeIdentifier;
        
    }

    /**
     * 
     * @param $types
     * @return bool
     */
    public function isTypeAnyOf($types)
    {
        return in_array($this->getType()->getValue(), $types);
    }

    /**
     * Normalize scalar token values
     * @param $value
     * @return string
     */
    private function normalizeScalar ($value)
    {
        switch ($this->getType()->getValue()) {
            case TokenType::S_STRING:
                $value = (string) $value;
                break;
            case TokenType::S_INTEGER:
                $value = intval($value);
                break;
            case TokenType::S_FLOAT:
                $value = floatval($value);
                break;
        }

        return $value;
    }
}