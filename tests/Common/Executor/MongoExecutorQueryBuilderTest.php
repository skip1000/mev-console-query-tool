<?php


class MongoExecutorQueryBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Mev\ConsoleQueryTool\Common\Executor\Mongo\QueryBuilder
     */
    private $queryBuilder;
    
    protected function setUp()
    {
        $this->queryBuilder = new \Mev\ConsoleQueryTool\Common\Executor\Mongo\QueryBuilder();
    }

    /**
     * 
     */
    public function testCreateFilterArguments()
    {
        $filter = $this->queryBuilder->createFilterArguments ($this->getWhereExpressions());
        $this->assertEquals($this->getExpectedFilter(), $filter);
    }
    
    public function testCreateOptionsArguments()
    {
        $select = new \Mev\ConsoleQueryTool\Common\Query\Statement\Select();
        $select->setOrder (new \Mev\ConsoleQueryTool\Common\Query\Statement\Order('price', 'ASC'));
        $select->addSelect('name');
        $select->addSelect('name2', 'field2');
        $select->setLimit(100);
        $select->setOffset(20);
        
        $options = $this->queryBuilder->createOptionsArguments($select);
        $expected = [
            'projection' => [
                '_id' => false,
                'name' => true,
                'name2.field2' => true
            ],
            'limit' => 100,
            'skip' => 20,
            'sort' => [
                'price' => 1
            ]
        ];
        
        $this->assertEquals($expected, $options);
    }

    /**
     * @return array
     */
    private function getWhereExpressions()
    {
        return[
            $this->createExpression ('price', '>', 'AND', 5),
            $this->createExpression ('author', '=', 'AND', 'tom'),
            $this->createExpression ('year', '<', 'AND', 2009),
            $this->createExpression ('price', '>=', 'OR', 5),
            $this->createExpression ('price', '<=', 'OR', 10)
        ];
    }

    /**
     * @return array
     */
    private function getExpectedFilter()
    {
        return [
            '$and' => [
                [
                    'price' => [
                        '$gt' => 5
                    ]
                ],
                [
                    'author' => [
                        '$eq' => 'tom'
                    ]
                ],
                [
                    'year' => [
                        '$lt' => 2009
                    ]
                ],
                
            ],
            '$or' => [
                [
                    'price' => [
                        '$gte' => 5
                    ]
                ],
                [
                    'price' => [
                        '$lte' => 10
                    ]
                ]
            ]
        ];
        
    }

    private function createExpression($field, $operator, $logic, $value)
    {
        $expression = new \Mev\ConsoleQueryTool\Common\Query\Statement\Where\Expression();
        $expression->setField($field);
        $expression->setLogicOperator($logic);
        $expression->setOperation($operator);
        $expression->setValue($value);

        return $expression;
    }

}