<?php

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Parser\Parser_Definition;

$spec->describe( "When parsing tokens", function() {

    $this->let( "parser", function() {

        return new Parser( $this->parser_definition );

    });

    $this->let( "parser_definition", function() {

        return ( new Parser_Definition() )->define( function($parser) {

            $parser->token( "integer", "/([0-9]+)(?![0-9])/AmsU", function($integer) {

                $this->parsed_integers[] = $integer;

            });

            $parser->token( "alpha", "/([a-z]+)(?![a-z])/AmsU", function($alpha) {

                $this->parsed_alpha[] = $alpha;

            });

        });

    });

    $this->describe( "for each token found", function() {

        $this->let( "input", function() {
            return "123abc321cba";
        });

        $this->it( "evaluates the token closure", function() {

            $html = $this->parser->parse_string( $this->input );

            $this->expect( $this->parser->parsed_integers ) ->to() ->equal( [123, 321] );

            $this->expect( $this->parser->parsed_alpha ) ->to() ->equal( ["abc", "cba"] );

        });

    });

    $this->describe( "for each unexpected token", function() {

        $this->let( "input", function() {
            return "123.cba";
        });

        $this->it( "raises an error the input", function() {

            $this->expect( function() {

                $this->parser->parse_string( $this->input );

            }) ->to() ->raise(
                \Haijin\Haiku\UnexpectedExpressionError::class,
                function($error) {

                    $this->expect( $error->getMessage() ) ->to() ->equal(
                        'Unexpected expression ".cba". At line: 1 column: 4.'
                    );
            });

        });

    });

});