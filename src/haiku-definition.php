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


$parser->token( "cr", "/(\n)/A", function($cr) {
    $this->indentation = 0;
});


// Match spaces and tabs.
$parser->token( "indentation", "/((?: |\t)+)(?! |\t)/A", function($spaces) {

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

// Match tag names.
$parser->token( "tag", "/([0-9a-zA-z_\-]+)(?![0-9a-zA-z_\-])/A", function($tag) {

    $tag_node = Create::a( Haiku_Tag::class )->with( $tag );

    $this->adjust_nodes_to_indentation( $tag_node );


});

// Match '='' followed by a PHP expression.
$parser->token( "text", "/=(.+)(?=\n)/A", function($expression) {

    $php_expression_node = Create::a( Haiku_PHP_Expression::class )
        ->with( trim( $expression ) );

    $this->adjust_nodes_to_indentation( $php_expression_node );

});
