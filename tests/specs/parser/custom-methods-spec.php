<?php

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Parser\Parser_Definition;

$spec->describe( "When calling custom methods in the parser", function() {

    $this->let( "parser", function() {

        return new Parser( $this->parser_definition );

    });

    $this->let( "input", function() {
        return "1";
    });

    $this->describe( "if the method is defined", function() {

        $this->let( "parser_definition", function() {

            return ( new Parser_Definition() )->define( function($parser) {

                $parser->def( "custom", function($n, $m) {

                    return $n + $m;

                });

                $parser->expression( "root", function() {

                    $this->matcher( function() {

                        $this ->lit( "1" );

                    });

                    $this->handler( function() {

                        return $this->custom( 3, 4 );

                    });

                });

            });

        });

        $this->it( "evaluates the method and returns the result", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->equal( 7 );

        });

    });

    $this->describe( "if the method is not defined", function() {

        $this->let( "parser_definition", function() {

            return ( new Parser_Definition() )->define( function($parser) {

                $parser->expression( "root", function() {

                    $this->matcher( function() {

                        $this ->lit( "1" );

                    });

                    $this->handler( function() {

                        return $this->custom( 3, 4 );

                    });

                });

            });

        });

        $this->it( "raises a undefined custom method error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input );

            }) ->to() ->raise(
                Haijin\Haiku\UndefinedMethodError::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to() ->equal( 'The method "custom" is not defined in this parser.' );

                    $this->expect( $error->get_method_name() ) ->to()
                        ->equal( "custom" );

                    $this->expect( $error->get_parser() ) ->to()
                        ->be( "===" )->than( $this->parser );
                }
            );

        });

    });

});