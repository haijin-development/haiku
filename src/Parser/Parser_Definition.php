<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;
use Haijin\File_Path;
use Haijin\Ordered_Collection;
use Haijin\Dictionary;

/*
 * Regex cheatsheet:
 *      group without capturing: (?:)
 *      lookahead assertion: (?=)
 *      lookahead negation: (?!)
 */

class Parser_Definition
{
    protected $expressions;

    /// Initializing

    public function __construct()
    {
        $this->expressions = Create::a( Ordered_Collection::class )->with();
        $this->expressions_by_name = Create::a( Dictionary::class )->with();
    }


    /// Accessing


    public function get_expressions()
    {
        return $this->expressions;
    }

    public function get_expression_named( $expression_name )
    {
        return $this->expressions_by_name[ $expression_name ];
    }

    public function get_expressions_in( $expressions_names )
    {
        return $expressions_names->collect( function($expression_name) {

                return $this->get_expression_named( $expression_name );

            }, $this );
    }

    /// Defining

    public function define($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $closure->call( $binding, $this );

        return $this;
    }


    public function define_in_file($file_path)
    {
        if( is_string( $file_path ) ) {
            $file_path = Create::a( File_Path::class )->with( $file_path );
        }

        return $this->define( function($parser) use($file_path) {
            require( $file_path->to_string() );
        });
    }

    /// DSL

    public function before_parsing($closure)
    {
        $this->before_parsing_closure = $closure;
    }

    public function after_parsing($closure)
    {
        $this->after_parsing_closure = $closure;
    }

    public function expression($name, $definition_closure)
    {
        $expression = Create::an( Expression::class )->with( $name );

        $definition_closure->call( $expression );

        $this->add_expression( $expression );
    }

    protected function add_expression($expression)
    {
        $this->expressions[] = $expression;
        $this->expressions_by_name[ $expression->get_name() ] = $expression;
    }
}