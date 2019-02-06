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

        return $document;

    });

});


/// Lines

$parser->expression( "lines_list",  function() {

    $this->matcher( function() {

        $this
            ->line() ->cr() ->lines_list()
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

                if( $node->indentation >= $each_node->indentation ) {
                    $nodes->add( $each_node );
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

        $this ->indentation() ->opt( $this->statement() );


    });

    $this->handler( function($indentation, $tag_node = null) {

        if( $tag_node !== null ) {
            $tag_node->indentation = $indentation;
        }

        return $tag_node;

    });

});

$parser->expression( "statement",  function() {

    $this->matcher( function() {

        $this
            ->tag()
            ->or()
            ->bracked_statement()
            ->or()
            ->unescaped_text()
            ->or()
            ->escaped_text()
            ->or()
            ->multiline_php_statement()
            ->or()
            ->one_line_php_statement();

    });

    $this->handler( function($tag_node = null) {

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

        $this
            ->explicit_tag()
            ->or()
            ->implicit_div();

    });

    $this->handler( function($tag_node) {
        return $tag_node;
    });

});

$parser->expression( "explicit_tag",  function() {

    $this->matcher( function() {

        $this
            ->tag_name()
                ->opt( $this->jquery_id() )
                ->opt( $this->jquery_classes() )

                ->space()

                ->opt( $this->tag_attributes_list() );

    });

    $this->handler( function($tag_name, $tag_id, $tag_classes, $attributes) {

        $tag_node = Create::a( Haiku_Tag::class )->with( $tag_name );

        if( $attributes === null ) {
            $attributes = [];
        }

        if( $tag_id !== null && ! isset( $attributes[ "id" ] ) ) {
            $tag_node->set_attribute( "id", $tag_id );
        }

        if( $tag_classes !== null ) {
            if( isset( $attributes[ "class" ] ) ) {
                $attributes[ "class" ] = $tag_classes . " " . $attributes[ "class" ];
            } else {
                $attributes[ "class" ] = $tag_classes;
            }
        }

        foreach( $attributes as $name => $value ) {
            $tag_node->set_attribute( $name, $value );
        }

        return $tag_node;
    });

});

$parser->expression( "implicit_div",  function() {

    $this->matcher( function() {

        $this
            ->jquery_id()

                ->opt( $this->jquery_classes() )

                ->space()

                ->opt( $this->tag_attributes_list() )

            ->or()
                ->opt( $this->jquery_id() )

                ->jquery_classes()

                ->space()

                ->opt( $this->tag_attributes_list() );

    });

    $this->handler( function($tag_id, $tag_classes, $attributes) {

        $tag_node = Create::a( Haiku_Tag::class )->with( "div" );

        if( $attributes === null ) {
            $attributes = [];
        }

        if( $tag_id !== null && ! isset( $attributes[ "id" ] ) ) {
            $tag_node->set_attribute( "id", $tag_id );
        }

        if( $tag_classes !== null ) {
            if( isset( $attributes[ "class" ] ) ) {
                $attributes[ "class" ] = $tag_classes . " " . $attributes[ "class" ];
            } else {
                $attributes[ "class" ] = $tag_classes;
            }
        }

        foreach( $attributes as $name => $value ) {
            $tag_node->set_attribute( $name, $value );
        }

        return $tag_node;
    });

});

$parser->expression( "tag_name",  function() {

    $this->matcher( function() {

        $this ->regex( "/([0-9a-zA-z]+)/" );

    });

    $this->handler( function($tag_string) {

        return $tag_string;

    });

});

$parser->expression( "jquery_id",  function() {

    $this->matcher( function() {

        $this ->str( "#" ) ->html_name();

    });

    $this->handler( function($id) {

        return $id;

    });

});

$parser->expression( "jquery_classes",  function() {

    $this->matcher( function() {

        $this
            ->jquery_class() ->jquery_classes()
            ->or()
            ->jquery_class();

    });

    $this->handler( function($class, $classes = null) {

        if( $classes === null ) {
            return $class;
        }


        return $class . " " . $classes;

    });

});

$parser->expression( "jquery_class",  function() {

    $this->matcher( function() {

        $this ->str( "." ) ->html_name();

    });

    $this->handler( function($class) {

        return $class;

    });

});

$parser->expression( "tag_attributes_list",  function() {

    $this->matcher( function() {

        $this
            ->attribute() ->space() ->str( "," ) ->blank() ->tag_attributes_list()
            ->or()
            ->attribute();

    });

    $this->handler( function($attribute, $attribute_list = null) {

        if( $attribute_list === null ) {
            return  $attribute;
        }

        return array_merge( $attribute, $attribute_list );

    });

});

$parser->expression( "attribute",  function() {

    $this->matcher( function() {

        $this
            ->html_name()
            ->space() ->str( "=" ) ->space()
            ->attribute_value();

    });

    $this->handler( function($name, $value) {

        return [ $name => $value ];

    });

});

$parser->expression( "attribute_value",  function() {

    $this->matcher( function() {

        $this ->string_literal();

    });

    $this->handler( function($literals) {

        return $literals[ 0 ];

    });

});

/// Statements

$parser->expression( "bracked_statement",  function() {

    $this->matcher( function() {

        $this ->regex( "/-(.+) do/" ) ->space();

    });

    $this->handler( function($text) {

        return Create::a( Haiku_Bracked_Statement::class )->with( trim( $text ) );

    });

});

$parser->expression( "unescaped_text",  function() {

    $this->matcher( function() {

        $this ->regex( "/!=(.+)(?=\n)/" );

    });

    $this->handler( function($text) {

        return Create::a( Haiku_PHP_Echoed_Expression::class )->with( trim( $text ), false );

    });

});

$parser->expression( "escaped_text",  function() {

    $this->matcher( function() {

        $this ->regex( "/=(.+)(?=\n)/" );

    });

    $this->handler( function($text) {

        return Create::a( Haiku_PHP_Echoed_Expression::class )->with( trim( $text ) );

    });

});

$parser->expression( "multiline_php_statement",  function() {

    $this->matcher( function() {

        $this ->str( "-" ) ->space() ->regex( "/\(\{(.+)\}\)/s" ) ->space();

    });

    $this->handler( function($text) {

        return Create::a( Haiku_PHP_Expression::class )->with( trim( $text ), true );

    });

});

$parser->expression( "one_line_php_statement",  function() {

    $this->matcher( function() {

        $this ->str( "-" ) ->regex( "/(.+)(?=\n)/" );

    });

    $this->handler( function($text) {

        return Create::a( Haiku_PHP_Expression::class )->with( trim( $text ) );

    });

});

/// Interpolated expressions

$parser->expression( "html_name",  function() {

    $this->processor( function() {

        $char = $this->peek_char();

        if( ! ctype_alnum( $char ) && $char != "-" && $char != "_" && $char != "(" ) {
            return false;
        }

        $char = $this->next_char();

        $literal = "";

        while( $this->not_end_of_stream() ) {
            if( $char == "(" && $this->peek_char( 1 ) == "{" ) {
                $literal .= "<?php echo htmlspecialchars(";

                $char = $this->next_char();

                while( $char != "}" && $this->peek_char( 1 ) != "}" ) {
                    $char = $this->next_char();

                    $literal .= $char;
                }

                $literal .= "); ?>";

                $this->skip_chars(2);

                $char = $this->next_char();
            }

            if( ! ctype_alnum( $char ) && $char != "-" && $char != "_" ) {
                $this->skip_chars( -1 );
                break;
            }

            $literal .= \htmlspecialchars( $char );

            $char = $this->next_char();
        }

        $this->set_result( $literal );

        return true;

    });

    $this->handler( function($tag_string) {

        return $tag_string;

    });

});

$parser->expression( "string_literal",  function() {

    $this->processor( function() {

        if( $this->peek_char() != '"' ) {
            return false;
        }

        $this->skip_chars( 1 );

        $literals = [];
        $current_literal = "";

        $scaping_next = false;

        while( $this->not_end_of_stream() ) {

            $char = $this->next_char();

            if( $scaping_next === true ) {
                $current_literal .= \htmlspecialchars( $char );

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

            if( $char == "(" && $this->peek_char( 1 ) == "{" ) {
                $current_literal .= "<?php echo htmlspecialchars(";

                $char = $this->next_char();

                while( $char != ")" && $this->peek_char( 1 ) != "}" ) {
                    $char = $this->next_char();

                    $current_literal .= $char;
                }

                $current_literal .= "); ?>";

                $this->skip_chars(2);

                $char = $this->next_char();
            }

            $current_literal .= \htmlspecialchars( $char );
        }

        $literals[] = $current_literal;

        $this->set_result( $literals );

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
