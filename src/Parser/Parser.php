<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Haiku\UnexpectedExpressionError;

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

        $this->evaluate_closure( $this->parser_definition->get_before_parsing_closure() );

        $this->char_index = 0;
        $this->line_index = 1;
        $this->column_index = 1;

        while( ! $this->at_eof() ) {

            $this->parse_next_expression();

        }

        return $this->evaluate_closure( $this->parser_definition->get_after_parsing_closure() );
    }

    protected function evaluate_closure($closure)
    {
        if( $closure === null ) {
            return $closure;
        }

        return $closure->call( $this );

    }

    /// Parsing

    protected function parse_next_expression()
    {

        foreach( $this->parser_definition->get_tokens()->to_array() as $token ) {

            $matched = $token->evaluate_on( $this );

            if( $matched ) {
                return;
            }
        }

        $this->raise_unexpected_expression_error();
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


    protected function raise_unexpected_expression_error()
    {
        $matches = [];

        preg_match( "/.*(?=\n?)/A", $this->string, $matches, 0, $this->char_index );

        throw new UnexpectedExpressionError( "Unexpected expression \"{$matches[0]}\". At line: {$this->line_index} column: {$this->column_index}." );
    }
}