<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Parser
{
    protected $string;

    protected $lines;
    public $line_index;
    public $line;
    public $char_index;

    /// Initializing

    public function __construct($parser_definition)
    {
        $this->parser_definition = $parser_definition;
    }

    /// Parsing

    public function parse($file)
    {
        return $this->parse_string( \file_get_contents( $file ) );
    }

    public function parse_string($string)
    {
        $this->string = $string;

        $this->lines = explode( "\n", $this->string );
        $this->line_index = 0;
        $this->line = null;
        $this->char_index = 0;

        $this->parser_definition->get_before_parsing_closure()->call( $this );

        while( ! $this->at_eof() ) {

            $this->line = $this->current_line();

            $this->parse_line();

            $this->line_index += 1;

        }

        return $this->parser_definition->get_after_parsing_closure()->call( $this );
    }

    /// Parsing

    protected function parse_line()
    {

        $this->char_index = 0;

        $this->parser_definition->get_tokens()->each_do( function($token) {

            $token->evaluate_on( $this );

        }, $this );

    }

    /// Querying string buffer

    protected function at_eof()
    {
        return $this->line_index >= count( $this->lines );
    }

    protected function current_line()
    {
        return $this->lines[ $this->line_index ];
    }

}