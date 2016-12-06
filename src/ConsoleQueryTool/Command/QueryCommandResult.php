<?php

namespace Mev\ConsoleQueryTool\Command;


use LucidFrame\Console\ConsoleTable;

/**
 * Represent query result as console table
 *
 * @package Mev\ConsoleQueryTool\Command\Presenter
 */
class QueryCommandResult
{
    /**
     * Records list
     *
     * @var array
     */
    private $records = [];

    /**
     * @param $records
     */
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * Get table view
     *
     * @return string
     */
    public function getQueryResultPresentation()
    {
        if (empty ($this->records)) {
            return 'Empty set' . PHP_EOL;
        }
        return $this->buildConsoleTable();
    }

    /**
     * Build console table by records
     *
     * @return string
     */
    private function buildConsoleTable()
    {

        $table = new ConsoleTable();
        $table->setHeaders($this->getTableHeaders());
        
        foreach ($this->records as $record) {
            $table->addRow ($this->normalizeValues(array_values ((array) $record)));
        }
        return $table->getTable();
    }


    /**
     * Convert not scalar values to json
     *
     * @param $values
     * @return array
     */
    private function normalizeValues($values)
    {
        return array_map(function ($value) {

            return is_scalar($value)
                ? $value
                : json_encode($value);

        }, $values);

    }


    /**
     * Retrieve table headers from records
     * 
     * @return array
     */
    private function getTableHeaders ()
    {
        $headerRecord = current($this->records);
        
        return array_keys ((array) $headerRecord);
    }

}