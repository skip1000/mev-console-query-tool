<?php

namespace Mev\ConsoleQueryTool\Common\Query\Lexer;


use MyCLabs\Enum\Enum;

/**
 * Include all supported SQL tokens types
 * 
 * @package Mev\ConsoleQueryTool\Common\Query\Lexer
 */
class TokenType extends Enum
{
    
    const S_NONE = 0;
    const S_EQ = 1;
    const S_GT = 3;
    const S_LT = 4;
    const S_DOT = 7;
    const S_COMMA = 8;
    const S_ALL = 9;
    const S_STRING = 10;
    const S_INTEGER = 11;
    const S_FLOAT = 12;
    const S_IDENTIFIER = 13;
    
    const T_SELECT = 100;
    const T_FROM = 101;
    const T_WHERE = 102;
    const T_ORDER = 103;
    const T_OR = 105;
    const T_BY = 106;
    const T_AND = 107;
    const T_ASC = 108;
    const T_DESC = 109;
    const T_LIMIT = 110;
}