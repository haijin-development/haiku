<?php

namespace Haijin\Haiku\Parser;

use Haijin\Instantiator\Create;
use Haijin\File_Path;
use Haijin\Ordered_Collection;

/*
 * Regex cheatsheet:
 *      group without capturing: (?:)
 *      lookahead assertion: (?=)
 *      lookahead negation: (?!)
 */

class Parser_Definition
{
    protected $before_parsing_closure;
    protected $after_parsing_closure;
    protected $tokens;

    /// Initializing

    public function __construct()
    {
        $this->before_parsing_closure = null;
        $this->after_parsing_closure = null;

        $this->tokens = Create::a( Ordered_Collection::class )->with();
    }


    /// Accessing

    public function get_before_parsing_closure()
    {
        return $this->before_parsing_closure;
    }

    public function get_after_parsing_closure()
    {
        return $this->after_parsing_closure;
    }

    public function get_tokens()
    {
        return $this->tokens;
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

    public function token($name, $pattern, $closure)
    {
        $this->tokens[ $name ] = Create::a( Parser_Token::class )
                                    ->with( $name, $pattern, $closure );
    }
}