<?php

namespace Mev\ConsoleQueryTool\Common\Query\Statement;

/**
 * Expression class for building order query by parts
 * 
 * @package Mev\ConsoleQueryTool\Common\Query\Statement
 * @author Oleg Kolomiets kolomiets.dev@gmail.com
 */
class Order
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $direction;

    /**
     * @param string $field
     * @param string $direction
     */
    public function __construct($field, $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
    }

    /**
     * Get order field name
     * 
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get order direction
     * 
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }
    
    
}