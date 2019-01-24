<?php

namespace Haijin\Haiku;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

$parser->before_parsing( function() {

    $this->indentation_char = null;
    $this->indentation_unit = null;
    $this->previous_indentation = 0;
    $this->indentation = 0;

    $this->nodes = Create::an( Ordered_Collection::class )->with();

    $this->push_node(
        Create::a( Haiku_Document::class )->with()
    );

});

$parser->after_parsing( function() {

    return $this->nodes[0]->to_html();

});

$parser->token( "indentation", "/(\s+)/A", function($spaces) {

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

$parser->token( "tag", "/([0-9a-zA-z_\-]+)/A", function($tag) {

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

$parser->token( "text", "/=(.+)/A", function($expression) {

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
