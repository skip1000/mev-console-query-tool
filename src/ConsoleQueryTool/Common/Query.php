<?php
namespace Mev\ConsoleQueryTool\Common;


use Mev\ConsoleQueryTool\Common\Executor\MongoQuery;
use Mev\ConsoleQueryTool\Common\Query\Lexer;
use Mev\ConsoleQueryTool\Common\Query\Statement\Configure\SelectConfigurator;
use Mev\ConsoleQueryTool\Common\Query\Statement\Select;

/**
 * Class Query
 * @package Mev\ConsoleQueryTool\Common
 */
class Query
{
    /**
     * @var MongoQuery
     */
    private $queryAdapter;

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * Query constructor.
     * @param MongoQuery $queryAdapter
     * @param Lexer $lexer
     */
    public function __construct(MongoQuery $queryAdapter, Lexer $lexer)
    {
        $this->queryAdapter = $queryAdapter;
        $this->lexer = $lexer;
    }

    /**
     * Execute query
     * 
     * @param $query
     * @return \MongoDB\Driver\Cursor
     */
    public function execute ($query)
    {
        $configurator = $this
            ->getSelectConfigurator($this->aggregateQueryTokenCollection($query));

        $select = $configurator->configure(new Select());
        
        return $this->queryAdapter->select($select);
    }

    /**
     * Create select configurator
     * 
     * @param Lexer\TokenCollection $tokenCollection
     * @return SelectConfigurator
     */
    private function getSelectConfigurator(Lexer\TokenCollection $tokenCollection)
    {
        return new SelectConfigurator ($tokenCollection);

    }

    /**
     * Aggregate tokens from query string
     * @param $query
     * @return Lexer\TokenCollection
     */
    private function aggregateQueryTokenCollection($query)
    {
        return $this->lexer->aggregate($query)->getTokenCollection();
    }
}