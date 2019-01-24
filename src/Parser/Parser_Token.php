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

        \preg_match( $this->pattern, $parser->string, $matches, 0, $parser->char_index );

        if( empty( $matches ) ) {
            return;
        }

        $matched_line = $matches[0];
        $matching_length = strlen( $matched_line );
        $token = $matches[1];

        $this->closure->call( $parser, $matches[1] );

        $parser->char_index += $matching_length;

        if( $matched_line[ strlen( $matched_line ) - 1 ] == "\n" ) {
            $parser->new_line();
        } else {
            $parser->column_index += strlen( $matching_length );

        }

    }

}