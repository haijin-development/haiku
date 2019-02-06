<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "a one liner PHP statement", function() {

        $this->describe( "with an ending semicolon", function() {

            $this->let( "input", function() {
                return
'div
    - $variable = "123";
';
            });

            $this->let( "expected_html", function() {
                return
'<div><?php $variable = "123"; ?></div>';
            });


            $this->it( "generates the PHP statement", function() {

                $html = $this->parser->parse_string( $this->input )->to_html();

                $this->expect( $html ) ->to() ->equal( $this->expected_html );

            });

        });

        $this->describe( "with no ending semicolon", function() {

            $this->let( "input", function() {
                return
'div
    - $variable = "123"
';
            });

            $this->let( "expected_html", function() {
                return
'<div><?php $variable = "123"; ?></div>';
            });


            $this->it( "generates the PHP statement", function() {

                $html = $this->parser->parse_string( $this->input )->to_html();

                $this->expect( $html ) ->to() ->equal( $this->expected_html );

            });

        });

    });

    $this->describe( "a one liner PHP statement", function() {

        $this->describe( "with no ending cr", function() {

            $this->let( "input", function() {
                return
'div
    - $variable = "123";';
            });

            $this->let( "expected_html", function() {
                return
'<div><?php $variable = "123"; ?></div>';
            });


            $this->it( "generates the PHP statement", function() {

                $html = $this->parser->parse_string( $this->input )->to_html();

                $this->expect( $html ) ->to() ->equal( $this->expected_html );

            });

        });

    });

    $this->describe( "a mutilines PHP statements", function() {

        $this->describe( "with an ending semicolon", function() {

            $this->let( "input", function() {
                return
'div
    - ({
        $variable = 1;
        $variable += 10;
    })
';
            });

            $this->let( "expected_html", function() {
                return
'<div><?php $variable = 1;
        $variable += 10; ?></div>';
            });


            $this->it( "generates the PHP statement", function() {

                $html = $this->parser->parse_string( $this->input )->to_html();

                $this->expect( $html ) ->to() ->equal( $this->expected_html );

            });

        });

    });

    $this->describe( "a mutilines PHP statements", function() {

        $this->describe( "with no ending cr", function() {

            $this->let( "input", function() {
                return
'div
    - ({
        $variable = 1;
        $variable += 10;
    })';
            });

            $this->let( "expected_html", function() {
                return
'<div><?php $variable = 1;
        $variable += 10; ?></div>';
            });


            $this->it( "generates the PHP statement", function() {

                $html = $this->parser->parse_string( $this->input )->to_html();

                $this->expect( $html ) ->to() ->equal( $this->expected_html );

            });

        });

    });

});