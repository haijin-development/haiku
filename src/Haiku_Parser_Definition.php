<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Haiku\Parser\Parser_Definition;

class Haiku_Parser_Definition
{
    static public $definition;
}

Haiku_Parser_Definition::$definition = Create::a( Parser_Definition::class )->with()
    ->define_in_file( __DIR__ . "/haiku-definition.php" );
