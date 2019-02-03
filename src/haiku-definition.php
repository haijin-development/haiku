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

        $this->lines_list();

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

$parser->expression( "lines_list",  function() {

    $this->matcher( function() {

        $this ->line() ->cr() ->lines_list()
        ->or()
        ->line() ->eol();

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

        $this ->tag_line()
        ->or()
        ->text_line()
        ->or()
        ->empty_line();

    });

    $this->handler( function($node) {

        return $node;

    });

});

$parser->expression( "empty_line",  function() {

    $this->matcher( function() {

        $this ->space();

    });

    $this->handler( function() {
        return null;
    });

});

$parser->expression( "tag_line",  function() {

    $this->matcher( function() {

        $this ->indentation() ->tag();

    });

    $this->handler( function($indentation, $tag_node) {

        $tag_node->indentation = $indentation;

        return $tag_node;
    });

});

$parser->expression( "text_line",  function() {

    $this->matcher( function() {

        $this ->indentation() ->text();

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

        $this ->tag_name() ->str( " " ) ->space() ->tag_attributes_list()
        ->or()
        ->tag_name();

    });

    $this->handler( function($tag_string, $attributes = []) {

        $tag_node = Create::a( Haiku_Tag::class )->with( $tag_string );

        foreach( $attributes as $each_attribute ) {
            $tag_node->set_attribute( $each_attribute[ 0 ], $each_attribute[ 1 ] );
        }

        return $tag_node;
    });

});

$parser->expression( "tag_name",  function() {

    $this->matcher( function() {

        $this ->regex( "/([0-9a-zA-z_\-]+)/" );

    });

    $this->handler( function($tag_string) {

        return $tag_string;

    });

});

$parser->expression( "tag_attributes_list",  function() {

    $this->matcher( function() {

        $this ->attribute() ->space() ->str( "," ) ->blank() ->tag_attributes_list()
        ->or()
        ->attribute();

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

        $this ->attribute_name()
        ->space() ->str( "=" ) ->space()
        ->attribute_value();

    });

    $this->handler( function($name, $value) {

        return [ $name, $value ];

    });

});

$parser->expression( "attribute_name",  function() {

    $this->matcher( function() {

        $this ->regex( "/([0-9a-zA-z_\-]+)/" );

    });

    $this->handler( function($name) {

        return $name;

    });

});

$parser->expression( "attribute_value",  function() {

    $this->matcher( function() {

        $this ->string_literal();

    });

    $this->handler( function($value) {

        return $value;

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

/// Literals

$parser->expression( "string_literal",  function() {

    $this->processor( function() {

        $char = $this->next_char();

        if( $char != '"' ) {
            return false;
        }

        $literal = "";
        $scaping_next = false;

        while( $this->not_end_of_stream() ) {

            $char = $this->next_char();

            if( $scaping_next === true ) {
                $literal .= $char;

                $scaping_next = false;
                continue;
            }

            if( $char == '\\' ) {
                $scaping_next = true;
                continue;
            }

            if( $char == '"' ) {
                break;
            }

            $literal .= $char;
        }

        $this->set_result( $literal );

        return true;

    });

    $this->handler( function($string) {

        return $string;

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
            "The template is using indentation units of {$unit} {$char}, but a line with {$spaces_count} {$char} was found. At line: {$this->current_line()} column: {$this->current_column()}."
        );

});

$parser->def( "raise_not_unique_indentation_char_error",  function() {

    throw Create::an( NotUniqueIndentationCharError::class )->with(
            "The template is using both tabs and spaces to indent, use only tabs or only spaces. At line: {$this->current_line()} column: {$this->current_column()}."
    );

});

$parser->def( "raise_indentation_char_missmatch_error",  function($used_chars, $missmatched_chars) {

    throw Create::an( IndentationCharMissmatchError::class )->with(
            "The template is indenting with {$used_chars} in one line and {$missmatched_chars} in another one, use only tabs or only spaces in all lines. At line: {$this->current_line()} column: {$this->current_column()}."
    );

});

$parser->def( "raise_invalid_indentation_increment_error",  function() {

    $line_index = $this->current_line() - 1;

    throw Create::an( InvalidIndentationIncrementError::class )->with(
            "Invalid indentation was found. An increment of only one unit was expected. At line: {$line_index} column: {$this->current_column()}."
    );

});
