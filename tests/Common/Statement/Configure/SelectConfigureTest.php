<?php

use Mev\ConsoleQueryTool\Common\Query\Lexer;
use Mev\ConsoleQueryTool\Common\Query\Statement\Configure\SelectConfigurator;
use Mev\ConsoleQueryTool\Common\Query\Statement\Select;
use Mev\ConsoleQueryTool\Common\Query\Statement\Where\Expression;
use Mev\ConsoleQueryTool\Common\Query\Statement\Order;

class SelectConfigureTest extends PHPUnit_Framework_TestCase
{
    /** @var  Select */
    private $select;
    
    protected function setUp()
    {
        $query = 'SELECT name, author, price FROM books WHERE price > 5 OR author = \'tom\' ORDER BY author DESC LIMIT 10, 100';
        
        $lexer = new Lexer();
        $selectConfigurator = new SelectConfigurator($lexer->aggregate($query)->getTokenCollection());
        
        $this->select = $selectConfigurator->configure (new Select ());
        
    }
    public function testConfigure() 
    {
       $this->assertInstanceOf(Select::class, $this->select);
    }

    public function testConfigureWhere()
    {
        $this->assertContainsOnly(Expression::class, $this->select->getWhere());
        $this->assertEquals($this->getExpectedWhere(), $this->select->getWhere ());
    }
    
    public function testSelectConfigure() 
    {
        $this->assertEquals ([
            'name', 
            'author',
            'price'
        ], array_keys($this->select->getSelect()));
    }

    public function testOrderConfigure()
    {
        $this->assertEquals ($this->getExpectedOrder(), $this->select->getOrder());
    }

    /**
     * @return array
     */
    private function getExpectedWhere()
    {
        return[
            $this->createExpression ('price', '>', 'OR', 5), 
            $this->createExpression ('author', '=', 'OR', 'tom')
        ];
    }

    /**
     * @return Order
     */
    private function getExpectedOrder()
    {
        return new Order('author', 'DESC');
    }
    
    private function createExpression($field, $operator, $logic, $value)
    {
        $expression = new Expression();
        $expression->setField($field);
        $expression->setLogicOperator($logic);
        $expression->setOperation($operator);
        $expression->setValue($value);
        
        return $expression;
    }
}