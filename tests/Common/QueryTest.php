<?php

use Mev\ConsoleQueryTool\Common\Executor\MongoQuery;
use Mev\ConsoleQueryTool\Common\Query;
use Mev\ConsoleQueryTool\Common\Query\Statement\Select;
use Mev\ConsoleQueryTool\Common\Query\Lexer;

class QueryTest extends PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $queryAdapter = $this
            ->getMockBuilder(MongoQuery::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'select'
            ])
            ->getMock();

        $query = new Query($queryAdapter, new Lexer());

        $queryAdapter->expects($this->once())
            ->method('select')
            ->with($this->equalTo($this->methodExpectedObject()));
            
        $query->execute ('SELECT * FROM books');
    }

    /**
     * Expected object
     * @return Select
     */
    private function methodExpectedObject()
    {
        $select = new Select();
        $select->addSelect('*');
        $select->setFrom ('books');
        
        return $select;
    }
}