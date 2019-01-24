<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;

class Parser_Token
{
    protected $name;
    protected $pattern;
    protected $closure;

    /// Initializing

    public function __construct($name, $pattern, $closure)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->closure = $closure;
    }

    public function evaluate_on($parser)
    {
        $matches = [];

        \preg_match( $this->pattern, $parser->line, $matches, 0, $parser->char_index );

        if( empty( $matches ) ) {
            return;
        }

        $this->closure->call( $parser, $matches[1] );

        $parser->char_index += strlen( $matches[1] );
    }

}