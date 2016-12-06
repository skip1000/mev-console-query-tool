<?php

namespace Mev\ConsoleQueryTool\Common\Query\Statement\Configure;

use Mev\ConsoleQueryTool\Common\Query\Lexer\TokenCollection;
use Mev\ConsoleQueryTool\Common\Query\Statement\Order;
use Mev\ConsoleQueryTool\Common\Query\Statement\Select;
use Mev\ConsoleQueryTool\Common\Query\Lexer\Token;
use Mev\ConsoleQueryTool\Common\Query\Lexer\TokenType;
use Mev\ConsoleQueryTool\Common\Query\Statement\StatementException;
use Mev\ConsoleQueryTool\Common\Query\Statement\Where\Expression;

/**
 * Select statement builder
 * Configure statement by token collection
 * 
 * @package Mev\ConsoleQueryTool\Common\Query\Statement\Configure
 */
class SelectConfigurator 
{
    /**
     * Tokens collection from query
     *
     * @var TokenCollection
     */
    private $tokenCollection;

    /**
     * Tokens iterator
     *
     * @var \ArrayIterator
     */
    private $tokenIterator;

    /**
     * Select statement to configure
     *
     * @var Select
     */
    private $selectStatement;

    /**
     * @param TokenCollection $tokenCollection
     */
    public function __construct(TokenCollection $tokenCollection) 
    {
        $this->tokenCollection = $tokenCollection;
        $this->tokenIterator = $tokenCollection->getIterator();
    }

    /**
     * Represent token collection as select statement
     *
     * @param Select $select
     * @throws StatementException
     * @return Select
     */
    public function configure(Select $select) 
    {
        $this->selectStatement = $select;
        $keyword = null;
        
        while ($this->tokenIterator->valid()) {
            /** @var Token $token */
            $token = $this->tokenIterator->current();

            
            if ($token->isKeyword()) {
                $keyword = $token->getType()->getValue ();
            }

            if ($keyword === TokenType::T_SELECT) {
                $this->configureSelectClause ();
            }

            if ($keyword === TokenType::T_FROM && $token->isTypeOf(TokenType::S_IDENTIFIER)) {
                $this->selectStatement->setFrom($token->getValue());
            }

            if ($keyword === TokenType::T_WHERE) {

                $this->configureWhereClause();
            }

            if ($keyword === TokenType::T_ORDER) {
                $this->configureOrderClause ();
            }

            if ($keyword === TokenType::T_LIMIT && $token->isTypeOf(TokenType::S_INTEGER)) {
                $this->configureLimitClause();
            }

            $this->tokenIterator->next();
        }
        
        return $select;
    }

    /**
     * Configure select clause
     * 
     * @return void
     */
    private function configureSelectClause ()
    {
        /** @var Token $token */
        $token = $this->tokenIterator->current();

        if ($token->isTypeOf(TokenType::S_ALL)) {

            if ($this->getPrev ()->isTypeOf(TokenType::S_DOT)) {
                $root = $this->tokenCollection->offsetGet($this->tokenIterator->key() - 2);
                
                $this->selectStatement->addSelect ($root->getValue(), $token->getValue());
            } else {
                
                $this->selectStatement->addSelect ($token->getValue());
            }
        }

        if ($token->isTypeOf(TokenType::S_IDENTIFIER)) {

            switch (true) {
                case $this->getNext()->isTypeOf(TokenType::S_DOT):
                    $this->selectStatement->addSelect ($token->getValue());
                    break;
                case $this->getPrev()->isTypeOf(TokenType::S_DOT):
                    $root = $this->tokenCollection->offsetGet($this->tokenIterator->key() - 2);
                    $this->selectStatement->addSelect ($root->getValue(), $token->getValue());
                    break;
                default:
                    $this->selectStatement->addSelect ($token->getValue());
                    break;
            }
        }

    }

    /**
     * Create Where clause of select statement
     * 
     * @throws StatementException
     */
    private function configureWhereClause ()
    {
        /** @var Token $token */
        $token = $this->tokenIterator->current();

        if ($token->isTypeOf(TokenType::S_DOT)) {
            throw new StatementException('Where statement does not support alias');
        }

        $operationTypes = [
            TokenType::S_EQ,
            TokenType::S_GT,
            TokenType::S_LT,
        ];

        if ($token->isTypeOf(TokenType::S_IDENTIFIER) && $this->getNext()->isTypeAnyOf($operationTypes)) {

            $expr = new Expression ();
            $expr->setField($token->getValue());

            $operation = $this->getNext ()->getValue ();

            /** @var Token $operationExtended */
            $operationExtended = $this->tokenCollection->offsetGet($this->tokenIterator->key() + 2);

            // Check for >= and <=
            $isGreaterLessOrEqual = $operationExtended->isTypeAnyOf([TokenType::S_EQ])
                && $this->getNext()->isTypeAnyOf([TokenType::S_GT, TokenType::S_LT]);

            $isNotEqualOperator = $operationExtended->isTypeOf(TokenType::S_GT)
                && $this->getNext()->isTypeOf(TokenType::S_LT);


            if ($isGreaterLessOrEqual || $isNotEqualOperator) {
                $operation .= $operationExtended->getValue();
            }

            $expr->setOperation($operation);
            
            switch (true) {
                case $this->getPrev()->isTypeOf(TokenType::T_WHERE):
                case $this->getPrev()->isTypeOf(TokenType::T_OR):
                    $logicOperator = 'OR';
                    break;
                case $this->getPrev()->isTypeOf(TokenType::T_AND):
                    $logicOperator = 'AND';
                    break;
                default:
                    throw new StatementException('Invalid logic operator: ' . $this->getPrev()->getValue());
            }

            $expr->setLogicOperator($logicOperator);

            $valueTokenOffset = $isGreaterLessOrEqual || $isNotEqualOperator ? 3: 2;
            /** @var Token $expressionValueToken */
            $expressionValueToken = $this->tokenCollection->offsetGet($this->tokenIterator->key() + $valueTokenOffset);

            if ($expressionValueToken->isTypeAnyOf([
                TokenType::S_STRING,
                TokenType::S_INTEGER,
                TokenType::S_FLOAT,
            ])) {

                $value = $expressionValueToken->isTypeOf(TokenType::S_STRING)
                    ? trim($expressionValueToken->getValue(), "'")
                    : $expressionValueToken->getValue();

                $expr->setValue($value);
            }

            if (!$expr->validate()) {

                $this->selectStatement->addWhere($expr);
            } else {
                throw new StatementException ('Invalid Expression');
            }

        }
    }

    /**
     * Create order clause of select statement
     * 
     * @throws StatementException
     * @return void
     */
    private function configureOrderClause()
    {
        /** @var Token $token */
        $token = $this->tokenIterator->current();

        if ($token->isTypeOf(TokenType::T_ORDER) && $this->getNext()->isTypeOf(TokenType::T_BY)) {

            $position = $this->tokenIterator->key();
            /** @var Token $field */
            $field = $this->tokenCollection->offsetGet($position + 2);

            /** @var Token $direction */
            $direction = $this->tokenCollection->offsetGet($position + 3);

            if ($field->isTypeOf(TokenType::S_IDENTIFIER) && $direction->isTypeAnyOf([TokenType::T_DESC, TokenType::T_ASC])) {
                $this->selectStatement->setOrder(new Order($field->getValue(), strtoupper($direction->getValue())));
            } else {
                throw new StatementException ('Invalid order clause');
            }
        }
    }

    /**
     * Create pagination of select statement 
     * 
     * @return void;
     */
    private function configureLimitClause() {
        /** @var Token $token */
        $token = $this->tokenIterator->current();

        if (is_null($this->selectStatement->getLimit())) {
            $this->selectStatement->setLimit($token->getValue());
        } else {
            $this->selectStatement->setOffset ($this->selectStatement->getLimit());
            $this->selectStatement->setLimit($token->getValue());
        }
    }

    /**
     * Get next token without cursor iteration
     * 
     * @return Token
     */
    private function getNext() {
        return $this->tokenCollection->offsetGet ($this->tokenIterator->key () + 1);
    }

    /**
     * Get previous token without cursor iteration
     * 
     * @return Token
     */
    private function getPrev() {
        return $this->tokenCollection->offsetGet ($this->tokenIterator->key () - 1);
    }
}