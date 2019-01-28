<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;

class Expression_Particle
{
    protected $expression_name;

    /// Initializing

    public function __construct($expression_name)
    {
        $this->expression_name = $expression_name;
    }

    /// Accessing

    public function get_expression_name()
    {
        return $this->expression_name;
    }

    /// Parsing

    public function parse_with( $parser )
    {
        return $parser->parse_expression( $this );
    }
}