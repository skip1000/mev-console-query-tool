<?php

namespace Mev\ConsoleQueryTool\Common\Query\Statement\Where;

/**
 * Expression class for building WHERE query by parts
 * 
 */
class Expression
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operation;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Logic operator OR or AND
     * 
     * @var string  
     */
    private $logicOperator;

    /**
     * Validate query expression
     * 
     * @return bool
     */
    public function validate ()
    {
        return empty ($this->field) || empty($this->operation) || empty ($this->value) || empty($this->logicOperator);
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param mixed $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getLogicOperator()
    {
        return $this->logicOperator;
    }

    /**
     * @param mixed $logicOperator
     */
    public function setLogicOperator($logicOperator)
    {
        $this->logicOperator = $logicOperator;
    }
}