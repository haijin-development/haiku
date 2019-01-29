<?php

use Haijin\Haiku\Parser\Parser;
use Haijin\Haiku\Parser\Parser_Definition;

$spec->describe( "Before parsing an input", function() {

    $this->let( "parser", function() {

        return new Parser( $this->parser_definition );

    });

    $this->let( "input", function() {
        return "1";
    });

    $this->describe( "if a before_parsing closure is defined", function() {

        $this->let( "parser_definition", function() {

            return ( new Parser_Definition() )->define( function($parser) {

                $parser->before_parsing( function() {

                    $this->before_parsing_closure_evaluated = true;

                });

                $parser->expression( "root", function() {

                    $this->matcher( function() {

                        $this ->lit( "1" );

                    });

                    $this->handler( function() {

                        return "parsed";

                    });

                });

            });

        });

        $this->it( "evaluates the closure before start to parse the input", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->equal( "parsed" );

            $this->expect( $this->parser->before_parsing_closure_evaluated ) ->to()
                ->be() ->true();

        });

    });

    $this->describe( "if no before_parsing closure is defined", function() {

        $this->let( "parser_definition", function() {

            return ( new Parser_Definition() )->define( function($parser) {

                $parser->expression( "root", function() {

                    $this->matcher( function() {

                        $this ->lit( "1" );

                    });

                    $this->handler( function() {

                        return "parsed";

                    });

                });

            });

        });

        $this->it( "does not fail", function() {

            $result = $this->parser->parse_string( $this->input );

            $this->expect( $result ) ->to() ->equal( "parsed" );

        });

    });

});