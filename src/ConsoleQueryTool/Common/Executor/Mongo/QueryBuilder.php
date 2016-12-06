<?php

namespace Mev\ConsoleQueryTool\Common\Executor\Mongo;

use Mev\ConsoleQueryTool\Common\Query\Statement\Select;
use Mev\ConsoleQueryTool\Common\Query\Statement\Where\Expression;

/**
 * Query builder for Mongo
 * Class QueryBuilder
 * @package Mev\ConsoleQueryTool\Common\Executor\Mongo
 */
class QueryBuilder
{
    
    /**
     * Create filter arguments by WHERE expressions
     * 
     * @param $expressions
     * @return array
     * @throws \Exception
     */
    public function createFilterArguments($expressions)
    {
        $filter = [];

        /**
         * @var Expression $expression
         */
        foreach ($expressions as $expression) {
            $logicOperator = $this->normalizeLogicOperation($expression->getLogicOperator());
            $compareOperator = $this->normalizeOperation($expression->getOperation());

            $expr = [
                $expression->getField() => [
                    $compareOperator => $expression->getValue()
                ]
            ];

            $filter[$logicOperator][] = $expr;
        }
        return $filter;
    }

    /**
     * Create options arguments by select statement
     * 
     * @param Select $select
     * @return array
     */
    public function createOptionsArguments(Select $select)
    {
        $limit = $select->getLimit()
            ? $select->getLimit()
            : 0;
        $skip = $select->getOffset()
            ? $select->getOffset()
            : 0;

        $options = [
            'projection' => $this->createProjection ($select->getSelect()),
            'limit' => $limit,
            'skip' => $skip,
        ];

        $orderClause = $select->getOrder ();

        if ($orderClause) {
            $direction = $orderClause->getDirection() === 'DESC' ? -1 : 1;
            $options['sort'] = [
                $orderClause->getField() => $direction
            ];
        }
        return $options;
    }

    /**
     * Create projection expression
     * 
     * @param $selectList
     * @return array
     */
    private function createProjection($selectList)
    {
        $projection = [];

        if ( array_key_exists('*', $selectList)) {
            return $projection;
        }

        /**
         * Exclude _id from results
         */
        $projection['_id'] = false;


        foreach ($selectList as $rootIdentifier => $subfields) {
            if (empty($subfields) || in_array('*', $subfields)) {
                $projection[$rootIdentifier] = true;
            } else {
                $projection += $this->createSubselectSet($rootIdentifier, $subfields);
            }
        }
        return $projection;
    }

    /**
     * Create projection from child field set
     * Input [a => [b,c]] 
     * Output [a.b, c.c]
     * 
     * @param $rootIdentifier
     * @param $subfields
     * @return array
     */
    private function createSubselectSet ($rootIdentifier, $subfields)
    {
        $fieldSet = array_map(function ($field) use ($rootIdentifier) {
            return sprintf('%s.%s', $rootIdentifier, $field);
        }, $subfields);

        return array_fill_keys($fieldSet, true);

    }

    /**
     * Normalize bool operation to Mongo syntax
     * 
     * @param $operation
     * @return string
     * @throws \Exception
     */
    private function normalizeLogicOperation ($operation)
    {
        $operation = strtolower ($operation);
        switch ($operation) {
            case 'or':
                return '$or';
                break;
            case 'and':
                return '$and';
                break;

            default:
                throw new \Exception('Invalid logic operator ' . $operation);
        }

    }


    /**
     * Normalize compare operation to Mongo syntax
     * 
     * @param $operation
     * @return string
     * @throws \Exception
     */
    private function normalizeOperation($operation)
    {
        switch ($operation) {
            case '<>':
                return '$ne';
                break;
            case '=':
                return '$eq';
                break;
            case '>':
                return '$gt';
                break;
            case '<':
                return '$lt';
                break;
            case '>=':
                return '$gte';
                break;
            case '<=':
                return '$lte';
                break;
            default:
                throw new \Exception('Invalid operator ' . $operation);
        }

    }

    /**
     * Create query path by db name and collection
     * 
     * @param string $database
     * @param string $collection
     * @return mixed
     */
    public function getCollectionPath($database, $collection) {
        return join('.', [$database, $collection]);
    }
}