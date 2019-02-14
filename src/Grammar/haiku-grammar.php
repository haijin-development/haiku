<?php

namespace Haijin\Haiku\Grammar;

use Haijin\Ordered_Collection;
use Haijin\Haiku\Dom\Haiku_Document;
use Haijin\Haiku\Dom\Haiku_Tag;
use Haijin\Haiku\Dom\Haiku_Bracked_Statement;
use Haijin\Haiku\Dom\Haiku_PHP_Expression;
use Haijin\Haiku\Dom\Haiku_PHP_Echoed_Expression;
use  Haijin\Haiku\Errors\Not_Unique_Indentation_Char_Error;
use  Haijin\Haiku\Errors\Indentation_Char_Missmatch_Error;
use  Haijin\Haiku\Errors\Unmatched_Indentation_Error;
use  Haijin\Haiku\Errors\Invalid_Indentation_Increment_Error;


$parser->before_parsing( function() {

    $this->indentation_unit = null;
    $this->indentation_char = null;

});

/// Root

$parser->expression( "root",  function($exp) {

    $exp->matcher( function($exp) {

        $exp ->lines_list();

    });

    $exp->handler( function($nodes_list) {

        $document = new Haiku_Document();

        $nodes_list->each_do( function($node) use($document) {

            $document->add_child( $node );

        });

        return $document;

    });

});


/// Lines

$parser->expression( "lines_list",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->line() ->cr() ->lines_list()
            ->or()
            ->line() ->eol();

    });

    $exp->handler( function($node, $nodes_list = null) {

        if( $node === null ) {
            return $nodes_list;
        }

        $nodes = new Ordered_Collection();

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

$parser->expression( "line", function($exp) {

    $exp->matcher( function($exp) {

        $exp ->indentation() ->opt( $exp->statement() );


    });

    $exp->handler( function($indentation, $tag_node = null) {

        if( $tag_node !== null ) {
            $tag_node->indentation = $indentation;
        }

        return $tag_node;

    });

});

$parser->expression( "statement",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->tag()
            ->or()
            ->bracked_statement()
            ->or()
            ->unescaped_text()
            ->or()
            ->escaped_text()
            ->or()
            ->php_statement();

    });

    $exp->handler( function($tag_node = null) {

        return $tag_node;

    });

});

/// Indentation

$parser->expression( "indentation",  function($exp) {

    $exp->matcher( function($exp) {

        $exp-> regex( "/((?: |\t)*)(?! |\t)/" );

    });

    $exp->handler( function($spaces) {

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

$parser->expression( "tag",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->explicit_tag()
            ->or()
            ->implicit_div();

    });

    $exp->handler( function($tag_node) {
        return $tag_node;
    });

});

$parser->expression( "explicit_tag",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->tag_name()
                ->opt( $exp->jquery_id() )
                ->opt( $exp->jquery_classes() )

                ->space()

                ->opt( $exp->tag_attributes_list() );

    });

    $exp->handler( function($tag_name, $tag_id, $tag_classes, $attributes) {

        $tag_node = new Haiku_Tag( $tag_name );

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

$parser->expression( "implicit_div",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->jquery_id()

                ->opt( $exp->jquery_classes() )

                ->space()

                ->opt( $exp->tag_attributes_list() )

            ->or()
                ->opt( $exp->jquery_id() )

                ->jquery_classes()

                ->space()

                ->opt( $exp->tag_attributes_list() );

    });

    $exp->handler( function($tag_id, $tag_classes, $attributes) {

        $tag_node = new Haiku_Tag( "div" );

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

$parser->expression( "tag_name",  function($exp) {

    $exp->matcher( function($exp) {

        $exp ->regex( "/([0-9a-zA-z]+)/" );

    });

    $exp->handler( function($tag_string) {

        return $tag_string;

    });

});

$parser->expression( "jquery_id",  function($exp) {

    $exp->matcher( function($exp) {

        $exp ->str( "#" ) ->html_name();

    });

    $exp->handler( function($id) {

        return $id;

    });

});

$parser->expression( "jquery_classes",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->jquery_class() ->jquery_classes()
            ->or()
            ->jquery_class();

    });

    $exp->handler( function($class, $classes = null) {

        if( $classes === null ) {
            return $class;
        }


        return $class . " " . $classes;

    });

});

$parser->expression( "jquery_class",  function($exp) {

    $exp->matcher( function($exp) {

        $exp ->str( "." ) ->html_name();

    });

    $exp->handler( function($class) {

        return $class;

    });

});

$parser->expression( "tag_attributes_list",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->attribute() ->space() ->str( "," ) ->blank() ->tag_attributes_list()
            ->or()
            ->attribute();

    });

    $exp->handler( function($attribute, $attribute_list = null) {

        if( $attribute_list === null ) {
            return  $attribute;
        }

        return array_merge( $attribute, $attribute_list );

    });

});

$parser->expression( "attribute",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->html_name()
            ->space() ->str( "=" ) ->space()
            ->attribute_value();

    });

    $exp->handler( function($name, $value) {

        return [ $name => $value ];

    });

});

$parser->expression( "attribute_value",  function($exp) {

    $exp->matcher( function($exp) {

        $exp ->string_literal();

    });

    $exp->handler( function($string) {

        return $string;

    });

});

/// Statements

$parser->expression( "bracked_statement",  function($exp) {

    $exp->matcher( function($exp) {

        $exp ->regex( "/-(.+) do/" ) ->space();

    });

    $exp->handler( function($text) {

        return new Haiku_Bracked_Statement( trim( $text ) );

    });

});

$parser->expression( "unescaped_text",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->regex( "/!=\s*\{\{(.+)\}\}/sU" ) ->space()
            ->or()
            ->regex( "/!=(.+)(?=\n|$)/" );

    });

    $exp->handler( function($text) {

        return new Haiku_PHP_Echoed_Expression( trim( $text ), false );

    });

});

$parser->expression( "escaped_text",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->regex( "/=\s*\{\{(.+)\}\}/sU" ) ->space()
            ->or()
            ->regex( "/=(.+)(?=\n|$)/" );

    });

    $exp->handler( function($text) {

        return new Haiku_PHP_Echoed_Expression( trim( $text ) );

    });

});

$parser->expression( "php_statement",  function($exp) {

    $exp->matcher( function($exp) {

        $exp
            ->regex( "/-\s*\{\{(.+)\}\}/sU" ) ->space()
            ->or()
            ->regex( "/-(.+)(?=\n|$)/" );

    });

    $exp->handler( function($text) {

        return new Haiku_PHP_Expression( trim( $text ) );

    });

});

/// Interpolated expressions

$parser->expression( "html_name",  function($exp) {

    $exp->processor( function() {

        $char = $this->peek_char();

        if( ! ctype_alnum( $char ) && $char != "-" && $char != "_" && $char != "{" ) {
            return false;
        }

        $char = $this->next_char();

        $literal = "";

        while( $this->not_end_of_stream() ) {
            if( $char == "{" && $this->peek_char( 1 ) == "{" ) {
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

    $exp->handler( function($tag_string) {

        return $tag_string;

    });

});

$parser->expression( "string_literal",  function($exp) {

    $exp->processor( function() {

        if( $this->peek_char() != '"' ) {
            return false;
        }

        $this->skip_chars( 1 );

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

            if( $char == "{" && $this->peek_char( 1 ) == "{" ) {
                $current_literal .= "<?php echo htmlspecialchars(";

                $char = $this->next_char();

                while( $char != "}" && $this->peek_char( 1 ) != "}" ) {
                    $char = $this->next_char();

                    $current_literal .= $char;
                }

                $current_literal .= "); ?>";

                $this->skip_chars(2);

                $char = $this->next_char();

                if( $char == '"' ) {
                    break;
                }
            }

            $current_literal .= \htmlspecialchars( $char );
        }

        $this->set_result( $current_literal );

        return true;

    });

    $exp->handler( function($string) {

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

    throw new Unmatched_Indentation_Error(
            "The template is using indentation units of {$unit} {$char}, but a line with {$spaces_count} {$char} was found. At line: {$this->current_line()} column: {$this->current_column()}."
        );

});

$parser->def( "raise_not_unique_indentation_char_error",  function() {

    throw new Not_Unique_Indentation_Char_Error(
            "The template is using both tabs and spaces to indent, use only tabs or only spaces. At line: {$this->current_line()} column: {$this->current_column()}."
    );

});

$parser->def( "raise_indentation_char_missmatch_error",  function($used_chars, $missmatched_chars) {

    throw new Indentation_Char_Missmatch_Error(
            "The template is indenting with {$used_chars} in one line and {$missmatched_chars} in another one, use only tabs or only spaces in all lines. At line: {$this->current_line()} column: {$this->current_column()}."
    );

});

$parser->def( "raise_invalid_indentation_increment_error",  function() {

    $line_index = $this->current_line() - 1;

    throw new Invalid_Indentation_Increment_Error(
            "Invalid indentation was found. An increment of only one unit was expected. At line: {$line_index} column: {$this->current_column()}."
    );

});
