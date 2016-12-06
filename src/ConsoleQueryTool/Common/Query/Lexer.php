<?php

namespace Mev\ConsoleQueryTool\Common\Query;

use Mev\ConsoleQueryTool\Common\Query\Lexer\Token;
use Mev\ConsoleQueryTool\Common\Query\Lexer\TokenCollection;
use Mev\ConsoleQueryTool\Common\Query\Lexer\TokenType;

/**
 * Represent string query as array of tokens
 */
class Lexer
{
    /**
     * @var Token[]
     */
    private $tokenCollection;

    /**
     * Aggregate tokens from query string
     * @param $query    SQL query string
     * @return $this
     */
    public function aggregate($query)
    {
        $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
        $matches = preg_split($this->getPattern(), $query, -1, $flags);

        $this->tokenCollection = array_map(function ($matched) {

            list ($value, ) = $matched;
            
            return new Token($this->getTokenType($value), $value);

        }, $matches);

        return $this;
    }


    /**
     * Returns tokens collection
     * 
     * @return TokenCollection
     */
    public function getTokenCollection()
    {
        return new TokenCollection ($this->tokenCollection);
    }

    /**
     * Create regular expr pattern for matching token values
     * 
     * @return string
     */
    public function getPattern()
    {
        return sprintf(
            '/(%s)|%s/%s',
            implode(')|(', $this->getCatchablePatterns()),
            implode('|', $this->getNonCatchablePatterns()),
            'i'
        );
    }

    /**
     * Patterns for matching identifiers, expression, expression values, string values 
     * @return array
     */
    protected function getCatchablePatterns()
    {
        return array(
            '[a-z_\\\][a-z0-9_]*(?:\\\[a-z_][a-z0-9_]*)*',
            '(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?',
            "'(?:[^']|'')*'",
            '\?[0-9]*|:[a-z_][a-z0-9_]*'
        );
    }

    /**
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        return array('\s+', '(.)');
    }

    /**
     * Return token type by matched part of query
     * 
     * @param $value
     * @return mixed
     */
    protected function getTokenType ($value)
    {
        switch (true)
        {
            
            case is_numeric($value):
                $type = TokenType::S_INTEGER();

                if (false !== strpos($value, '.')) {
                    $type = TokenType::S_FLOAT();
                }
                break;
            case $value[0] === "'":
                $type = TokenType::S_STRING();
                break;
            case TokenType::isValidKey ($this->normalizePhrase ($value)):
                $type = call_user_func([TokenType::class, $this->normalizePhrase ($value)]);
                break;
            case '*' === $value:
                $type = TokenType::S_ALL();
                break;
            case '=' === $value:
                $type = TokenType::S_EQ();
                break;
            case '>' === $value:
                $type = TokenType::S_GT();
                break;
            case '<' === $value:
                $type = TokenType::S_LT();
                break;
            case ',' === $value:
                $type = TokenType::S_COMMA();
                break;
            case '.' === $value:
                $type = TokenType::S_DOT();
                break;
            case is_string($value) && preg_match('/[a-z_]+/i', $value):
                $type = TokenType::S_IDENTIFIER();
                break;
            default:
                $type = TokenType::S_NONE();
        }
        return $type;
    }

    /**
     * Normalize value to token type key
     * 
     * @param $phrase
     * @return mixed
     */
    private function normalizePhrase ($phrase)
    {
        return sprintf ('T_%s', strtoupper ($phrase));
    }

}