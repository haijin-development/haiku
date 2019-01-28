<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;

class Literal_Particle
{
    protected $literal_string;

    /// Initializing

    public function __construct($literal_string)
    {
        $this->literal_string = $literal_string;
    }

    /// Accessing

    public function get_literal_string()
    {
        return $this->literal_string;
    }

    /// Parsing

    public function parse_with( $parser )
    {
        return $parser->parse_literal( $this );
    }

    /// Printing

    public function print_string()
    {
        return "exp('$this->literal_string')";
    }

}