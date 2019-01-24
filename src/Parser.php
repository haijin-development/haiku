<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Parser
{
    protected $string;

    protected $lines;
    protected $line;
    protected $line_index;

    protected $indentation_unit;
    protected $current_indentation;

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

        $this->indentation_unit = null;
        $this->current_indentation = 0;

        $this->nodes = Create::an( Ordered_Collection::class )->with();

        $this->push_node(
            Create::a( Haiku_Document::class )->with()
        );

        while( ! $this->at_eof() ) {

            $this->line = $this->lines[ $this->line_index ];

            $this->parse_line();

            $this->line_index += 1;
        }

        return $this->nodes[0]->to_html();
    }

    protected function at_eof()
    {
        return $this->line_index >= count( $this->lines );
    }

    protected function parse_line()
    {
        $this->parse_tag( function($spaces, $tag) {

            $spaces_count = strlen( $spaces );

            if( $this->indentation_unit === null && $spaces_count > 0 ) {
                $this->indentation_unit = $spaces_count;
            }

            if( $spaces_count > 0 && $spaces_count % $this->indentation_unit != 0 ) {
                $this->raise_invalid_indentation_length_error(
                    $spaces_count, $this->indentation_unit
                );
            }

            $indentation = 0;
            if( $spaces_count > 0 ) {
                $indentation = $spaces_count / $this->indentation_unit;
            }

            $tag_node = Create::a( Haiku_Tag::class )->with( $tag );

            if( $indentation > $this->current_indentation + 1 ) {
                $this->raise_unexpected_identation_error();
            }

            if( $this->current_indentation == $indentation ) {

                $this->current_node()->add_child( $tag_node );

            }

            if( $indentation == $this->current_indentation + 1 ) {

                $this->push_node( $this->current_node()->last_child() );

                if( $this->current_node() === null ) {
                    var_dump( $this->nodes );
                }

                $this->current_node()->add_child( $tag_node );

            }

            if( $indentation < $this->current_indentation ) {

                for( $i = $this->current_indentation; $indentation < $i; $i-- ) {
                    $this->pop_node();
                }

                $this->current_node()->add_child( $tag_node );

            }

            $this->current_indentation = $indentation;

        });

        $this->parse_text( function($spaces, $expression) {

            $spaces_count = strlen( $spaces );

            if( $this->indentation_unit === null && $spaces_count > 0 ) {
                $this->indentation_unit = $spaces_count;
            }

            if( $spaces_count > 0 && $spaces_count % $this->indentation_unit != 0 ) {
                $this->raise_invalid_indentation_length_error(
                    $spaces_count, $this->indentation_unit
                );
            }

            $indentation = 0;
            if( $spaces_count > 0 ) {
                $indentation = $spaces_count / $this->indentation_unit;
            }

            $tag_node = Create::a( Haiku_PHP_Expression::class )
                ->with( trim( $expression ) );

            if( $indentation > $this->current_indentation + 1 ) {
                $this->raise_unexpected_identation_error();
            }

            if( $this->current_indentation == $indentation ) {

                $this->current_node()->add_child( $tag_node );

            }

            if( $indentation == $this->current_indentation + 1 ) {

                $this->push_node( $this->current_node()->last_child() );

                if( $this->current_node() === null ) {
                    var_dump( $this->nodes );
                }

                $this->current_node()->add_child( $tag_node );

            }

            if( $indentation < $this->current_indentation ) {

                for( $i = $this->current_indentation; $indentation < $i; $i-- ) {
                    $this->pop_node();
                }

                $this->current_node()->add_child( $tag_node );

            }

            $this->current_indentation = $indentation;

        });
    }

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

    protected function parse_tag($closure)
    {

        $matches = [];

        \preg_match( "/^(\s+)?([0-9a-zA-z_\-]+)$/", $this->line, $matches );

        if( empty( $matches ) ) {
            return;
        }

        $spaces = $matches[1];
        $tag = $matches[2];

        $closure->call( $this, $spaces, $tag );
    }

    protected function parse_text($closure)
    {

        $matches = [];

        \preg_match( "/^(\s+)?=(.+)$/", $this->line, $matches );

        if( empty( $matches ) ) {
            return;
        }

        $spaces = $matches[1];
        $expression = $matches[2];

        $closure->call( $this, $spaces, $expression );
    }
}