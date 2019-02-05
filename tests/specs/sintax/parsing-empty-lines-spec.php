<?php

use Haijin\Parser\Parser;
use Haijin\Haiku\Haiku_Parser_Definition;

$spec->describe( "When parsing empty lines", function() {

    $this->let( "parser", function() {

        return new Parser( Haiku_Parser_Definition::$definition );

    });

    $this->describe( "with just a cr", function() {

        $this->let( "input", function() {
            return
"div

p";
        });

        $this->let( "expected_html", function() {
            return
"<div /><p />";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with just a cr at the end of stream", function() {

        $this->let( "input", function() {
            return
"div
p
";
        });

        $this->let( "expected_html", function() {
            return
"<div /><p />";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with spaces", function() {

        $this->let( "input", function() {
            return
"div
    
p";
        });

        $this->let( "expected_html", function() {
            return
"<div /><p />";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with spaces at the end of the stream", function() {

        $this->let( "input", function() {
            return
"div
p
   ";
        });

        $this->let( "expected_html", function() {
            return
"<div /><p />";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with tabs", function() {

        $this->let( "input", function() {
            return
"div
\t\t\t
p";
        });

        $this->let( "expected_html", function() {
            return
"<div /><p />";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "with tabs at the end of the stream", function() {

        $this->let( "input", function() {
            return
"div
p
\t\t\t";
        });

        $this->let( "expected_html", function() {
            return
"<div /><p />";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

    $this->describe( "in between nested tags", function() {

        $this->let( "input", function() {
            return
"div

    p

        a";
        });

        $this->let( "expected_html", function() {
            return
"<div><p><a /></p></div>";
        });

        $this->it( "parses the input", function() {

            $html = $this->parser->parse_string( $this->input )->to_html();

            $this->expect( $html ) ->to() ->equal( $this->expected_html );

        });

    });

});