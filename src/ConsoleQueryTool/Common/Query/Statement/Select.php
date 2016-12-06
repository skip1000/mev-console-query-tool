<?php

namespace Mev\ConsoleQueryTool\Common\Query\Statement;


use Mev\ConsoleQueryTool\Common\Query\Statement\Where\Expression;

/**
 * Expression class for building select query by parts
 * 
 * @package Mev\ConsoleQueryTool\Common\Query\Statement
 */
class Select implements StatementInterface
{
    /**
     * List of fields to retrieve
     * 
     * @var array
     */
    private $select = [];

    /**
     * Identifier of from statement
     * 
     * @var string
     */
    private $from = null;

    /**
     * List of query filter expressions
     * 
     * @var Expression[]
     */
    private $where = [];

    /**
     * Order statement
     * 
     * @var Order|null
     */
    private $order;

    /**
     * Skip elements
     * 
     * @var null|int
     */
    private $offset = null;

    /**
     * Max records to retrieve
     * 
     * @var null|int
     */
    private $limit = null;

    /**
     * @return array
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Add element to select list
     * 
     * @param $root         Root selection
     * @param null $field   Subfield of root selection   
     */
    public function addSelect($root, $field = null) {
        
        if(!isset($this->select[$root])) {
            $this->select[$root] = [];
        }
        
        if(!is_null($field)) {
            $this->select[$root][] = $field; 
        }
    }

    /**
     * @param array $select
     */
    public function setSelect($select)
    {
        $this->select = $select;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return Where\Expression[]
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param Where\Expression[] $where
     */
    public function setWhere($where)
    {
        $this->where = $where;
    }

    /**
     * @param Where\Expression $where
     */
    public function addWhere($where)
    {
        $this->where[] = $where;
    }

    /**
     * @return Order|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
}