<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

/*
 * Regex cheatsheet:
 *      group without capturing: (?:)
 *      lookahead assertion: (?=)
 *      lookahead negation: (?!)
 */


$parser->expression( "root",  function() {

    $this->matcher( function() {

        $this->exp( "lines-list" );

    });

    $this->handler( function($nodes_list) {

        $document = Create::a( Haiku_Document::class )->with();

        $nodes_list->each_do( function($node) use($document) {

            $document->add_child( $node );

        });

        return $document->to_html();

    });

});

$parser->expression( "lines-list",  function() {

    $this->matcher( function() {

        $this ->exp( "line" ) ->exp( "lines-list" )
        ->or()
        ->exp( "line" );

    });

    $this->handler( function($node, $nodes_list = null) {

        $nodes = Create::an( Ordered_Collection::class )->with();

        if( $nodes_list === null ) {

            $nodes->add( $node );

            return $nodes;
        }

        $previous_line_node = $nodes_list->first();

        if( $node->indentation == $previous_line_node->indentation ) {

            $nodes->add( $node );

            $nodes_list->each_do( function($each_node) use($node, $nodes) {

                if( $node->indentation != $each_node->indentation ) {
                    throw new \Exception( "Invalid indentation found" );
                }

                $nodes->add( $each_node );

            });

            return $nodes;

        }

        if( $node->indentation < $previous_line_node->indentation ) {

            $nodes->add( $node );

            $nodes_list->each_do( function($each_node) use($node, $nodes) {

                if( $node->indentation < $each_node->indentation - 1 ) {
                    $this->raise_invalid_indentation_increment_error();
                }

                if( $node->indentation == $each_node->indentation - 1 ) {
                    $node->add_child( $each_node );
                }

                if( $node->indentation == $each_node->indentation ) {
                    $nodes->add( $each_node );
                }

                if( $node->indentation > $each_node->indentation ) {
                    throw new \Exception( "Invalid indentation found" );
                }

            }, $this );

            return $nodes;

        }

        if( $node->indentation > $previous_line_node->indentation ) {

            $nodes->add( $node );

            $nodes_list->each_do( function($each_node) use($node, $nodes) {

                if( $node->indentation == $each_node->indentation ) {
                    throw new \Exception( "Invalid indentation found" );
                }

                if( $node->indentation < $each_node->indentation ) {
                    throw new \Exception( "Invalid indentation found" );
                }

                $nodes->add( $each_node );

            });

            return $nodes;

        }

    });

});

$parser->expression( "line",  function() {

    $this->matcher( function() {

        $this ->exp( "indentation" ) ->exp( "tag" ) ->lit( "\n")
        ->or()
        ->exp( "indentation" ) ->exp( "tag" );

    });

    $this->handler( function($indentation, $tag_node) {

        $tag_node->indentation = $indentation;

        return $tag_node;
    });

});

$parser->expression( "indentation",  function() {

    $this->matcher( function() {

        $this-> regex( "/((?: |\t)*)(?! |\t)/" );

    });

    $this->handler( function($spaces) {

        if( preg_match( "/\t/", $spaces ) &&  preg_match( "/ /", $spaces ) ) {
            $this->raise_not_unique_indentation_char_error();
        }

        $spaces_count = strlen( $spaces );

        if( $this->indentation_unit == null && $spaces_count > 0 ) {

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

        if( $spaces_count == 0 ) {
            $indentation = 0;
        } else {
            $indentation = $spaces_count / $this->indentation_unit;
        }

        return $indentation;

    });

});

$parser->expression( "tag",  function() {

    $this->matcher( function() {

        $this-> regex( "([0-9a-zA-z_\-]+)" );

    });

    $this->handler( function($tag_string) {

        return Create::a( Haiku_Tag::class )->with( $tag_string );

    });

});
