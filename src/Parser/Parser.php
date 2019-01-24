<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Parser
{
    public $string;
    public $char_index;
    public $line_index;
    public $column_index;

    /// Initializing

    public function __construct($parser_definition)
    {
        $this->string = null;
        $this->char_index = null;
        $this->line_index = 1;
        $this->column_index = 1;

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

        $this->parser_definition->get_before_parsing_closure()->call( $this );

        $this->char_index = 0;
        $this->line_index = 1;
        $this->column_index = 1;

        while( ! $this->at_eof() ) {

            $this->parse_next_expression();

        }

        return $this->parser_definition->get_after_parsing_closure()->call( $this );
    }

    /// Parsing

    protected function parse_next_expression()
    {

        $this->parser_definition->get_tokens()->each_do( function($token) {

            $token->evaluate_on( $this );

        }, $this );

    }

    public function new_line()
    {
        $this->line_index += 1;
        $this->column_index = 1;
    }

    /// Querying string buffer

    protected function at_eof()
    {
        return $this->char_index >= strlen( $this->string );
    }

}