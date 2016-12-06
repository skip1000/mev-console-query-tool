<?php

namespace Mev\ConsoleQueryTool\Command;


use Mev\ConsoleQueryTool\Common\Executor\MongoQuery;
use Mev\ConsoleQueryTool\Common\Query\Lexer;
use Mev\ConsoleQueryTool\Common\Query;
use MongoDB\Driver\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class provide console application console commands
 * 
 * @package Mev\ConsoleQueryTool\Command
 */
class QueryCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('query:execute')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'MongoDB server host', 'localhost')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'MongoDB server port', 27017)
            ->addOption('db', null, InputOption::VALUE_REQUIRED, 'MongoDB database')
            ->addArgument('query', InputArgument::REQUIRED, 'SQL query to execute');
        
    }

    /**
     * Main application command
     * Execute SQL statement as mongo query
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Mev\ConsoleQueryTool\Common\Query\Statement\StatementException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mongoDatabase = $input->getOption('db');
        $mongoHost = $input->getOption('host');
        $mongoPort = $input->getOption('port');
        $query = $input->getArgument('query');

        $queryService = $this
            ->createQueryService ($mongoHost, $mongoPort, $mongoDatabase);
        
        $records = $queryService->execute($query); 

        $result = new QueryCommandResult($records->toArray());

        // Send output to std
        $output->write($result->getQueryResultPresentation());
    }

    /**
     * Create query service with 
     * Lexer - SQL parser
     * queryAdapter - execute mongo query
     * 
     * 
     * @todo Move to DI
     * @param $mongoHost
     * @param $mongoPort
     * @param $mongoDatabase
     * @return Query
     */
    private function createQueryService($mongoHost, $mongoPort, $mongoDatabase)
    {
        $connectionUri = sprintf('mongodb://%s:%s', $mongoHost, $mongoPort);
        
        $manager = new Manager($connectionUri);
        $queryAdapter = new MongoQuery($manager, $mongoDatabase);

        return new Query ($queryAdapter, new Lexer ());
    }
}