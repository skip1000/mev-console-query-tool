Console Query Tool
=======
[![Build Status](https://travis-ci.org/skip1000/mev-console-query-tool.svg?branch=master)](https://travis-ci.org/skip1000/mev-console-query-tool)


Application is a simple MongoDB client which works in console end executes SQL statement as native mongo queries.

----------


## Requirements ##

 - php 5.6 or greater
 - php-mongodb extension
 - unix compatible platform


----------


## Installation ##

    composer requires mev/console-query-tool


----------


## Usage ##

    php bin/app.php <command> <options> <query>

Available  commands:

    query:execute - Execute SQL query

Available  options:

    --host[=HOST]     MongoDB server host [default: "localhost"]
    --port[=PORT]     MongoDB server port [default: 27017]
    --db=DB           MongoDB database
    -h, --help        Display a help message

Example



    php bin/app.php query:execute --db=test "SELECT price, name FROM books WHERE price >=5"

----------


Supported statements:

 - SELECT (without subqueries)
 - WHERE
 - LIMIT
 - ORDER BY

Supported compare operators:

 - =  - equal
 - <>  - not equal
 - < - less than
 - > - greater than
 - >= - greater than or equal
 - <= - less than or equal

Supported logical operators:

 - AND
 - OR
