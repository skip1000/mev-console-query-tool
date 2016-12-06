<?php
use Mev\ConsoleQueryTool\Command\QueryCommandResult;

class QueryCommandResultTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyResult()
    {
        $queryResult = new QueryCommandResult([]);
        $this->assertEquals($queryResult->getQueryResultPresentation(), 'Empty set' . PHP_EOL);
    }
}