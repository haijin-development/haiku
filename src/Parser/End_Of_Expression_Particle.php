<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;

class End_Of_Expression_Particle
{
    /// Parsing

    public function parse_with( $parser )
    {
        return $parser->parse_end_of_expression( $this );
    }

    /// Printing

    public function print_string()
    {
        return "eoe";
    }

}