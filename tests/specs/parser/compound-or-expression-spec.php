<?php

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Parser\Parser_Definition;

$spec->xdescribe( "When matching a particle among several", function() {

    $this->let( "parser", function() {

        return new Parser( $this->parser_definition );

    });

    $this->let( "parser_definition", function() {

        return ( new Parser_Definition() )->define( function($parser) {

            $parser->expression( "root",  function() {

                $this->matcher( function() {

                    $this->exp( "integer" ) ->or() ->exp( "alpha" ) ->or() ->lit( "#" );

                });

                $this->handler( function($integer_or_alpha) {

                    var_dump( $integer_or_alpha );

                    return $integer_or_alpha;

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

            $parser->expression( "alpha",  function() {

                $this->matcher( function() {

                    $this->regex( "/([a-z]+)/" );

                });

                $this->handler( function($alpha_string) {

                    return $alpha_string;

                });

            });

        });

    });

    $this->describe( "when the input matches the first expression", function() {

        $this->let( "input", function() {
            return "123";
        });

        $this->it( "evaluates the handler closure", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->be( "===" ) ->than( 123 );

        });

    });

    $this->describe( "when the input matches the second expression", function() {

        $this->let( "input", function() {
            return "abc";
        });

        $this->it( "evaluates the handler closure", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->equal( "abc" );

        });

    });

    $this->describe( "when the input matches the third expression", function() {

        $this->let( "input", function() {
            return "#";
        });

        $this->xit( "evaluates the handler closure", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->be() ->null();

        });

    });

    $this->describe( "for an unexpected expression at the beginning", function() {

        $this->let( "input", function() {
            return "a+4";
        });

        $this->xit( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input );

            }) ->to() ->raise(
                \Haijin\Haiku\UnexpectedExpressionError::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to() ->equal(
                        'Unexpected expression "a+4". At line: 1 column: 1.'
                    );
            }); 

        });

    });

    $this->describe( "for an unexpected expression after an expected expression", function() {

        $this->let( "input", function() {
            return "3+a";
        });

        $this->xit( "raises an error", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input );

            }) ->to() ->raise(
                \Haijin\Haiku\UnexpectedExpressionError::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to() ->equal(
                        'Unexpected expression "a". At line: 1 column: 3.'
                    );
            }); 

        });

    });

});