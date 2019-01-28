<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Haiku\UnexpectedExpressionError;

class Parser
{
    public $string;
    public $string_length;

    public $char_index;
    public $line_index;
    public $column_index;

    /// Initializing

    public function __construct($parser_definition)
    {
        $this->string = null;
        $this->string_length = 0;

        $this->context_frame = $this->new_context_frame();
        $this->frames_stack = Create::an( Ordered_Collection::class )->with();

        $this->parser_definition = $parser_definition;
    }

    protected function new_context_frame()
    {
        $context_frame = new \stdclass();

        $context_frame->char_index = null;
        $context_frame->line_index = 1;
        $context_frame->column_index = 1;

        $context_frame->current_expression = null;
        $context_frame->expected_particles =
            Create::an( Ordered_Collection::class )->with();

        $context_frame->matched_length = 0;
        $context_frame->matched_cr = false;
        $context_frame->handler_params = [];
        $context_frame->result = null;

        return $context_frame;
    }

    /// Parsing

    public function parse($file)
    {
        return $this->parse_string( \file_get_contents( $file ) );
    }

    public function parse_string($string)
    {
        $this->string = $string;
        $this->string_length = strlen( $this->string );

        $this->context_frame->char_index = 0;
        $this->context_frame->line_index = 1;
        $this->context_frame->column_index = 1;

        $this->begin_expression( "root" );

        $this->do_parsing_loop();

        return $this->context_frame->handler_params[ 0 ];
    }



    /// Expressions

    protected function get_expression_named($expression_name)
    {
        return $this->parser_definition->get_expression_named( $expression_name );
    }

    protected function begin_expression($expression_name)
    {
        $this->frames_stack->add( $this->context_frame );

        $this->context_frame = clone $this->context_frame;

        $this->context_frame->current_expression =
            $this->get_expression_named( $expression_name );

        $this->context_frame->expected_particles =
            $this->context_frame->current_expression->get_particles();

        $this->context_frame->expected_particles->add(
            Create::an( End_Of_Expression_Particle::class )->with()
        );
    }

    protected function end_matched_expression()
    {
        $current_context = $this->context_frame;

        $this->context_frame = $this->frames_stack->remove_last();

        $this->context_frame->char_index = $current_context->char_index;
        $this->context_frame->line_index = $current_context->line_index;
        $this->context_frame->column_index = $current_context->column_index;

        $this->context_frame->handler_params[] = $current_context->result;

    }

    /// Parsing

    protected function do_parsing_loop()
    {
        while( ! $this->at_eof() ) {

            if( $this->context_frame->current_expression === null ) {
                $this->raise_unexpected_expression_error();
            }

            $this->parse_current_expression();

        }
    }

    protected function parse_current_expression()
    {
        $this->context_frame->handler_params = [];
        $this->context_frame->result = null;

        while( $this->context_frame->expected_particles->not_empty() )
        {
            $particle = $this->context_frame->expected_particles->remove_at( 0 );

            $parsed = $this->parse_particle( $particle );

            if( ! $parsed ) {
                $this->raise_unexpected_expression_error();
            }

        }

    }

    public function parse_particle($particle)
    {
        $this->context_frame->matched_length = 0;
        $this->context_frame->matched_cr = false;

        $parsed = $particle->parse_with( $this );

        if( ! $parsed ) {
            return false ;
        }

        $this->update_stream_position();

        return true;
    }

    /// Particle methods

    public function parse_regex($regex_particle)
    {
        $matches = [];

        \preg_match(
            $regex_particle->get_regex_string() . "A",
            $this->string,
            $matches,
            0,
            $this->context_frame->char_index
        );

        if( empty( $matches ) ) {
            return false;
        }

        $this->context_frame->matched_length = strlen( $matches[ 0 ] );

        $this->context_frame->handler_params[] = $matches[ 1 ];

        if( $matches[ 0 ][ $this->context_frame->matched_length - 1 ] == "\n" ) {

            $this->context_frame->matched_cr = true;

        }

        return true;
    }

    public function parse_multiple_regex($regex_particle)
    {
        $matches = [];

        \preg_match(
            $regex_particle->get_regex_string() . "A",
            $this->string,
            $matches,
            0,
            $this->context_frame->char_index
        );

        if( empty( $matches ) ) {
            return false;
        }

        $this->context_frame->matched_length = strlen( $matches[ 0 ] );

        $this->context_frame->handler_params[] = array_slice( $matches, 1 );

        if( $matches[ 0 ][ $this->context_frame->matched_length - 1 ] == "\n" ) {

            $this->context_frame->matched_cr = true;

        }

        return true;
    }

    public function parse_expression($expression_particle)
    {
        $n = $expression_particle->get_expression_name();

        $this->begin_expression( $expression_particle->get_expression_name() );

        return true;
    }

    public function parse_end_of_expression($end_of_expression_particle)
    {
        $handler_result = $this->context_frame->current_expression->get_handler_closure()
            ->call( $this, ...$this->context_frame->handler_params );

        $this->context_frame->result = $handler_result;

        $this->end_matched_expression();

        return true;
    }

    /// Stream methods

    protected function at_eof()
    {
        return $this->context_frame->char_index >= $this->string_length;
    }

    protected function update_stream_position()
    {
        $this->increment_char_index_by( $this->context_frame->matched_length );

        if( $this->context_frame->matched_cr ) {

            $this->new_line();

        } else {

            $this->increment_column_index_by( $this->context_frame->matched_length );

        }        
    }

    public function new_line()
    {
        $this->context_frame->line_index += 1;
        $this->context_frame->column_index = 1;
    }

    public function increment_char_index_by( $n )
    {
        $this->context_frame->char_index += $n;
    }

    public function increment_column_index_by( $n )
    {
        $this->context_frame->column_index += $n;
    }

    /// Raising errors

    protected function raise_unexpected_expression_error()
    {
        $matches = [];

        preg_match(
            "/.*(?=\n?)/A",
            $this->string,
            $matches,
            0,
            $this->context_frame->char_index
        );

        throw new UnexpectedExpressionError( "Unexpected expression \"{$matches[0]}\". At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}." );
    }
}