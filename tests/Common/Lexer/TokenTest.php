<?php


use Mev\ConsoleQueryTool\Common\Query\Lexer\Token;
use Mev\ConsoleQueryTool\Common\Query\Lexer\TokenType;

class TokenTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider getTokenDataProvider
     */
    public function testTypeDetection(Token $token, $typeOf, $anyTypeOf, $isKeyword, $isTypeOf, $isAnyTypeOf)
    {
        $this->assertEquals ($token->isKeyword(), $isKeyword);
        $this->assertEquals ($token->isTypeAnyOf($anyTypeOf), $isAnyTypeOf);
        $this->assertEquals ($token->isTypeOf($typeOf), $isTypeOf);
    }

    /**
     * @dataProvider normalizeScalarDataProvider
     */
    public function testNormalizeScalar(TokenType $type, $value)
    {
        $token  = new Token ($type, $value);
        
        switch ($type->getValue()) {
            case TokenType::S_INTEGER:
                $this->assertTrue(is_int($token->getValue()));
                break;
            case TokenType::S_STRING:
                $this->assertTrue(is_string($token->getValue()));
                break;
            case TokenType::S_FLOAT:
                $this->assertTrue(is_float($token->getValue()));
                break;
        }
    }

    /**
     * @return array
     */
    public function getTokenDataProvider()
    {
        return [
            [
                new Token(TokenType::S_STRING(), 'test'),
                TokenType::S_DOT,
                [TokenType::S_DOT, TokenType::S_STRING],
                false,
                false,
                true,
            ],
            [
                new Token(TokenType::T_SELECT(), 'test'),
                TokenType::T_SELECT,
                [TokenType::S_DOT, TokenType::T_WHERE],
                true,
                true,
                false
            ]
        ];
    }

    /**
     * @return array
     */
    public function normalizeScalarDataProvider() {
        return [
            [
                TokenType::S_FLOAT(),
                '23.3',
            ],
            [
                TokenType::S_STRING(),
                "'string'",
            ],
            [
                TokenType::S_INTEGER(),
                '23',
            ]
        ];
    }
    

}