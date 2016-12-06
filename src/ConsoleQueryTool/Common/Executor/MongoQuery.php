<?php

namespace Mev\ConsoleQueryTool\Common\Executor;


use Mev\ConsoleQueryTool\Common\Executor\Mongo\QueryBuilder;
use Mev\ConsoleQueryTool\Common\Query\Statement\Select;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

/**
 * Create mongo query from Select statement
 * 
 * @package Mev\ConsoleQueryTool\Common\Executor
 */
class MongoQuery implements SelectAwareInterface
{
    /**
     * @var Manager
     */
    private $manager;

    private $database;

    private $queryBuilder;

    /**
     * MongoDB constructor.
     * @param Manager $manager
     * @param $database
     */
    public function __construct (Manager $manager , $database)
    {
        $this->manager = $manager;
        $this->database = $database;
        $this->queryBuilder = new QueryBuilder ();
    }

    /**
     * Execute select query by Select statement
     * 
     * @param Select $select
     * @return Cursor
     */
    public function select(Select $select)
    {
        $query = new Query ($this->queryBuilder->createFilterArguments($select->getWhere()), $this->queryBuilder->createOptionsArguments ($select));

        $cursor = $this
            ->manager
            ->executeQuery($this->queryBuilder->getCollectionPath($this->database, $select->getFrom()), $query);
        
        return $cursor;
    }
}