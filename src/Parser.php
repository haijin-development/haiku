<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Parser
{
    protected $string;

    protected $lines;
    protected $line_index;
    protected $line;
    protected $char_index;

    protected $indentation_char;
    protected $indentation_unit;
    protected $previous_indentation;
    protected $indentation;

    protected $nodes;

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

        $this->indentation_char = null;
        $this->indentation_unit = null;
        $this->previous_indentation = 0;
        $this->indentation = 0;

        $this->nodes = Create::an( Ordered_Collection::class )->with();

        $this->push_node(
            Create::a( Haiku_Document::class )->with()
        );

        while( ! $this->at_eof() ) {

            $this->line = $this->current_line();

            $this->parse_line();

            $this->line_index += 1;

        }

        return $this->nodes[0]->to_html();
    }

    /// Parsing

    protected function parse_line()
    {

        $this->char_index = 0;

        $this->parse_indentation( function($spaces) {

            $spaces_count = strlen( $spaces );

            if( $this->indentation_unit === null && $spaces_count > 0 ) {

                if( preg_match( "/\t/", $spaces ) &&  preg_match( "/ /", $spaces ) ) {
                    $this->raise_not_unique_indentation_char_error();
                }

                $this->indentation_unit = $spaces_count;
                $this->indentation_char = $spaces[ 0 ];

            }

            if( $this->indentation_char == " " && preg_match( "/\t/", $spaces ) ) {
                $this->raise_indentation_char_missmatch_error("spaces", "tabs");
            }
            if( $this->indentation_char == "\t" && preg_match( "/ /", $spaces ) ) {
                $this->raise_indentation_char_missmatch_error("tabs", "spaces");
            }

            if( $spaces_count > 0 && $spaces_count % $this->indentation_unit != 0 ) {
                $this->raise_unmatched_indentation_error(
                    $spaces_count, $this->indentation_unit
                );
            }

            $this->indentation = 0;

            if( $spaces_count > 0 ) {
                $this->indentation = $spaces_count / $this->indentation_unit;
            }

            if( $this->indentation > $this->previous_indentation + 1 ) {
                $this->raise_invalid_indentation_increment_error();
            }

        });

        $this->parse_tag( function($tag) {

            $tag_node = Create::a( Haiku_Tag::class )->with( $tag );

            if( $this->indentation == $this->previous_indentation ) {

                $this->current_node()->add_child( $tag_node );

            }

            if( $this->indentation == $this->previous_indentation + 1 ) {

                $this->push_node( $this->current_node()->last_child() );

                if( $this->current_node() === null ) {
                    var_dump( $this->nodes );
                }

                $this->current_node()->add_child( $tag_node );

            }

            if( $this->indentation < $this->previous_indentation ) {

                for( $i = $this->previous_indentation; $this->indentation < $i; $i-- ) {
                    $this->pop_node();
                }

                $this->current_node()->add_child( $tag_node );

            }

            $this->previous_indentation = $this->indentation;

        });

        $this->parse_text( function($expression) {

            $tag_node = Create::a( Haiku_PHP_Expression::class )
                ->with( trim( $expression ) );

            if( $this->indentation == $this->previous_indentation ) {

                $this->current_node()->add_child( $tag_node );

            }

            if( $this->indentation == $this->previous_indentation + 1 ) {

                $this->push_node( $this->current_node()->last_child() );

                if( $this->current_node() === null ) {
                    var_dump( $this->nodes );
                }

                $this->current_node()->add_child( $tag_node );

            }

            if( $this->indentation < $this->previous_indentation ) {

                for( $i = $this->previous_indentation; $this->indentation < $i; $i-- ) {
                    $this->pop_node();
                }

                $this->current_node()->add_child( $tag_node );

            }

            $this->previous_indentation = $this->indentation;

        });
    }

    /// Lexers

    protected function parse_indentation($closure)
    {
        $matches = [];

        \preg_match( "/(\s+)/A", $this->line, $matches, 0, $this->char_index );

        if( empty( $matches ) ) {
            return;
        }

        $spaces = $matches[1];

        $closure->call( $this, $spaces );

        $this->char_index += strlen($spaces);
    }

    protected function parse_tag($closure)
    {
        $matches = [];

        \preg_match( "/([0-9a-zA-z_\-]+)/A", $this->line, $matches, 0, $this->char_index );

        if( empty( $matches ) ) {
            return;
        }

        $tag = $matches[ 1 ];

        $closure->call( $this, $tag );

        $this->char_index += strlen($tag);
    }

    protected function parse_text($closure)
    {
        $matches = [];

        \preg_match( "/=(.+)/A", $this->line, $matches, 0, $this->char_index );

        if( empty( $matches ) ) {
            return;
        }

        $expression = $matches[ 1 ];

        $closure->call( $this, $expression );

        $this->char_index += strlen($expression);
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

    /// Stacking nodes

    protected function current_node()
    {
        return $this->nodes[ -1 ];
    }

    protected function push_node($node)
    {
        $this->nodes->add( $node );
    }

    protected function pop_node()
    {
        $this->nodes->remove_last();

        // To do: this copes with a bug in Ordered_Collection and with a bad design choice
        // in PHP, fix it later.
        $this->nodes = Create::an( Ordered_Collection::class )
            ->with( $this->nodes );
    }

    /// Raising errors

    protected function raise_unmatched_indentation_error($spaces_count, $unit)
    {
        if( $this->indentation_char == "\t" ) {
            $char = "tabs";
        } else {
            $char = "spaces";
        };

        throw Create::an( UnmatchedIndentationError::class )->with(
                "The template is using indentation units of {$unit} {$char}, but a line with {$spaces_count} {$char} was found. At line: {$this->line_index} column: {$this->char_index}."
            );
    }

    protected function raise_not_unique_indentation_char_error()
    {
        throw Create::an( NotUniqueIndentationCharError::class )->with(
                "The template is using both tabs and spaces to indent, use only tabs or only spaces. At line: {$this->line_index} column: {$this->char_index}." 
        );
    }

    protected function raise_indentation_char_missmatch_error($used_chars, $missmatched_chars)
    {
        throw Create::an( IndentationCharMissmatchError::class )->with(
                "The template is indenting with {$used_chars} in one line and {$missmatched_chars} in another one, use only tabs or only spaces in all lines. At line: {$this->line_index} column: {$this->char_index}." 
        );
    }

    protected function raise_invalid_indentation_increment_error()
    {
        throw Create::an( InvalidIndentationIncrementError::class )->with(
                "Invalid indentation was found. An increment of only one unit was expected. At line: {$this->line_index} column: {$this->char_index}." 
        );
    }

}