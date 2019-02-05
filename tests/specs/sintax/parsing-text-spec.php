<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing text", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "escaped text with = and no ending semicolon", function() {

        $this->let( "input", function() {
            return
"div
    = '123'
";
        });

        $this->let( "expected_html", function() {
            return
"<div><?php echo htmlspecialchars( '123' ); ?></div>";
        });

        $this->it( "generates the escaped text", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "escaped text with = and ending semicolon", function() {

        $this->let( "input", function() {
            return
"div
    = '123';
";
        });

        $this->let( "expected_html", function() {
            return
"<div><?php echo htmlspecialchars( '123' ); ?></div>";
        });

        $this->it( "generates the escaped text", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });


    $this->describe( "unescaped text with != and no ending semicolon", function() {

        $this->let( "input", function() {
            return
"div
    != '123'
";
        });

        $this->let( "expected_html", function() {
            return
"<div><?php echo '123'; ?></div>";
        });

        $this->it( "generates the unescaped text", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "unescaped text with == and ending semicolon", function() {

        $this->let( "input", function() {
            return
"div
    != '123';
";
        });

        $this->let( "expected_html", function() {
            return
"<div><?php echo '123'; ?></div>";
        });

        $this->it( "generates the unescaped text", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });


});