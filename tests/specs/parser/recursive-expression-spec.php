<?php

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Parser\Parser_Definition;

$spec->describe( "When matching recursive expressions", function() {

    $this->let( "parser", function() {

        return new Parser( $this->parser_definition );

    });

    $this->let( "parser_definition", function() {

        return ( new Parser_Definition() )->define( function($parser) {

            $parser->expression( "root",  function() {

                $this->matcher( function() {

                    $this->lit( "[" ) ->exp( "integer-list" ) ->lit( "]" );

                });

                $this->handler( function($integer) {

                    return $integer;

                });

            });

            $parser->expression( "integer-list",  function() {

                $this->matcher( function() {

                    $this->exp( "integer" ) ->lit( "," ) ->exp( "integer-list" )
                    ->or()
                    ->exp( "integer" );

                });

                $this->handler( function($integer, $list = null) {

                    if( $list == null ) {
                        return [ $integer ];
                    } else {
                        return array_merge( [ $integer ], $list );
                    }

                    var_dump( $integer );
                    var_dump( $list );
                    return $integer;

                });

            });

            $parser->expression( "integer",  function() {

                $this->matcher( function() {

                    $this->regex( "/([0-9]+)/" );

                });

                $this->handler( function($integer_string) {

                    return (int) $integer_string;

                });

            });

        });

    });

    $this->describe( "when the input matches a base expression", function() {

        $this->let( "input", function() {
            return "[123]";
        });

        $this->it( "evaluates the handler closure", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->equal( [ 123 ] );

        });

    });

    $this->describe( "when the input matches a recursive expression", function() {

        $this->let( "input", function() {
            return "[123,321]";
        });

        $this->it( "evaluates the handler closure", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->equal( [ 123, 321 ] );

        });

    });

    $this->describe( "for an unexpected expression at the beginning", function() {

        $this->let( "input", function() {
            return "123,321]";
        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input );

            }) ->to() ->raise(
                \Haijin\Haiku\UnexpectedExpressionError::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to() ->equal(
                        'Unexpected expression "123,321]". At line: 1 column: 1.'
                    );
            }); 

        });

    });

    $this->describe( "for an unexpected expression after an expected expression", function() {

        $this->let( "input", function() {
            return "[123 321]";
        });

        $this->it( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input );

            }) ->to() ->raise(
                \Haijin\Haiku\UnexpectedExpressionError::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to() ->equal(
                        'Unexpected expression " 321]". At line: 1 column: 5.'
                    );
            }); 

        });

    });

});