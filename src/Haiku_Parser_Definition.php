<?php

namespace Haijin\Haiku;

use Haijin\Parser\Parser_Definition;

class Haiku_Parser_Definition
{
    static public $definition;
}

Haiku_Parser_Definition::$definition = ( new Parser_Definition() )
    ->define( function($parser) {

        require( __DIR__ . "/Grammar/haiku-grammar.php" );

    });
