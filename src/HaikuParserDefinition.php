<?php

namespace Haijin\Haiku;

use Haijin\Parser\ParserDefinition;

class HaikuParserDefinition
{
    static public $definition;
}

HaikuParserDefinition::$definition = (new ParserDefinition())
    ->define(function ($parser) {

        require(__DIR__ . "/Grammar/haiku-grammar.php");

    });
