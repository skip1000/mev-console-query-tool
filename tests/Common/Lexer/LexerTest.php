<?php

use Mev\ConsoleQueryTool\Common\Query\Lexer;

class LexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider tokenTypeDataProvider
     * @param $query
     * @param $expectedTokenType
     */
    public function testGetTokenType($query, $expectedTokenType) 
    {
        $lexer = new Lexer();

        /**
         * @var Lexer\Token $firstToken
         */
        $firstToken = $lexer
            ->aggregate($query)
            ->getTokenCollection()
            ->offsetGet(0);
        
        $this->assertTrue($firstToken->isTypeOf($expectedTokenType));
    }
    
    public function tokenTypeDataProvider()
    {
        return [
            [
                'SELECT',
                Lexer\TokenType::T_SELECT,
            ],
            [
                'FROM',
                Lexer\TokenType::T_FROM,
            ],
            [
                'WHERE',
                Lexer\TokenType::T_WHERE,
            ],
            [
                'ORDER',
                Lexer\TokenType::T_ORDER,
            ],
            [
                'LIMIT',
                Lexer\TokenType::T_LIMIT,
            ],
            [
                'somefield',
                Lexer\TokenType::S_IDENTIFIER,
            ],
            [
                "'str'",
                Lexer\TokenType::S_STRING,
            ],
            [
                "10",
                Lexer\TokenType::S_INTEGER,
            ],
            [
                "10.55",
                Lexer\TokenType::S_FLOAT,
            ],
            [
                '=',
                Lexer\TokenType::S_EQ,
            ],
            [
                '>',
                Lexer\TokenType::S_GT,
            ],
            [
                '<',
                Lexer\TokenType::S_LT,
            ],
            [
                '.',
                Lexer\TokenType::S_DOT,
            ],
            [
                ',',
                Lexer\TokenType::S_COMMA,
            ],
            [
                '*',
                Lexer\TokenType::S_ALL,
            ],
            [
                'or',
                Lexer\TokenType::T_OR,
            ],
            [
                'and',
                Lexer\TokenType::T_AND,
            ],
            [
                'asc',
                Lexer\TokenType::T_ASC,
            ],
            [
                'desc',
                Lexer\TokenType::T_DESC,
            ],
        ];
        
    }
        

}