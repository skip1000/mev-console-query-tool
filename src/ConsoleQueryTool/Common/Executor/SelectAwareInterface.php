<?php

namespace Mev\ConsoleQueryTool\Common\Executor;


use Mev\ConsoleQueryTool\Common\Query\Statement\Select;


interface SelectAwareInterface
{
    /**
     * Build query and retrieve data by Select statement
     * 
     * @param Select $select
     * @return mixed
     */
    public function select (Select $select);
}