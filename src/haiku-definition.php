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

    $this->indentation_unit = null;
    $this->indentation_char = null;

});

/// Root

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


/// Lines

$parser->expression( "lines-list",  function() {

    $this->matcher( function() {

        $this ->exp( "line" ) ->cr() ->exp( "lines-list" )
        ->or()
        ->exp( "line" ) ->cr()
        ->or()
        ->exp( "line" );

    });

    $this->handler( function($node, $nodes_list = null) {

        if( $node === null ) {
            return $nodes_list;
        }

        $nodes = Create::an( Ordered_Collection::class )->with();

        if( $nodes_list === null ) {

            $nodes->add( $node );

            return $nodes;
        }

        $previous_line_node = $nodes_list->first();

        if( $node->indentation == $previous_line_node->indentation ) {

            $nodes->add( $node );

            $nodes_list->each_do( function($each_node) use($node, $nodes) {

                if( $node->indentation < $each_node->indentation ) {
                    $this->raise_unexpected_expression_error();
                }

                $nodes->add( $each_node );

            }, $this );

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

$parser->expression( "line", function() {

    $this->matcher( function() {

        $this ->exp( "tag-line" )
        ->or()
        ->exp( "text-line" )
        ->or()
        ->exp( "empty-line" );

    });

    $this->handler( function($node) {

        return $node;

    });

});

$parser->expression( "empty-line",  function() {

    $this->matcher( function() {

        $this ->regex( "/((?: |\t)*)/" );

    });

    $this->handler( function() {
        return null;
    });

});

$parser->expression( "tag-line",  function() {

    $this->matcher( function() {

        $this ->exp( "indentation" ) ->exp( "tag" );

    });

    $this->handler( function($indentation, $tag_node) {

        $tag_node->indentation = $indentation;

        return $tag_node;
    });

});

$parser->expression( "text-line",  function() {

    $this->matcher( function() {

        $this ->exp( "indentation" ) ->exp( "text" );

    });

    $this->handler( function($indentation, $tag_node) {

        $tag_node->indentation = $indentation;

        return $tag_node;
    });

});

/// Indentation

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

/// Tags

$parser->expression( "tag",  function() {

    $this->matcher( function() {

        $this ->regex( "([0-9a-zA-z_\-]+)" ) ->space() ->exp( "tag-attributes" )
        ->or()
        ->regex( "([0-9a-zA-z_\-]+)" );

    });

    $this->handler( function($tag_string, $attributes = []) {

        $tag_node = Create::a( Haiku_Tag::class )->with( $tag_string );

        foreach( $attributes as $each_attribute ) {
            $tag_node->set_attribute( $each_attribute[ 0 ], $each_attribute[ 1 ] );
        }

        return $tag_node;
    });

});

$parser->expression( "tag-attributes",  function() {

    $this->matcher( function() {

        $this ->str( "{ " ) ->exp( "attributes-list" ) ->str( " }" );

    });

    $this->handler( function($attributes) {

        return $attributes;

    });

});

$parser->expression( "attributes-list",  function() {

    $this->matcher( function() {

        $this ->exp( "attribute" )
        ->or()
        ->exp( "attribute" ) ->str( ", " ) ->exp( "attributes-list" );

    });

    $this->handler( function($attribute, $attribute_list = null) {

        if( $attribute_list === null ) {
            return [ $attribute ];
        }

        return array_merge( [ $attribute ], $attribute_list );

    });

});

$parser->expression( "attribute",  function() {

    $this->matcher( function() {

        $this ->m_regex( "/([0-9a-zA-z_\-]+): ([0-9a-zA-z_\-]+)(?!,|\})/" );

    });

    $this->handler( function($matches) {

        return $matches;

    });

});

/// Text

$parser->expression( "text",  function() {

    $this->matcher( function() {

        $this-> regex( "/=(.+)(?=\n)/" );

    });

    $this->handler( function($text) {

        return Create::a( Haiku_PHP_Expression::class )->with( trim( $text ) );

    });

});


/// Custom methods

$parser->def( "raise_unmatched_indentation_error",  function($spaces_count, $unit) {

    if( $this->indentation_char == "\t" ) {
        $char = "tabs";
    } else {
        $char = "spaces";
    };

    throw Create::an( UnmatchedIndentationError::class )->with(
            "The template is using indentation units of {$unit} {$char}, but a line with {$spaces_count} {$char} was found. At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}."
        );

});

$parser->def( "raise_not_unique_indentation_char_error",  function() {

    throw Create::an( NotUniqueIndentationCharError::class )->with(
            "The template is using both tabs and spaces to indent, use only tabs or only spaces. At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}."
    );

});

$parser->def( "raise_indentation_char_missmatch_error",  function($used_chars, $missmatched_chars) {

    throw Create::an( IndentationCharMissmatchError::class )->with(
            "The template is indenting with {$used_chars} in one line and {$missmatched_chars} in another one, use only tabs or only spaces in all lines. At line: {$this->context_frame->line_index} column: {$this->context_frame->column_index}."
    );

});

$parser->def( "raise_invalid_indentation_increment_error",  function() {

    $line_index = $this->context_frame->line_index - 1;

    throw Create::an( InvalidIndentationIncrementError::class )->with(
            "Invalid indentation was found. An increment of only one unit was expected. At line: {$line_index} column: {$this->context_frame->column_index}."
    );

});
